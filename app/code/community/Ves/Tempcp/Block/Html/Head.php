<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ves_Tempcp_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    /**
     * Initialize template
     *
     */
    protected function _construct()
    {
        parent::_construct();
    	if($this->isVenusTheme()) {
        	$this->setTemplate('venustheme/tempcp/head.phtml');
        }
    }

    public function isVenusTheme($theme_name = "") {
        $show_tempcp = Mage::getStoreConfig("ves_tempcp/ves_tempcp/show");
        if($show_tempcp) {
            if(!$theme_name) {
                $theme_name =  Mage::getDesign()->getTheme('frontend');
                $package = Mage::getSingleton('core/design_package')->getPackageName();
                $theme_name = $package."/".$theme_name;
            }
            if(!($_model = Mage::registry('ves_theme_model'))) {
                $_model  = Mage::getModel('ves_tempcp/theme')->loadThemeByGroup($theme_name);
                Mage::unregister('ves_theme_model');
                Mage::register('ves_theme_model', $_model);
            }
            if($_model && $_model->getId()){
                return true;
            }
        }
        
        return false;
    }
}