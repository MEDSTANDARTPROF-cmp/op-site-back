<?php

class IeYandexMarketSalePricePriceGetListProcessor extends modProcessor
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
        $result = array();
        /** @var ieYandexMarketTools $tools */
        $tools = $this->ieyandexmarket->getTools();
        if (
            $tools->hasAddition('mssaleprice') &&
            $tools->hasAddition('iemssaleprice')
        ) {

            $iemssaleprice = $this->modx->getService('iemssaleprice', 'IeMsSalePrice');
            if ($iemssaleprice) {
                $counts = $iemssaleprice->getTools()->getCountSalePrice();
                if ($counts) {
                    $result[] = array('id' => 'price', 'name' => 'price');
                    $idx = 1;
                    foreach ($counts as $count) {
                        $idx++;
                        $key = 'price' . $idx;
                        $result[] = array('id' => $key, 'name' => $key);
                    }
                }
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

return 'IeYandexMarketSalePricePriceGetListProcessor';
