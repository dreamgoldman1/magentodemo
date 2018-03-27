<?php

class Ves_Tempcp_Model_Observer {

	public function isAdmin(Varien_Event_Observer $observer)
    {

        if(Mage::app()->getStore()->isAdmin())
        {
            return true;
        }

        if(Mage::getDesign()->getArea() == 'adminhtml')
        {
            return true;
        }

        Mage::getSingleton('core/session', array('name'=>'adminhtml'));

        if(Mage::getSingleton('admin/session')->isLoggedIn()){
        	return true;
        }

        $controllerAction = $observer->getEvent()->getControllerAction();
    	if($controllerAction) {
    		$action_name = $controllerAction->getFullActionName();
	        if($action_name == "cms_index_noRoute") {
	        	return true;
	        }
	    } else {
	    	return true;
	    }
        return false;
    }

	public function initControllerPredispatch(Varien_Event_Observer $observer) {
		$this->_initFramework($observer);
    }
    public function beforeRender(Varien_Event_Observer $observer) {
		$this->_initFramework($observer);
    }

    function _initFramework(Varien_Event_Observer $observer) {
		if($this->isAdmin($observer)){
		  //do stuff

		} else {
			$helper = Mage::helper("ves_tempcp/framework")->getFramework( );

			if(!$helper || !is_object($helper)) {
				$packageName =  Mage::getSingleton('core/design_package')->getPackageName();
				if(!$packageName) {
					$packageName = "default";
				}

				$themeName =  Mage::getDesign()->getTheme('frontend');
				$themeName = $packageName."/".$themeName;
				$themeConfig = Mage::helper('ves_tempcp/theme')->getCurrentTheme();
				$helper = Mage::helper("ves_tempcp/framework")->initFramework( $themeName, $themeConfig );
			}
		}
    }

    public function update($observer)
    {
    	$helper = Mage::helper("ves_tempcp/framework")->getFramework( );

		if(!$helper || !is_object($helper)) {

			$themeConfig = Mage::helper('ves_tempcp/theme')->getCurrentTheme();
			$helper = Mage::helper("ves_tempcp/framework")->initFramework( $themeName, $themeConfig );

		} else {

			$themeConfig = $helper->getConfig();
			
		}
		if($themeConfig->get("enable_cartedit")) {
			$cart = $observer->getCart();
	        $data = $observer->getInfo();

	        foreach ($data as $itemId => $itemInfo) {
	            $item = $cart->getQuote()->getItemById($itemId);
	            if (!$item) continue;
	            $cart->updateItem($item->getId(),$itemInfo);
	        }
	        $cart->save();
		}
        
    }

}
