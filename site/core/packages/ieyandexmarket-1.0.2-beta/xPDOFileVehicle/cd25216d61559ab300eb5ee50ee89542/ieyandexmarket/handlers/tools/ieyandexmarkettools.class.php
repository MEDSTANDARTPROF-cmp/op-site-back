<?php

class ieYandexMarketTools extends MsIeTools
{

    /**
     * @param string $className
     * @return IeYandexMarketXMLWriter|null
     */
    public function getXMLWriter($className = '')
    {
        $className = $this->modx->getOption('ieyandexmarket_xml_writer_class', null, 'IeYandexMarketXMLWriter', true);
        return $this->loadWriter($className, $this->config['writersPath']);
    }

    /**
     * @return array
     */
    public function getMulticategories()
    {
        $result = array();
        $classKey = 'msCategoryMember';
        $q = $this->modx->newQuery($classKey);

        $q->select($this->modx->getSelectColumns($classKey, $classKey));
        $q->sortby('product_id'); 

        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!isset($result[$row['product_id']])) {
                    $result[$row['product_id']] = array();
                }
                $result[$row['product_id']][] = $row['category_id'];
            }
        }
        return $result;
    }
}