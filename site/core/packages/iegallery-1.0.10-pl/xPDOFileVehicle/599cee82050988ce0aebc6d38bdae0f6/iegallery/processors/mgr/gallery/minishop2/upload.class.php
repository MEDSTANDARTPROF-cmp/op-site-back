<?php
include_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/gallery/upload.class.php';

class IeGalleryMs2ImagesUploadProcessor extends msProductFileUploadProcessor
{
    public $permission = '';
    /** @var msProduct $product */
    private $product = 0;

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        $id = $this->getProperty('id', 0);
        if (!$id || !$this->product = $this->modx->getObject('msProduct', $id)) {
            return $this->modx->lexicon('ms2_gallery_err_no_product');
        }
        if (!$this->mediaSource = $this->product->initializeMediaSource()) {
            return $this->modx->lexicon('ms2_gallery_err_no_source');
        }
        $this->miniShop2 = $this->modx->getService('miniShop2');

        return true;
    }


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$data = $this->handleFile()) {
            return $this->failure($this->modx->lexicon('ms2_err_gallery_ns'));
        }

        $properties = $this->mediaSource->getPropertyList();
        $pathinfo = $this->miniShop2->pathinfo($data['name']);
        $extension = strtolower($pathinfo['extension']);
        $filename = strtolower($pathinfo['filename']);

        $image_extensions = $allowed_extensions = array();
        if (!empty($properties['imageExtensions'])) {
            $image_extensions = array_map('trim', explode(',', strtolower($properties['imageExtensions'])));
        }
        if (!empty($properties['allowedFileTypes'])) {
            $allowed_extensions = array_map('trim', explode(',', strtolower($properties['allowedFileTypes'])));
        }
        if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
            @unlink($data['tmp_name']);

            return $this->failure($this->modx->lexicon('ms2_err_gallery_ext'));
        } else {
            if (in_array($extension, $image_extensions)) {
                if (empty($data['properties']['height']) || empty($data['properties']['width'])) {
                    @unlink($data['tmp_name']);

                    return $this->failure($this->modx->lexicon('ms2_err_wrong_image'));
                }
                $type = 'image';
            } else {
                $type = $extension;
            }
        }

        // Duplicate check
        $tmpObject = $this->modx->getObject($this->classKey, array(
            'product_id' => $this->product->id,
            'hash' => $data['hash'],
            'parent' => 0,
        ));
        if ($tmpObject) {
            @unlink($data['tmp_name']);
            return $this->failure($this->modx->lexicon('ms2_err_gallery_exists'), array('code' => 304, 'file_id' => $tmpObject->get('id'), 'file' => $tmpObject->get('file'), 'name' => $tmpObject->get('name')));
        }

        $filename = !empty($properties['imageNameType']) && $properties['imageNameType'] == 'friendly'
            ? $this->product->cleanAlias($filename)
            : $data['hash'];
        $filename = str_replace(',', '', $filename) . '.' . $extension;
        $tmp_filename = $filename;
        $i = 1;
        while (true) {
            $count = $this->modx->getCount($this->classKey, array(
                'product_id' => $this->product->id,
                'file' => $tmp_filename,
                'parent' => 0,
            ));
            if (!$count) {
                $filename = $tmp_filename;
                break;
            } else {
                $pcre = '#(-' . ($i - 1) . '|)\.' . $extension . '$#';
                $tmp_filename = preg_replace($pcre, "-$i.$extension", $tmp_filename);
                $i++;
            }
        }

        $rank = isset($properties['imageUploadDir']) && empty($properties['imageUploadDir'])
            ? 0
            : $this->modx->getCount($this->classKey, array('parent' => 0, 'product_id' => $this->product->id));

        /** @var msProductFile $uploaded_file */
        $uploaded_file = $this->modx->newObject($this->classKey, array(
            'product_id' => $this->product->id,
            'parent' => 0,
            'name' => preg_replace('#\.' . $extension . '$#i', '', $data['name']),
            'file' => $filename,
            'path' => $this->product->id . '/',
            'source' => $this->mediaSource->get('id'),
            'type' => $type,
            'rank' => $rank,
            'createdon' => date('Y-m-d H:i:s'),
            'createdby' => $this->modx->user->id,
            'hash' => $data['hash'],
            'properties' => $data['properties'],
        ));

        $this->mediaSource->createContainer($uploaded_file->get('path'), '/');
        $this->mediaSource->errors = array();
        if ($this->mediaSource instanceof modFileMediaSource) {
            $upload = $this->mediaSource->createObject($uploaded_file->get('path'), $uploaded_file->get('file'), file_get_contents($data['tmp_name']));
        } else {
            $data['name'] = $filename;
            $upload = $this->mediaSource->uploadObjectsToContainer($uploaded_file->get('path'), array($data));
        }
        @unlink($data['tmp_name']);

        if ($upload) {
            $url = $this->mediaSource->getObjectUrl($uploaded_file->get('path') . $uploaded_file->get('file'));
            $uploaded_file->set('url', $url);
            $uploaded_file->save();

            if (empty($rank)) {
                $imagesTable = $this->modx->getTableName($this->classKey);
                $sql = "UPDATE {$imagesTable} SET `rank` = `rank` + 1 WHERE product_id ='" . $this->product->id . "' AND id !='" . $uploaded_file->get('id') . "'";
                $this->modx->exec($sql);
            }

            $generate = $uploaded_file->generateThumbnails($this->mediaSource);
            if ($generate !== true) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    '[miniShop2] Could not generate thumbnails for image with id = ' . $uploaded_file->get('id') .
                    '. ' . $generate);

                return $this->failure($this->modx->lexicon('ms2_err_gallery_thumb'));
            } else {
                $this->product->updateProductImage();

                return $this->success('', $uploaded_file);
            }
        } else {
            return $this->failure($this->modx->lexicon('ms2_err_gallery_save') . ': ' .
                print_r($this->mediaSource->getErrors(), true)
            );
        }
    }


    /**
     * @return array|bool
     */
    public function handleFile()
    {
        $tf = tempnam(MODX_BASE_PATH, 'ms_');

        if (!empty($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $name = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $tf);
        } else {
            $file = $this->getProperty('file');
            if (!empty($file) && (strpos($file, '://') !== false || file_exists($file))) {
                $tmp = explode('/', $file);
                $name = end($tmp);
                if ($stream = fopen($file, 'r')) {
                    if ($res = fopen($tf, 'w')) {
                        while (!feof($stream)) {
                            fwrite($res, fread($stream, 8192));
                        }
                        fclose($res);
                    }
                    fclose($stream);
                }
            }
        }

        clearstatcache(true, $tf);
        if (file_exists($tf) && !empty($name) && $size = filesize($tf)) {
            if ($this->resize($tf)) {
                $size = filesize($tf);
            }

            $o = $this->modx->newObject($this->classKey);
            if (method_exists($o, 'generateHash')) {
                $hash = $o->generateHash($tf);
            } else {
                $res = fopen($tf, 'r');
                $hash = sha1(fread($res, 8192));
                fclose($res);
            }

            $data = array(
                'name' => $name,
                'tmp_name' => $tf,
                'hash' => $hash,
                'properties' => array(
                    'size' => $size,
                ),
            );


            $tmp = getimagesize($tf);


            if (is_array($tmp)) {
                $data['properties'] = array_merge(
                    $data['properties'],
                    array(
                        'width' => $tmp[0],
                        'height' => $tmp[1],
                        'bits' => $tmp['bits'],
                        'mime' => $tmp['mime'],
                    )
                );
            } elseif (strpos($data['name'], '.webp') !== false) {
                $img = imagecreatefromwebp($tf);
                $width = imagesx($img);
                $height = imagesy($img);

                $data['properties'] = array_merge(
                    $data['properties'],
                    array(
                        'width' => $width,
                        'height' => $height,
                        'mime' => 'image/webp',
                    )
                );
            }
            return $data;
        } else {
            unlink($tf);

            return false;
        }
    }

    /**
     * @param string $file
     * @param array $opt
     * @return bool|void
     */
    public function resize($file, $opt = array())
    {
        if (!$this->getProperty('resize_upload_image', false)) return;
        $tmp = getimagesize($file);
        if (is_array($tmp)) {
            $width = $tmp[0];
            $height = $tmp[1];
        } else if (strpos(basename($file), '.webp') !== false) {
            $img = imagecreatefromwebp($file);
            $width = imagesx($img);
            $height = imagesy($img);
        } else {
            return;
        }
        $properties = $this->mediaSource->getPropertyList();
        if ($width > $properties['maxUploadWidth'] || $height > $properties['maxUploadHeight']) {
            $options = array();
            if ($properties['maxUploadWidth'] > $properties['maxUploadHeight']) {
                $options['h'] = $properties['maxUploadHeight'];
            } else {
                $options['w'] = $properties['maxUploadWidth'];
            }
            $options = array_merge(array(
                'q' => $properties['thumbnailQuality'],
                'far' => '1',
                'bg' => 'ffffff'
            ), $options, $opt);

            if (!class_exists('modPhpThumb')) {
                /** @noinspection PhpIncludeInspection */
                require MODX_CORE_PATH . 'model/phpthumb/modphpthumb.class.php';
            }

            /** @noinspection PhpParamsInspection */
            $phpThumb = new modPhpThumb($this->modx);
            $phpThumb->initialize();

            foreach ($options as $k => $v) {
                $phpThumb->setParameter($k, $v);
            }
            $phpThumb->setSourceFilename($file);

            if ($phpThumb->GenerateThumbnail()) {
                if (!$phpThumb->RenderToFile($file)) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[phpThumbOf] Could not cache thumb "' . $file . '"  - Debug: ' . print_r($phpThumb->debugmessages, true));
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[resize] Could not generate thumbnail: ' . $file . ' - Debug: ' . print_r($phpThumb->debugmessages, true));
            }
        }
        return true;

    }
}

return 'IeGalleryMs2ImagesUploadProcessor';
