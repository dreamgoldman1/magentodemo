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
class Ves_Blog_Model_Config extends Mage_Catalog_Model_Product_Media_Config {

  const CACHE_BLOCK_LATEST_TAG = 'ves_blog_block_latest';
  const CACHE_WIDGET_LATEST_TAG = 'ves_blog_widget_latest';
  const CACHE_BLOCK_MENU_TAG = 'ves_blog_block_menu';
  const CACHE_BLOCK_ARCHIVES_TAG = 'ves_blog_block_archives';
  const CACHE_BLOCK_TAGS_TAG = 'ves_blog_block_tags';
  const CACHE_BLOCK_RSS_TAG = 'ves_blog_block_rss';
  const CACHE_BLOCK_RSS_LINK_TAG = 'ves_block_block_rsslink';

  public function getBaseMediaPath() {
    return Mage::getBaseDir('media') .DS. 'blog';
  }

  public function getBaseMediaUrl() {
    return Mage::getBaseUrl('media') . 'blog';
  }

  public function getBaseTmpMediaPath() {
    return Mage::getBaseDir('media') .DS. 'tmp' .DS. 'blog';
  }

  public function getBaseTmpMediaUrl() {
    return Mage::getBaseUrl('media') . 'tmp/blog';
  }

}