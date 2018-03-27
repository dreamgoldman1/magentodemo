<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Block_Blog_Headtags extends Ves_Blog_Block_Blog_Template
{
	private $post;
	private $category;
	
	public function __construct($attributes = array())
	{

		parent::__construct( $attributes );
		
		$my_template = "";
		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		}elseif($this->hasData("template")) {
			$my_template = $this->getData("template");
		}else {
			$my_template = "ves/blog/headtags.phtml";
		}
		$this->setTemplate($my_template);
	}
	public function _toHtml(){
		return parent::_toHtml();
	}
	public function getPost(){
		if( !$this->post ){
			$this->post = $this->getData("post_model");
		}
		if( !$this->post && Mage::registry('current_post')) {
			$this->post = Mage::registry('current_post');
		}
		return $this->post;

	}

	public function getCategory(){
		if( !$this->category ){
			$this->category = $this->getData("category_model");
		}
		if( !$this->category && Mage::registry('current_blog_category')) {
			$this->category = Mage::registry('current_blog_category');
		}
		return $this->category;

	}

}