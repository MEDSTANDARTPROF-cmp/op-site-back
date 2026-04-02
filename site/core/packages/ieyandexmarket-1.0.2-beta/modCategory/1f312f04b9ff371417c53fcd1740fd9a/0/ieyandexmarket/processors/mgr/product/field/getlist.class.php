<?php

class IeYandexMarketProductFieldGetListProcessor extends modProcessor
{
    /** @var IeYandexMarket $ieyandexmarket */
    public $ieyandexmarket;
    public $languageTopics = array('ieyandexmarket:default');

    public function initialize()
    {
        $this->ieyandexmarket = $this->modx->getService('ieyandexmarket', 'IeYandexMarket');
        return parent::initialize();
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var ieYandexMarketTools $tools */
        $tools = $this->ieyandexmarket->getTools();
        $query = trim($this->getProperty('query', ''));
        $exclude = $tools->getOption('import_exclude_fields');
        $exclude = $tools->explodeAndClean($exclude);

        $result = array();
        $fields = array_merge(
            $tools->getResourceFields('', ' Product', $exclude, true),
            $tools->getProductFields('', ' Product'),
            $tools->getProductCustomFields('', ' Product', array('vendor_name', 'vendor_country')),
            $tools->getProductOptionsFields('', ' Option'),
            $tools->getTvFields('tv.', 'TV', 'name')
        );

        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!empty($query)) {
                    if (
                        !preg_match('/' . $query . '/iu', $key) &&
                        !preg_match('/' . $query . '/iu', $field['alias'])
                    ) {
                        continue;
                    }
                }
                $result[] = $field;
            }

            if (!empty($query) && empty($result)) {
                $result[] = array('key' => $query, 'name' => $query, 'alias' => '', 'label' => '');
            }
        }
        return $result;
    }

    /**
     * @return array|mixed|string
     */
    public function process()
    {
        $list = $this->getList();
        return $this->outputArray($list, count($list));
    }
}

return 'IeYandexMarketProductFieldGetListProcessor';
