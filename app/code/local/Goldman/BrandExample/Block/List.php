<?php
class Goldman_BrandExample_Block_List extends Mage_Core_Block_Template
{
    public function getBrandCollection()
    {
        return Mage::getModel('goldman_branddirectory/brand')->getCollection()
            ->addFieldToFilter('visibility', Goldman_BrandDirectory_Model_Brand::VISIBILITY_DIRECTORY)
            ->setOrder('name', 'ASC');
    }
}