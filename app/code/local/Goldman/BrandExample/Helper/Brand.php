<?php
class Goldman_BrandExample_Helper_Brand extends Mage_Core_Helper_Abstract
{
    public function getBrandUrl(Goldman_BrandDirectory_Model_Brand $brand)
    {
        if (!$brand instanceof Goldman_BrandDirectory_Model_Brand) {
            return '#';
        }
        
        return $this->_getUrl(
            'goldman_brandexample/index/view', 
            array(
                'url' => $brand->getUrlKey(),
            )
        );
    }
}