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
class Ves_Blog_Model_System_Config_Backend_Blog_Blog extends Mage_Core_Model_Config_Data {
	protected function _afterSave() {
		Mage::app()->cleanCache( array(
			Mage_Core_Model_Store::CACHE_TAG,
			Mage_Cms_Model_Block::CACHE_TAG,
			Ves_Blog_Model_Config::CACHE_WIDGET_LATEST_TAG
			) );

		Mage::app()->cleanCache( array(
			Mage_Core_Model_Store::CACHE_TAG,
			Mage_Cms_Model_Block::CACHE_TAG,
			Ves_Blog_Model_Config::CACHE_BLOCK_LATEST_TAG
			) );

		Mage::app()->cleanCache( array(
			Mage_Core_Model_Store::CACHE_TAG,
			Mage_Cms_Model_Block::CACHE_TAG,
			Ves_Blog_Model_Config::CACHE_BLOCK_MENU_TAG
			) );

		Mage::app()->cleanCache( array(
			Mage_Core_Model_Store::CACHE_TAG,
			Mage_Cms_Model_Block::CACHE_TAG,
			Ves_Blog_Model_Config::CACHE_BLOCK_ARCHIVES_TAG
			) );

		Mage::app()->cleanCache( array(
			Mage_Core_Model_Store::CACHE_TAG,
			Mage_Cms_Model_Block::CACHE_TAG,
			Ves_Blog_Model_Config::CACHE_BLOCK_TAGS_TAG
			) );
	}
}