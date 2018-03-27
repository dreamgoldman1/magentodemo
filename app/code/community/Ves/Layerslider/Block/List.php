<?php
/*------------------------------------------------------------------------
 # VenusTheme Layer slider Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
class Ves_Layerslider_Block_List extends Mage_Core_Block_Template 
{
	/**
	 * @var string $_config
	 * 
	 * @access protected
	 */
	protected $_config = '';
	
	/**
	 * @var string $_config
	 * 
	 * @access protected
	 */
	protected $_listDesc = array();
	
	/**
	 * @var string $_config
	 * 
	 * @access protected
	 */
	protected $_show = 0;
	protected $_theme = "";

	protected $_banner = null;
	
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{	
		$this->convertAttributesToConfig($attributes);
		$this->_show = $this->getConfig("show");

		parent::__construct();		
	}

    public function convertAttributesToConfig($attributes = array()) {
      if($attributes) {
        foreach($attributes as $key=>$val) {
            $this->setConfig($key, $val);
        }
      }
    }

	public function getSliderBanner() {
		return $this->_banner;
	}
	/**
     * Rendering block content
     *
     * @return string
     */
	function _toHtml() 
	{
		$this->_show = $this->getConfig("show");
		if(!$this->_show) return;

		$alias = null;
        $banner_id = $this->getConfig("bannerId");
		$banner_id = $banner_id?$banner_id:0;
        $alias = $this->getData('alias');
        $this->_banner = null;

        if($banner_id) {
			$this->_banner  = Mage::getModel('ves_layerslider/banner')->load( $banner_id );
		}

        if(!$this->_banner && $alias) {
        	$this->_banner = Mage::getModel('ves_layerslider/banner')->getSliderByAlias($alias);
        }

 		$banner  = $this->getSliderBanner();
 		
		if(empty($banner)) return;

		$is_flexslider = $this->getConfig("is_flexslider");

		$is_active =  $banner->getData("is_active");

		if($is_active) {
			$banners = array();
			$setting = array();
			$params = $banner->getData("params");
			if (base64_decode($params, true)) {
				$params = unserialize(base64_decode($params) );
			} else {
				$params = unserialize($params);
			}

			if($params) {
				foreach($params as $key => $slider) {
						if(strpos($key, "slide-container-") !== false && $slider) {
							if(isset($slider['type']) && $slider['type'] == 'image' && $slider['src']) {
								$slider['src'] = Mage::helper("ves_layerslider")->getImage( $slider['src'] );
							}
							$banners[] = $slider;
							
						}
				}
				$setting['slider_width'] = isset($params['ss']['width'])?(int)$params['ss']['width']:1040;
				$setting['slider_height'] = isset($params['ss']['height'])?(int)$params['ss']['height']:450;
				$setting['full_width'] = isset($params['fw'])?$params['fw']:0;
				$setting['flexslider'] = isset($params['fs'])?$params['fs']:array();
				$setting['general'] = isset($params['bg'])?$params['bg']:array();

				if(isset($setting['general']['src']) && $setting['general']['src']) {
					$setting['general']['src'] = Mage::helper("ves_layerslider")->getImage( $setting['general']['src'] );
				}
			}

			$this->assign("setting", $setting);
			$this->assign("params", $params);
			$this->assign("banners", $banners);

			if($is_flexslider ) {
				$this->setTemplate("ves/layerslider/flexslider.phtml");
			} else {
				$this->setTemplate("ves/layerslider/default.phtml");
			}
			
			return parent::_toHtml();
		}
		return ;
    }

	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	public function getConfig( $key, $default = "", $panel='general_setting'){
	    $return = "";
	    $value = $this->getData($key);
	    //Check if has widget config data
	    if($this->hasData($key) && $value !== null) {

	      if($value == "true") {
	        return 1;
	      } elseif($value == "false") {
	        return 0;
	      }
	      
	      return $value;
	      
	    } else {

	      if(isset($this->_config[$key])){
	        $return = $this->_config[$key];

	        if($return == "true") {
		        $return = 1;
		    } elseif($return == "false") {
		        $return = 0;
		    }
	      }else{
	        $return = Mage::getStoreConfig("ves_layerslider/$panel/$key");
	      }
	      if($return == "" && !$default) {
	        $return = $default;
	      }

	    }

	    return $return;
	}
    /**
     * overrde the value of the extension's configuration
     *
     * @return string
     */
    function setConfig($key, $value) {
		if($value == "true") {
	        $value =  1;
	    } elseif($value == "false") {
	        $value = 0;
	    }
    	if($value != "") {
	      	$this->_config[$key] = $value;
	    }
    	return $this;
    }

	public function renderBannerElements( $banners = array()) {
		$html = Mage::helper("ves_layerslider/slider")->renderBannerElements( $banners );
		return $html;
	}
}
