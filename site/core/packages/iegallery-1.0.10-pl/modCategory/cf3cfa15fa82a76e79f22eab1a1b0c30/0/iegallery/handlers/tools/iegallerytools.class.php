<?php

class ieGalleryTools extends MsIeTools
{


    /* @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getMs2ImagesFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $this->modx->lexicon->load('minishop2:product');

        $aFields = array_keys($this->modx->getFields('msProductFile'));
        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $field,// $this->lexicon($field, 'ms2gallery_file_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $field,// $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $field,// $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }

    /* @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getMs2GalleryFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $corePath = $this->modx->getOption('ms2gallery.core_path', null, $this->modx->getOption('core_path') . 'components/ms2gallery/');
        $this->modx->addPackage('ms2gallery', "{$corePath}model/");
        $this->modx->lexicon->load('ms2gallery:default');
        $aFields = array_keys($this->modx->getFields('msResourceFile'));
        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'ms2gallery_file_'),
                    'label' => $label,
                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'ms2gallery_file_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'ms2gallery_file_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getGalleryCustomFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $aFields = array('gallery');

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'msie_alias_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }


    /**
     * @param string $type
     * @return string
     */
    public function getGalleryClassKey($type)
    {
        $classKey = '';
        switch ($type) {
            case 'minishop2':
                $classKey = 'msProductFile';
                break;
            case 'ms2gallery':
                $classKey = 'msResourceFile';
                break;

        }
        return $classKey;
    }

    /**
     * @param string $mode
     * @param string $type
     * @return string
     */
    public function getServiceNameByGallery($mode, $type)
    {
        $serviceName = '';
        switch ($type) {
            case 'minishop2':
                $serviceName = $mode == Msie::MODE_IMPORT ? 'ieGalleryMs2ImagesImportService' : 'ieGalleryMs2ImagesExportService';
                break;
            case 'ms2gallery':
                $serviceName = $mode == Msie::MODE_IMPORT ? 'ieGalleryMs2GalleryImportService' : 'ieGalleryMs2GalleryExportService';
                break;

        }
        return $serviceName;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldGallery($field, array $data, array $result, MsIeWorker &$worker)
    {
        $result[$field] = '';
        if (!empty($data['id'])) {
            $options = $worker->getSettings();
            $options['taskId'] = $worker->task->get('id');
            $result[$field] = $this->getResourceGalleryImages($data['id'], $options, true);
        }
        return $result;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldAttachThumb($field, array $data, array $result, MsIeWorker &$worker)
    {
        $result[$field] = '';
        if (!empty($data['id'])) {
            $path = $this->getPathByMediaSource($data['source'], $data['context_key']);
            $attachSettings = $worker->getSetting('attach_settings', array());
            $thumb = $attachSettings['thumb'] ?: 'small';
            $name = basename($data['thumb']);
            $file = "{$path}/{$data['id']}/{$thumb}/{$name}";
            $result[$field] = $this->normalizePath($file);
        }
        return $result;
    }


    /**
     * @param int $rid
     * @param array $options
     * @param bool $prepare
     * @return string|array
     */
    public function getResourceGalleryImages($rid, array $options = array(), $prepare = false)
    {
        $result = '';
        $resourceKey = 'product_id';
        $galleryType = $this->modx->getOption('gallery_type', $options, '');
        $classKey = $this->getGalleryClassKey($galleryType);
        if (empty($classKey)) {
            return $result;
        }
        $delimiter = $this->modx->getOption('gallery_image_delimiter', $options, ',');
        $limit = $this->modx->getOption('gallery_limit', $options, 0);
        $sortdir = $this->modx->getOption('gallery_sortdir', $options, 'DESC');
        $copyImagePath = '';
        $taskId = $this->modx->getOption('taskId', $options, time());
        $isCopyImage = $this->modx->getOption('gallery_copy_image', $options, false);
        $isArchive = $this->modx->getOption('archive', $options, false);
        $isToArchive = $this->modx->getOption('gallery_add_images_to_archive', $options, false);
        $isToArchive = $isArchive && $isToArchive;
        if ($isCopyImage || $isToArchive) {
            $copyImagePath = $this->modx->getOption('gallery_copy_image_path', $options, '{assets_path}images/export/{task_id}/', true);
            $copyImagePath = $this->prepareCopyImagePath($copyImagePath, array(
                'taskId' => $taskId,
                'isToArchive' => $isToArchive
            ));
        }

        if ($classKey === 'msResourceFile') {
            $resourceKey = 'resource_id';
            $corePath = $this->modx->getOption('ms2gallery.core_path', null, $this->modx->getOption('core_path') . 'components/ms2gallery/');
            $this->modx->addPackage('ms2gallery', "{$corePath}model/");
        }

        $q = $this->modx->newQuery($classKey);
        $q->rightJoin('modResource', 'Resource', "`Resource`.`id` = `{$classKey}` . `{$resourceKey}`");
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id', 'path', 'file', 'url', 'source')));
        $q->select($this->modx->getSelectColumns('modResource', 'Resource', '', array('context_key')));

        $q->sortby("`{$classKey}`.`{$resourceKey}`,`{$classKey}`.`rank`", $sortdir);
        $q->where(array(
            "`{$classKey}`.`parent`" => 0,
            "`{$classKey}`.`{$resourceKey}`" => $rid
        ));

        if ($limit) {
            $q->limit($limit);
        }

        if ($q->prepare() && $q->stmt->execute()) {
            if ($prepare) {
                while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $file = $item['path'] . $item['file'];
                    if ($copyImagePath) {
                        $target = $copyImagePath . $file;
                        $source = $this->getPathByMediaSource($item['source'], $item['context_key']) . $file;
                        $this->modx->cacheManager->copyFile($source, $target);
                        if ($isToArchive) {
                            $file = './images/' . $file;
                        }
                    }
                    $result .= $result ? $delimiter . $file : $file;
                }
            } else {
                $result = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $result;
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return string
     */
    public function prepareCopyImagePath($path, array $options = array())
    {
        if (!$path) return '';
        $taskId = $options['taskId'] ?? '';
        $isToArchive = $options['isToArchive'] ?? false;
        $path = str_replace('{task_id}', $taskId, $path);
        $path = $this->preparePath($path . '/', true);
        if ($isToArchive) {
            $path .= 'images/';
        }
        if (!is_dir($path)) {
            if (!$this->modx->cacheManager->writeTree($path)) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Could not create directory {$path}");
                $path = '';
            }
        }
        return $path;
    }

    /**
     * @param int $id
     * @param array $options
     * @return array
     */
    public function getGalleryImageById($id, array $options = array())
    {
        $result = array();
        $galleryType = $this->modx->getOption('gallery_type', $options, '');
        $classKey = $this->getGalleryClassKey($galleryType);
        if (!empty($classKey)) {
            $q = $this->modx->newQuery($classKey);
            $q->where(array('id' => $id, 'parent' => 0));
            $q->select($this->modx->getSelectColumns($classKey, $classKey));
            if ($q->prepare() && $q->stmt->execute()) {
                $result = $q->stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return $result;
    }

}