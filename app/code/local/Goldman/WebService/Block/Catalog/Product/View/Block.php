<?php

class Goldman_WebService_Block_Catalog_Product_View_Block extends Mage_Core_Block_Template {

    public function getCountries() {
        // Source REST from: https://restcountries.eu/
        $url = 'https://restcountries.eu/rest/v2/all';
        $countries = file_get_contents($url);                        
        $countries = json_decode($countries);
        
        return $countries;
    }

}
