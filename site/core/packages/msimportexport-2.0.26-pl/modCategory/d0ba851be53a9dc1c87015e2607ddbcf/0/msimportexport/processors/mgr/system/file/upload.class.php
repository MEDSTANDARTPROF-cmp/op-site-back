<?php
require_once(dirname(__FILE__) . '/getfields.class.php');

class msImportExportFileUploadProcessor extends msImportExportFileGetFieldsProcessor
{

    public function process()
    {
        $preset = $this->getProperty('preset', 0);
        $parse = $this->getProperty('parse', false);
        $file = !empty($_FILES['file']) ? $_FILES['file']['tmp_name'] : $this->getProperty('file');
        if ($preset) {
            if (!$this->preset = $this->modx->getObject('MsiePreset', $preset)) {
                $err = 'Error get preset for ID ' . $preset;
                $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                return $this->failure($err);
            }
        }
        if (!$this->service = $this->msie->getService($this->preset->get('mode'), $this->preset->get('service'))) {
            $err = $this->modx->lexicon('msimportexport_service_err_nf', array('service' => $this->preset->get('service')));
            return $this->failure($err);
        }


        $settings = $this->preset->get('settings');
        $masks = $this->service->getAllowedFileExtensions();
        $masks[] = 'zip';
        $removeSourceFile = $this->modx->getOption('remove_source_file', $settings, 0);

        if (!$this->msie->getTools()->isUrl($file) && !file_exists($file)) {
            $err = $this->modx->lexicon('msimportexport_system_err_nf_file', array('file' => $file));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            return $this->failure($err);
        }

        $uploadedFile = $this->msie->getTools()->upload($file, array('mkdir' => true, 'remove_source_file' => $removeSourceFile));


        if (!$uploadedFile) {
            $err = $this->modx->lexicon('msimportexport_system_err_upload_file', array('file' => $file));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            return $this->failure($err);
        }
        $file = $uploadedFile;

        $path = dirname($file) . DIRECTORY_SEPARATOR;
        $filename = $this->msie->getTools()->basename($file);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if ($ext == 'zip') {
            $unpack = $this->msie->getTools()->unzip($file, $path);
            unlink($file);

            if ($unpack === false) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error unzip file:' . $file . ' to:' . $path);
                $this->modx->cacheManager->deleteTree($path, array('deleteTop' => true, 'extensions' => ''));
                return $this->failure($this->modx->lexicon('msimportexport_system_err_unzip_file'));
            }

            if ($dirs = glob("$path*", GLOB_ONLYDIR)) {
                foreach ($dirs as $dir) {
                    if (basename($dir) == '__MACOSX') {
                        continue;
                    }
                    $path = $dir . DIRECTORY_SEPARATOR;
                    break;
                }
            }

            $options = array('masks' => $masks, 'maxDepth' => 1, 'sorted' => true);
            $filename = '';
            if ($files = $this->msie->getTools()->findFiles($path, $options)) {
                $filename = $this->msie->getTools()->basename($files[0]);
                if ($settings['convert_encoding']) {
                    foreach ($files as $file) {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if ($ext == 'csv') {
                            try {
                                if (!$this->msie->getTools()->convertFileToUTF8($file, $settings['source_encode'])) {
                                    $err = sprintf($this->modx->lexicon('msimportexport_system_err_convert_file'), $file, 'UTF-8');
                                    $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                                    return $this->failure($err);
                                }
                            } catch (Exception $e) {
                                $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
                                return $this->failure($e->getMessage());
                            }
                        }
                    }
                }
            }
        } else if (!empty($masks) && !in_array($ext, $masks)) {
            $filename = '';
        }

        if (empty($filename)) {
            $err = sprintf($this->modx->lexicon('msimportexport_system_err_ext_file'), implode(';', $masks));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            $this->modx->cacheManager->deleteTree($path, array('deleteTop' => true, 'extensions' => ''));
            return $this->failure($err);
        }

        $data = array(
            'path' => $path,
            'filename' => $filename,
            'fields' => $parse ? $this->parseFileFields($path . $filename) : array(),
            'presetFields' => $parse ? $this->preset->get('fields') : array(),
        );

        if ($parse) {
            $this->modx->cacheManager->deleteTree($path, array('deleteTop' => true, 'extensions' => ''));
        }

        return $this->success('', $data);
    }
}

return 'msImportExportFileUploadProcessor';
