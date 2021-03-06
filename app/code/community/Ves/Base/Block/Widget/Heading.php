<?php
class Ves_Base_Block_Widget_Heading extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface{


	public function __construct($attributes = array())
	{
		parent::__construct($attributes);

		if($this->hasData("template")) {
        	$my_template = $this->getData("template");
        }else{
 			$my_template = "ves/base/heading.phtml";
 		}
        $this->setTemplate($my_template);
	}



	protected function _toHtml(){
		if(!Mage::getStoreConfig('ves_base/general_setting/show')) {
			return ;
		}
		$content_html = $this->getConfig('content');
		$content_html = base64_decode($content_html);
		
		if($content_html) {
			$processor = Mage::helper('cms')->getPageTemplateProcessor();
			$content_html = $processor->filter($content_html);
		}
		
		$this->assign('content', $content_html);
		$this->assign('heading_tag', $this->getConfig('heading_tag', 'h1'));
		$this->assign('prefix', $this->getConfig('prefix'));

		return parent::_toHtml();
	}
	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	public function getConfig( $key, $default = ""){
	    $value = $this->getData($key);
	    //Check if has widget config data
	    if($this->hasData($key) && $value !== null) {

	      if($value == "true") {
	        return 1;
	      } elseif($value == "false") {
	        return 0;
	      }
	      
	      return $value;
	      
	    }
	    return $default;
	}
}