<?php

class Goldman_BrandExample_Block_Catalog_Product_View_Link extends Mage_Core_Block_Template {

    public function getBrand() {
        $product = Mage::registry('current_product');
        if (!$product instanceof Mage_Catalog_Model_Product) {
            return false;
        }

        $brandId = (int) $product->getBrandId();
        $brand = Mage::getModel('goldman_branddirectory/brand')->load($brandId);
        if ($brand->getId() < 1) {
            return false;
        }

        return $brand;
    }

}
