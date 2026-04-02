<?php
include_once MODX_CORE_PATH . 'components/iems2/handlers/workers/iems2productexportworker.class.php';

class ieGalleryMs2ImagesExportWorker extends MsIeExportWorker
{
    use IeMs2ProductExportWorkerTrait;

    /** @var string $classKey */
    protected $classKey = 'msProductFile';
    /** @var string $resourceKey */
    protected $resourceKey = 'product_id';
    /** @var string $copyImagePath */
    protected $copyImagePath = '';
    /** @var string $delimiter */
    protected $delimiter = '|';
    /** @var bool $absoluteUrl */
    protected $absoluteUrl = false;
    /** @var bool $concatenateImages */
    protected $concatenateImages = false;

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->delimiter = $this->getSetting('gallery_image_delimiter', ',');
            $this->absoluteUrl = $this->getSetting('gallery_absolute_url', false);
            $this->concatenateImages = $this->getSetting('gallery_concatenate_images', false);
            $this->addPrepareFieldMethod('gallery', $this, 'prepareFieldGallery');
            if (
                $this->isAddImagesToArchive() ||
                $this->getSetting('gallery_copy_image', false)
            ) {
                $this->copyImagePath = $this->prepareCopyImagePath();
            }
        }
        return $initialized;
    }

    /**
     * @return array
     */
    public function buildQueryConfig()
    {
        $config = parent::buildQueryConfig();
        $config['innerJoin']['Resource'] = array('class' => 'modResource', 'alias' => 'Resource', 'on' => '`Resource`.`id` = `' . $this->classKey . '`.`' . $this->resourceKey . '`');
        $config['leftJoin']['Data'] = array('class' => 'msProductData', 'alias' => 'Data', 'on' => '`Data`.`id` = `Resource`.`id`');

        $config['select'][$this->classKey] = $this->makeSelectGallery() . ',' . $this->makeSelectUrl() . ',';
        $config['select'][$this->classKey] .= $this->modx->getSelectColumns($this->classKey, $this->classKey, '', array('parent', 'url'), true);
        $config['select']['Resource'] = $this->modx->getSelectColumns('modResource', 'Resource', '', array('id'), true);
        $config['select']['Data'] = $this->modx->getSelectColumns('msProductData', 'Data', '', array('id', 'source'), true);

        $config['where'][$this->classKey . '.`parent`'] = 0;

        if ($resources = $this->getResourceIds()) {
            $config['where']['`Resource`.`id`:IN'] = $resources;

        }

        if ($this->getVendorIds()) {
            $config['where']["`Data`.`vendor`:IN"] = $this->getVendorIds();
        }

        return $config;
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
        if (!empty($data[$field])) {
            if ($images = $this->tools->explodeAndClean($data[$field], $this->delimiter)) {
                foreach ($images as $filename) {
                    $file = $data['path'] . $filename;
                    if($this->copyImagePath) {
                        $target = $this->copyImagePath . $file;
                        $source = $this->tools->getPathByMediaSource($data['source'], $data['context_key']) . $file;
                        $this->modx->cacheManager->copyFile($source, $target);
                        if (!empty($result[$field])) $result[$field] .= $this->delimiter;
                        if ($this->isAddImagesToArchive()) {
                            $file = './images/' . $file;
                        }
                    } else {
                        if (!empty($result[$field])) $result[$field] .= $this->delimiter;
                    }
                    $result[$field] .= $file;
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getFilesForArchiving()
    {
        $files = array();
        if ($this->isAddImagesToArchive() && $this->copyImagePath) {
            $files[] = $this->copyImagePath;
        }
        return $files;
    }

    /**
     * @return bool
     */
    public function isAddImagesToArchive()
    {
        $addImagesToArchive = $this->getSetting('gallery_add_images_to_archive', false);
        $archive = $this->getSetting('archive', false);
        return $archive && $addImagesToArchive;
    }

    /**
     * @return string
     */
    public function getCopyImagePath()
    {
        return $this->copyImagePath;
    }


    /**
     * @return string
     */
    public function prepareCopyImagePath()
    {
        $path = $this->getSetting('gallery_copy_image_path', '{assets_path}images/export/{task_id}/');
        return $this->tools->prepareCopyImagePath($path, array(
            'taskId' => $this->task->get('id'),
            'isToArchive' => $this->isAddImagesToArchive()
        ));
    }

    /**
     * @return string
     */
    public function makeSelectUrl()
    {
        $select = "`{$this->classKey}`.`url`";
        if ($this->absoluteUrl) {
            $prefix = $this->tools->getSiteUrl();
            $select = "CONCAT('{$prefix}',{$select})";
        }

        if ($this->concatenateImages) {
            $select = "GROUP_CONCAT({$select} SEPARATOR '{$this->delimiter}')";
        }

        return "{$select} as url";
    }

    /**
     * @return string
     */
    public function makeSelectGallery()
    {
        $select = "`{$this->classKey}`.`file`";
        if ($this->concatenateImages) {
            $select = "GROUP_CONCAT({$select} SEPARATOR '{$this->delimiter}')";
        }

        return "{$select} as gallery";
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return "`{$this->classKey}`.`{$this->resourceKey}`,`{$this->classKey}`.`rank`";
    }

    /**
     * @return string
     */
    public function getSortDir()
    {
        return 'DESC';
    }

    /**
     * @return string
     */
    public function getGroupBy()
    {
        if ($this->concatenateImages) {
            return "`{$this->classKey}`.`{$this->resourceKey}`";
        } else {
            return "`{$this->classKey}`.`id`";
        }
    }

}