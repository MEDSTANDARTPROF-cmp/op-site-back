<?php

class ieGalleryMs2ImagesImportWorker extends MsIeImportWorker
{
    /** @var string $classKey */
    protected $classKey = 'msProduct';
    /** @var bool $isRemoveLinks */
    protected $isRemoveImages = false;
    /** @var string $resourceKey */
    protected $resourceKey = 'product_id';
    /** @var string $galleryType */
    protected $galleryType = 'minishop2';
    /** @var bool $isOldVersion */
    protected $isOldVersion = false;
    protected $allowedImages = [];

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->stats = array('errors' => 0, 'created' => 0, 'updated' => 0);
            $this->processorsPath = MODX_CORE_PATH . 'components/iegallery/processors/';
            $this->isRemoveImages = $this->getSetting('gallery_remove_images', false);
            $this->addPrepareFieldMethod('gallery', $this, 'prepareFieldGallery');
            $this->isOldVersion = $this->isOldVersionMs2();
            $allowedImages = $this->modx->getOption('upload_images');
            $this->allowedImages = $this->tools->explodeAndClean($allowedImages);
        }
        return $initialized;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        if ($result = parent::prepareData($data)) {
            if (empty($result['gallery'])) {
                $this->incrStatsRecord('errors');
                $err = $this->modx->lexicon('iegallery_err_data_file_ns');
                $this->tools->log($err);
                return array();
            }
            $settings = $this->getSettings();
            $checkingField = $this->getSetting('checking_field', '');
            if (!$resource = $this->findResource($checkingField, $result[$checkingField], $this->classKey, $settings)) {
                $this->incrStatsRecord('errors');
                $err = $this->modx->lexicon('msimportexport_import_err_resource_nf', array('key' => $checkingField, 'value' => $result[$checkingField]));
                $this->tools->log($err);
                return array();
            }
            $result['id'] = $resource->get('id');
        }
        return $result;
    }

    /**
     * @param array $data
     */
    public function work(array $data = array())
    {

        if (empty($data)) return;
        $this->action = 'create';
        $response = $this->fireEvent('msieOnBeforeImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data));
        if (!is_array($response)) {
            if ($response === false) {
                $this->incrStatsRecord('errors');
            }
            return;
        }

        $data = $response['data'];
        if ($this->debug) {
            $record = print_r($this->getReadRecord(), 1);
            $this->debug("Import before run processor.\n\naction: {$this->action}\nfile record: {$record}\nparams: " . print_r($data, 1));
        }
        if (
            $this->isRemoveImages &&
            !$this->storage->hasKeyInStore('ids', $data['id'])
        ) {
            if ($this->debug) {
                $this->debug('Remove all images' . print_r($data, 1));
            }
            $data[$this->resourceKey] = $data['id'];
            $this->removeImages($data);
        }

        $files = $data['gallery'];
        $data['resize_upload_image'] = $this->getSetting('gallery_resize_upload_image', false);
        foreach ($files as $file) {
            $params = array_merge($data, array('file' => $file));
            /** @var modProcessorResponse $response */
            $this->modx->error->reset();
            $response = $this->runProcessor("mgr/gallery/{$this->galleryType}/upload", $params);
            $object = $response->getObject();

            if ($this->debug) {
                $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
            }
            if ($response->isError()) {
                if (isset($object['code']) && $object['code'] == 304) {
                    $this->action = 'update';
                    $params['id'] = $object['file_id'];
                    $params['file'] = $object['file'];
                    $params['name'] = $object['name'];
                } else {
                    $err = $this->modx->lexicon('msimportexport_import_err_import',
                        array(
                            'action' => $this->action,
                            'message' => $response->getMessage(),
                            'info' => print_r($data, 1) . "\n" . print_r($response->getAllErrors(), 1)
                        )
                    );

                    $this->tools->log($err);
                    $this->incrStatsRecord('errors');
                    continue;
                }
            }

            if (
                $this->action == 'update' &&
                $this->getSetting('gallery_force_update', false)
            ) {
                unset($params['url']);
                if (count($params) <= 3) continue;
                $this->modx->error->reset();
                $response = $this->runProcessor("mgr/gallery/{$this->galleryType}/update", $params);
                if ($response->isError()) {
                    $err = $this->modx->lexicon('msimportexport_import_err_import',
                        array(
                            'action' => $this->action,
                            'message' => $response->getMessage(),
                            'info' => print_r($data, 1) . "\n" . print_r($response->getAllErrors(), 1)
                        )
                    );

                    $this->tools->log($err);
                    $this->incrStatsRecord('errors');
                    continue;
                }
                $object = $response->getObject();
                if ($this->debug) {
                    $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
                }
            }
            $this->incrStatsRecord($this->action . 'd');
            $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $params, 'object' => $object));
        }
        $this->storage->pushStore('ids', $data['id'], $data['id']);
    }


    /**
     * @param string $field
     * @param mixed $val
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldGallery($field, $val, array $data, array $result, MsIeWorker &$worker)
    {
        if (!empty($val)) {
            $delimiter = $this->getSetting('gallery_image_delimiter', '');
            $files = $this->tools->explodeAndClean($val, $delimiter);
            if ($files) {
                $basePath = $this->getSetting('gallery_base_path_images', MODX_BASE_PATH);
                $basePath = $this->tools->preparePath($basePath);
                foreach ($files as $key => &$file) {
                    if ($this->tools->isUrl($file)) {
                        $downloadFile = $this->tools->download($file);
                        if ($downloadFile) {
                            $extension = pathinfo($downloadFile, PATHINFO_EXTENSION);
                            if (!$extension || !in_array($extension, $this->allowedImages)) {
                                $err = $this->modx->lexicon('iegallery_err_invalid_download_image_extension', [
                                    'extension' => $extension,
                                    'url' => $file,
                                    'file' => $downloadFile,
                                    'data' => print_r($data, 1),
                                ]);
                                $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                                unset($files[$key]);
                            } else {
                                $file = $downloadFile;
                            }
                        } else {
                            unset($files[$key]);
                        }
                    } else {
                        if (strpos($file, './') !== false) {
                            $file = $this->getWorkingDirectory() . $file;
                        } else {
                            $file = $basePath . DIRECTORY_SEPARATOR . $file;
                        }
                        $file = $this->tools->normalizePath($file);
                        if (!file_exists($file)) {
                            $err = $this->modx->lexicon('msimportexport_system_err_nf_file', array('file' => $file));
                            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                            unset($files[$key]);
                        }
                    }
                }
                if (!isset($result[$field])) $result[$field] = array();
                $result[$field] = array_merge($result[$field], $files);
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function removeImages(array $data)
    {
        $this->modx->error->reset();
        if ($this->isOldVersion) {
            $response = $this->runProcessor("mgr/gallery/{$this->galleryType}/removeallold", $data);
        } else {
            $response = $this->runProcessor("mgr/gallery/{$this->galleryType}/removeall", $data);
        }

        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response->getAllErrors(), 1));
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function isOldVersionMs2()
    {
        if ($ms2 = $this->tools->getMs2Instance()) {
            $tmp = explode('.', $ms2->version);
            return $tmp[0] <= 2;
        }
        return false;
    }
}