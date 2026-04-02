<?php

class MsIeDownloader
{
    /** @var modX $modx */
    public $modx;
    /** @var Msie $msie */
    public $msie;
    /** @var MsiePreset $preset */
    protected $preset = null;
    /** @var array $config */
    protected $config = array();
    /** @var array $stopStatus */
    protected $stopStatus = array();
    /** @var string $key */
    protected $key;
    /** @var string $token */
    protected $token;

    public function __construct(Msie &$msie, $config = array())
    {
        $this->msie = &$msie;
        $this->modx = &$msie->modx;
        $this->config = array_merge(array(
            'sleepTime' => 2,
            'scriptTimeLimit' => 800,
            'reconnectTimeout' => 800,
        ), $config);
        $this->modx->loadClass('MsieTask');
        $this->stopStatus = array(
            MsieTask::STATUS_FAILED,
            MsieTask::STATUS_KILLED,
            MsieTask::STATUS_STOPPED
        );
    }

    /**
     * @param string $msg
     * @param mixed $object
     * @return array|string
     */
    protected function success($msg = '', $object = null)
    {
        return $this->modx->error->success($msg, $object);
    }

    /**
     * @param string $msg
     * @param mixed $object
     * @return array|string
     */
    protected function failure($msg = '', $object = null)
    {
        return $this->modx->error->failure($msg, $object);
    }

    /**
     * @return boolean
     */
    protected function hasErrors()
    {
        return $this->modx->error->hasError();
    }

    /**
     * @return array
     */
    protected function getErrors()
    {
        return $this->modx->error->getErrors();
    }

    /**
     * @param string $key
     * @param bool $reg
     * @return bool|string
     */
    protected function checkAccessDownload($key, $reg = true)
    {
        $ok = true;
        if ($lockTtl = $this->preset->getSetting('download_lock_ttl', 0)) {
            if ($this->getAccessDownload($key)) {
                $ok = $this->modx->lexicon('msimportexport_do_err_download_lock', array('sec' => $lockTtl));
            } else {
                if ($reg) {
                    $this->regAccessDownload($key, $lockTtl);
                }
            }
        }
        return $ok;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function getAccessDownload($key)
    {
        $ip = $this->msie->getTools()->getClientIp();
        $ip = ip2long($ip['ip']);
        $this->modx->getService('registry', 'registry.modRegistry');
        $this->modx->registry->addRegister('msieAccessDownload', 'registry.modDbRegister', array('directory' => 'msieAccessDownload'));
        if (!$this->modx->registry->msieAccessDownload->connect()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[checkAccessDownload] Error connect to registry "msieAccessDownload"');
            return null;
        }
        $this->modx->registry->msieAccessDownload->subscribe("/{$ip}/{$key}");
        return $this->modx->registry->msieAccessDownload->read(array('remove_read' => false, 'poll_limit' => 1));
    }


    /**
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    protected function regAccessDownload($key, $ttl = 30)
    {
        $ip = $this->msie->getTools()->getClientIp();
        $ip = ip2long($ip['ip']);
        $this->modx->getService('registry', 'registry.modRegistry');
        $this->modx->registry->addRegister('msieAccessDownload', 'registry.modDbRegister', array('directory' => 'msieAccessDownload'));
        if (!$this->modx->registry->msieAccessDownload->connect()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[regAccessDownload] Error connect to registry "msieAccessDownload"');
            return false;
        }
        $topic = "/{$ip}/";
        $this->modx->registry->msieAccessDownload->subscribe($topic);
        $this->modx->registry->msieAccessDownload->send($topic, array($key => time()), array(
            'ttl' => $ttl,
        ));
        //$this->modx->registry->msieAccessDownload->unsubscribe($topic);
        return true;
    }

    /**
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false;
    }

    /**
     * @param string $token
     * @return array
     */
    protected function getTaskData($token)
    {
        $result = array();
        /** @var MsieTask $task */
        $task = $this->modx->getObject('MsieTask', array('token' => $token));
        if ($task) {
            $report = $task->getReport(true);
            $result = array(
                'status' => $task->get('status'),
                'file' => $this->modx->getOption('file', $report),
                'total' => $this->modx->getOption('total', $report, 0),
                'iteration' => $this->modx->getOption('iteration', $report, 0),
            );
        } else {
            $err = $this->modx->lexicon('msimportexport_do_err_task_nf', array('token' => $token));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
        }
        return $result;
    }

    /**
     * @param string $token
     * @return array
     */
    protected function status($token)
    {
        if ($data = $this->getTaskData($token)) {
            unset($data['file']);
            $result = $this->success('', $data);
        } else {
            $result = $this->failure('Error!');
        }
        return $result;
    }

    /**
     * @param string $token
     * @return array
     */
    protected function download($token)
    {
        $data = $this->getTaskData($token);
        if ($data) {
            if ($data['status'] === MsieTask::STATUS_COMPLETED) {
                if ($this->msie->getTools()->browserDownload($data['file'])) {
                    return $this->success();
                } else {
                    $err = $this->modx->lexicon('msimportexport_do_err_file_nf', array('token' => $token));
                    $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                    http_response_code(404);
                    exit();
                }
            } else {
                $err = $this->modx->lexicon('msimportexport_do_err_task_nc', array('token' => $token));
                $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            }
        }
        return $this->failure('Error!');
    }

    protected function run()
    {
        $sing = '';
        $properties = array();
        if ($this->isAjaxRequest()) {
            $properties = @$_SESSION[$this->msie->sessionDownloadKey][$this->key];
            if (is_array($properties)) {
                $sing = $this->modx->getOption('sing', $properties, '', true);
                unset($properties['sing']);
                if ($sing && $sing === $this->msie->getTools()->generateSign($properties)) {
                    $presetId = $this->modx->getOption('preset', $properties, 0, true);
                    $this->preset = $this->modx->getObject('MsiePreset', $presetId);
                } else {
                    $err = $this->modx->lexicon('msimportexport_do_err_sing', array('sing' => $sing));
                    $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                    return $this->failure('Error!');
                }
            }
        } else if ($this->token) {
            $this->preset = $this->modx->getObject('MsiePreset', array('token' => $this->token));
        }

        if ($this->preset) {
            if ($sing) {
                $access = $this->checkAccessDownload($sing);
                if ($access !== true) {
                    return $this->failure($access);
                }
            }
            $options = $this->modx->getOption('options', $properties, array(), true);
            if ($options) {
                $options = $this->msie->getTools()->fromJSON($options, array());
            }
            $options['properties'] = array('task_trigger' => 'frontend');
            if ($task = $this->msie->getTaskManager()->add($this->preset->get('id'), $options)) {
                $url = $this->msie->getDoUrl();
                $data = array(
                    'token' => $task->get('token'),
                    'delay' => $task->getSetting('task_refresh_freq', 2),
                );
                if ($this->isAjaxRequest()) {
                    return $this->success('', $data);
                } else {
                    @set_time_limit($this->config['scriptTimeLimit']);
                    MsIeTools::setSessionWaitTimeout($this->modx, $this->config['reconnectTimeout']);
                    header('HTTP/1.1 102 Processing');
                    $loop = true;
                    while ($loop) {
                        sleep($this->config['sleepTime']);
                        if ($taskData = $this->getTaskData($data['token'])) {
                            if ($taskData['status'] === MsieTask::STATUS_COMPLETED) {
                                $url .= '?act=download&token=' . $data['token'];
                                header("Location: {$url}", true, 302);
                                exit();
                            } else if (in_array($taskData['status'], $this->stopStatus)) {
                                $loop = false;
                            }
                        } else {
                            $loop = false;
                        }
                    }
                }
            } else {
                $err = $this->modx->lexicon('msimportexport_do_err_add_task', array('token' => $this->token, 'preset' => $this->preset->get('id')));
                $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            }
        } else {
            $err = $this->modx->lexicon('msimportexport_do_err_preset_nf', array('token' => $this->token, 'key' => $this->key));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
        }
        return $this->failure('Error!');

    }

    public function execute()
    {
        $action = $this->modx->getOption('act', $_GET, '', true);
        $this->key = $this->modx->getOption('key', $_POST, '', true);
        $this->token = $this->modx->getOption('token', $_GET, '', true);
        $this->modx->error->reset();
        switch ($action) {
            case 'download':
                $result = $this->download($this->token);
                break;
            case 'status':
                $result = $this->status($this->token);
                break;
            default:
                $result = $this->run();
        }

        if ($this->isAjaxRequest()) {
            echo $this->modx->toJSON($result);
        } else {
            $code = empty($result['success']) ? 400 : 200;
            http_response_code($code);
        }
    }

}
