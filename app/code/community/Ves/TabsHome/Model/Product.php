<?php
/******************************************************
 * @package Venustheme Megamenu module for Magento 1.4.x.x and Magento 1.7.x.x
 * @version 1.0.0.1
 * @author http://venustheme.com
 * @copyright	Copyright (C) December 2010 venustheme.com <@emai:venustheme@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
*******************************************************/

class Ves_TabsHome_Model_Product extends Mage_Catalog_Block_Product_Abstract{

	protected $_config = '';
	const DEFAULT_STORE_ID = 0;
	const CACHE_BLOCK_TAG = 'ves_tabshome_block';
	const CACHE_WIDGET_TAG = 'ves_tabshome_widget';
	public $_current_page = 1;
	public function getConfig( $key, $val=0) 
    {
		return (isset($this->_config[$key])?$this->_config[$key]:$val);
    }
    public function setCurPage($page = 1) {
    	$this->_current_page = (int)$page;
    }

    public function getCollectionPro($model_type = 'catalog/product_collection')
    {
      $storeId = Mage::app()->getStore()->getId();        
      $productFlatTable = Mage::getResourceSingleton('catalog/product_flat')->getFlatTableName($storeId);
      $attributesToSelect = "*";//array('name','entity_id','price', 'small_image','short_description');
      try{
	        /**
	        * init resource singleton collection
	        */
	        $products = Mage::getResourceModel($model_type);//Mage::getResourceSingleton('reports/product_collection');
	        if(Mage::helper('catalog/product_flat')->isEnabled()){
	          $products->joinTable(array('flat_table'=>$productFlatTable),'entity_id=entity_id', $attributesToSelect);
	        }else{
	          $products->addAttributeToSelect($attributesToSelect);
	        }
	        $products->addStoreFilter($storeId);
       		return $products;
      }catch (Exception $e){
            Mage::logException($e->getMessage());
      }
    }

	public function getListSpecialProducts( $config = array() ){
		$this->_config = $config;
		$storeId    = Mage::app()->getStore()->getId();
		$cateids = $this->getConfig('catsid');
		if($cateids && $cateids != "1") {
			$arr_catsid = array();
	    	if(stristr($cateids, ',') === FALSE) {
	          $arr_catsid =  array($cateids);
		    }else{
		          $arr_catsid = explode(",", $cateids);
		    }
		    $resource = Mage::getSingleton('core/resource');
		    
		    $products = $this->getCollectionPro()
			         	->addFieldToFilter('visibility', array(
			                               Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
			                               Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
			                   )) //showing just products visible in catalog or both search and catalog
			         	->addMinimalPrice()
						->addUrlRewrite()
						->addTaxPercents()
						->addStoreFilter($storeId)
			            ->addFinalPrice()
			            ->joinTable($resource->getTableName('catalog_category_product'), 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left')
			         	->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $arr_catsid))))
			         	->groupByAttribute('entity_id');
		         	 
        	$products ->getSelect()
               			->where('price_index.final_price < price_index.price');

			                   ;
    	} else {
		    $products = $this->getCollectionPro()
			         	->addFieldToFilter('visibility', array(
			                               Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
			                               Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
			                   )) //showing just products visible in catalog or both search and catalog
			         	->addMinimalPrice()
						->addUrlRewrite()
						->addTaxPercents()
						->addStoreFilter($storeId)
			            ->addFinalPrice()
			         	->groupByAttribute('entity_id');
		         	 
        	$products ->getSelect()
               			->where('price_index.final_price < price_index.price');
    	}

    	Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $products->setPageSize($this->getConfig('limit_item',6))->setCurPage( $this->_current_page );
        $this->setProductCollection($products);

		$this->_addProductAttributesAndPrices($products);
        $list = array();                  
		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {            
			$list = $products;
		}
		
		return $list;
	}
    
    public function getListLatestProducts(  $config = array())
    {
    	$fieldorder = 'created_at';
    	$order = 'desc';
    	$this->_config = $config;
    	$storeId    = Mage::app()->getStore()->getId();
    	$cateids = $this->getConfig('catsid');
    	$todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $resource = Mage::getSingleton('core/resource');

    	if($cateids && $cateids != "1") {
    		$cateids = $this->getConfig('catsid', 'catalog_source_setting');
	    	$arr_catsid = array();
	    	if(stristr($cateids, ',') === FALSE) {
	          $arr_catsid =  array($cateids);
		    }else{
		          $arr_catsid = explode(",", $cateids);
		    }

    	    $products   = $this->getCollectionPro()
							    ->addAttributeToFilter(array( array('attribute' => 'news_from_date', array('or'=> array(
					                0 => array('date' => true, 'to' => $todayEndOfDayDate),
					                1 => array('is' => new Zend_Db_Expr('null')))
					          ), 'left')))
					          ->addAttributeToFilter(array( array('attribute' => 'news_to_date', array('or'=> array(
					                0 => array('date' => true, 'from' => $todayStartOfDayDate),
					                1 => array('is' => new Zend_Db_Expr('null')))
					            ), 'left')))
						          ->joinTable($resource->getTableName('catalog_category_product'), 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left')
				         			->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $arr_catsid))))
						          ->addAttributeToSort('news_from_date', 'desc')
						          ->addAttributeToSort($fieldorder, $order)
							    ->addMinimalPrice()
							    ->addFinalPrice()
							    ->addStoreFilter($storeId)
							    ->addUrlRewrite()
							    ->addTaxPercents()
							    ->groupByAttribute('entity_id');
    	} else {
		    $products   = $this->getCollectionPro()
							    ->addAttributeToFilter(array( array('attribute' => 'news_from_date', array('or'=> array(
					                0 => array('date' => true, 'to' => $todayEndOfDayDate),
					                1 => array('is' => new Zend_Db_Expr('null')))
					          ), 'left')))
					          ->addAttributeToFilter(array( array('attribute' => 'news_to_date', array('or'=> array(
					                0 => array('date' => true, 'from' => $todayStartOfDayDate),
					                1 => array('is' => new Zend_Db_Expr('null')))
					            ), 'left')))
						        ->addAttributeToSort('news_from_date', 'desc')
						        ->addAttributeToSort($fieldorder, $order)
							    ->addMinimalPrice()
							    ->addFinalPrice()
							    ->addStoreFilter($storeId)
							    ->addUrlRewrite()
							    ->addTaxPercents()
							    ->groupByAttribute('entity_id');
    	}		
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $products->setPageSize($this->getConfig('limit_item',6))->setCurPage($this->_current_page);
        $this->setProductCollection($products);
		
		$this->_addProductAttributesAndPrices($products);
        $list = array();                  
		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {            
			$list = $products;
		}
		
		return $list;
    }

    public function getListRandomProducts(  $config = array())
    {
    	$fieldorder = 'created_at';
    	$order = 'desc';
    	$this->_config = $config;
    	$storeId    = Mage::app()->getStore()->getId();
    	$cateids = $this->getConfig('catsid');
    	
        $resource = Mage::getSingleton('core/resource');

    	if($cateids && $cateids != "1") {
    		$cateids = $this->getConfig('catsid', 'catalog_source_setting');
	    	$arr_catsid = array();
	    	if(stristr($cateids, ',') === FALSE) {
	          $arr_catsid =  array($cateids);
		    }else{
		          $arr_catsid = explode(",", $cateids);
		    }

    	    $products   = $this->getCollectionPro()
							   
						        ->joinTable($resource->getTableName('catalog_category_product'), 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left')
				         		->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $arr_catsid))))
							    ->addMinimalPrice()
							    ->addFinalPrice()
							    ->addStoreFilter($storeId)
							    ->addUrlRewrite()
							    ->addTaxPercents()
							    ->groupByAttribute('entity_id');
    	} else {
		    $products   = $this->getCollectionPro()
							    ->addMinimalPrice()
							    ->addFinalPrice()
							    ->addStoreFilter($storeId)
							    ->addUrlRewrite()
							    ->addTaxPercents()
							    ->groupByAttribute('entity_id');
    	}		
    	$products->getSelect()->order(new Zend_Db_Expr('RAND()'));

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $products->setPageSize($this->getConfig('limit_item',6))->setCurPage($this->_current_page);

        $this->setProductCollection($products);
		
		$this->_addProductAttributesAndPrices($products);
        $list = array();                  
		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {            
			$list = $products;
		}
		
		return $list;
    }
    public function getListRelatedProducts(  $config = array())
    {
    	$list = array();
    	$product_id = isset($config['product_id'])?$config['product_id']:(Mage::registry('current_product')?Mage::registry('current_product')->getId():0);

    	$fieldorder = 'created_at';
    	$order = Varien_Db_Select::SQL_DESC;

    	if($product_id) {

    		$product = Mage::registry('current_product');
	        /* @var $product Mage_Catalog_Model_Product */

	        $_itemCollection = $product->getRelatedProductCollection()
	            ->addAttributeToSelect('required_options')
	            ->setPositionOrder()
	            ->addStoreFilter()
	        ;

	        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
	            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($_itemCollection,
	                Mage::getSingleton('checkout/session')->getQuoteId()
	            );
	            $this->_addProductAttributesAndPrices($_itemCollection);
	        }
	//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
	        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($_itemCollection);

	        $_itemCollection->load();

	        foreach ($_itemCollection as $product) {
	            $product->setDoNotUseCategoryId(true);
	        }

    		$list = $_itemCollection;
    	}
		
		return $list;
    }
    public function getListUpsellProducts(  $config = array())
    {
    	$list = array();
    	$fieldorder = 'created_at';
    	$order = Varien_Db_Select::SQL_DESC;
		// Get product object.
		$product_id = isset($config['product_id'])?$config['product_id']:(Mage::registry('current_product')?Mage::registry('current_product')->getId():0);

		if($product_id) {
			$object = Mage::getModel('catalog/product');
	   
		    //Get product detail using product id  (Suppose you have product id is : $product_id)
		    $_product = $object->load($product_id);
	  
	   		// Fetch list of upsell product using query.
	   		$upsell_product = $_product->getUpSellProductCollection()->addAttributeToSort('position', Varien_Db_Select::SQL_ASC)->addStoreFilter();

		   //check if record is empty or not
		   $count = count($upsell_product);
		   if(!empty($count)) {
		   		//if result is not empty then get  upsell product detail using foreach loop
		   		$productIds = array();
		      	foreach($upsell_product as $_upsell){
		      		$productIds[] = $_upsell->getId();
		      	}
		         
		        if($productIds) {
					$products = Mage::getResourceModel('catalog/product_collection')
				    ->addAttributeToSelect('*')
				    ->addMinimalPrice()
				    ->addUrlRewrite()
				    ->addTaxPercents()
				    ->addStoreFilter()
				    ->addIdFilter($productIds)
				    ->setOrder ($fieldorder,$order);

				    Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
			        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
			        $products->setPageSize($this->getConfig('limit_item',6))->setCurPage($this->_current_page);
			        $this->setProductCollection($products);
					
					$this->_addProductAttributesAndPrices($products);
			        $list = array();                  
					if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {            
						$list = $products;
					}
				}
		   }
	   
		}
	  

		return $list;
    }
    
    public function getListBestSellerProducts(  $config = array())
    {
    	$this->_config = $config;
    	$fieldorder = 'ordered_qty';
    	$order = 'desc';
    	$storeId    = Mage::app()->getStore()->getId();
    	$cateids = $this->getConfig('catsid');

    	// Date
        $date = new Zend_Date();
        $toDate = $date->setDay(1)->getDate()->get('Y-MM-dd');
        $fromDate = $date->subMonth(1)->getDate()->get('Y-MM-dd');
        $resource = Mage::getSingleton('core/resource');
    	if($cateids && $cateids != "1") {

	    	$arr_catsid = array();
	    	if(stristr($cateids, ',') === FALSE) {
	          	$arr_catsid =  array($cateids);
		    }else{
		        $arr_catsid = explode(",", $cateids);
		    }

	    	 $products   = $this->getCollectionPro()
										//->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
										->addStoreFilter()
										->addPriceData()
										->addTaxPercents()
										->addUrlRewrite()
										->joinTable($resource->getTableName('catalog_category_product'), 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left')
									  ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $arr_catsid))));

			  $products->getSelect()
						->joinLeft(
							array('aggregation' => $products->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
							"e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
							array('SUM(aggregation.qty_ordered) AS sold_quantity')
							)
						->group('e.entity_id')
						->order(array('sold_quantity DESC', 'e.created_at'));


    	} else {

    		$products   = $this->getCollectionPro()
									//->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
									->addStoreFilter()
									->addPriceData()
									->addTaxPercents()
									->addUrlRewrite();

		  	$products->getSelect()
					->joinLeft(
						array('aggregation' => $products->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
						"e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
						array('SUM(aggregation.qty_ordered) AS sold_quantity')
						)
					->group('e.entity_id')
					->order(array('sold_quantity DESC', 'e.created_at'));

    	}
    	Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		
    	$list = array();

    	$products->setPageSize($this->getConfig('limit_item',6))->setCurPage(1);

		$list = $products;

		return $list;
    }

    public function getListTopRatedProducts($config = array()) {
		$this->_config = $config;
    	$storeId    = Mage::app()->getStore()->getId();
    	$cateids = $this->getConfig('catsid');
    	$resource = Mage::getSingleton('core/resource');
    	if($cateids && $cateids != "1") {
    		$arr_catsid = array();
	    	if(stristr($cateids, ',') === FALSE) {
	         	$arr_catsid =  array($cateids);
		    }else{
		        $arr_catsid = explode(",", $cateids);
		    }
    		$products   = $this->getCollectionPro('reports/product_collection')
			                   	->addAttributeToFilter(array( array('attribute' =>'visibility', array('neq'=>1))))
			                   	->joinTable($resource->getTableName('catalog_category_product'), 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left')
					         	->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $arr_catsid))))
					         	->groupByAttribute('entity_id');

			$products->joinField('rating_summary_field', 'review/review_aggregate', 'rating_summary', 'entity_pk_value=entity_id',  array('entity_type' => 1, 'store_id' => Mage::app()->getStore()->getId()), 'left');                
			$products->addAttributeToSort('rating_summary_field', 'desc');
		} else {
			$products   = $this->getCollectionPro('reports/product_collection')
                   				->addAttributeToFilter(array( array('attribute' =>'visibility', array('neq'=>1))))
		         				->groupByAttribute('entity_id');

			$products->joinField('rating_summary_field', 'review/review_aggregate', 'rating_summary', 'entity_pk_value=entity_id',  array('entity_type' => 1, 'store_id' => Mage::app()->getStore()->getId()), 'left');                
			$products->addAttributeToSort('rating_summary_field', 'desc');
		}

	    Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
	    Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

      	$products->setPageSize($this->getConfig('limit_item',6))->setCurPage(1);
      	$this->setProductCollection($products);
		$this->_addProductAttributesAndPrices($products);

      	$list = array();                  
		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {            
			$list = $products;
		}

		return $list;
	}
    public function getListMostViewedProducts(  $config = array())
    {
    	$this->_config = $config;
    	$storeId    = Mage::app()->getStore()->getId();
    	$cateids = $this->getConfig('catsid');
    	$resource = Mage::getSingleton('core/resource');
    	if($cateids && $cateids != "1") {
    		$arr_catsid = array();
	    	if(stristr($cateids, ',') === FALSE) {
	          $arr_catsid =  array($cateids);
		    }else{
		          $arr_catsid = explode(",", $cateids);
		    }
		    $products   = $this->getCollectionPro('reports/product_collection')
		    						->addAttributeToFilter(array( array('attribute' =>'visibility', array('neq'=>1))))
		    						->joinTable($resource->getTableName('catalog_category_product'), 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left')
		         					->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $arr_catsid))))
									->groupByAttribute('entity_id');


    	} else {

    		$products   = $this->getCollectionPro('reports/product_collection')
    								->addAttributeToFilter(array( array('attribute' =>'visibility', array('neq'=>1))))
									->groupByAttribute('entity_id');
    	}

    	Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        $products->setPageSize($this->getConfig('limit_item',6))->setCurPage(1);
        $this->setProductCollection($products);
		$this->_addProductAttributesAndPrices($products);

        $list = array();                  
		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {            
			$list = $products;
		}
		
		return $list;
    }
    
    public function getListFeaturedProducts(  $config = array())
    { 
    	$this->_config = $config;
    	$storeId    = Mage::app()->getStore()->getId();
    	$cateids = $this->getConfig('catsid');
    	$resource = Mage::getSingleton('core/resource');
    	if($cateids && $cateids != "1") {
    		$arr_catsid = array();
	    	if(stristr($cateids, ',') === FALSE) {
	          $arr_catsid =  array($cateids);
		      }else{
		          $arr_catsid = explode(",", $cateids);
		      }
	    	$products = $this->getCollectionPro()
										    ->addMinimalPrice()
										    ->addUrlRewrite()
										    ->addTaxPercents()
									  		->addAttributeToFilter( array( 
														    array( 'attribute'=>'featured', 'eq' => '1' )
														))
										    ->joinTable($resource->getTableName('catalog_category_product'), 'product_id=entity_id', array('category_id'=>'category_id'), null, 'left')
										    ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $arr_catsid))))
								    		->addAttributeToSort('news_from_date','desc')
										    ->addAttributeToSort('created_at', 'desc')
										    ->addAttributeToSort('updated_at', 'desc')
										    ->groupByAttribute('entity_id');	
    	} else {
	    	$products = $this->getCollectionPro()
										    ->addMinimalPrice()
										    ->addUrlRewrite()
										    ->addTaxPercents()
									  		->addAttributeToFilter( array( 
														    array( 'attribute'=>'featured', 'eq' => '1' )
														))
								    		->addAttributeToSort('news_from_date','desc')
										    ->addAttributeToSort('created_at', 'desc')
										    ->addAttributeToSort('updated_at', 'desc')
										    ->groupByAttribute('entity_id');	
    	}
    	
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $products->setPageSize($this->getConfig('limit_item',6))->setCurPage(1);
        $this->setProductCollection($products);
		$this->_addProductAttributesAndPrices($products);
        $list = array();
		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {
			       
			$list = $products;
		}
		
		return $list;
    }
    
    function inArray($source, $target) {
		for($i = 0; $i < sizeof ( $source ); $i ++) {
			if (in_array ( $source [$i], $target )) {
			return true;
			}
		}
    }
	
    function getProductByCategory(){
        $return = array(); 
        $pids = array();
        $catsid=$this->getConfig('catsid');
        $products = Mage::getResourceModel ( 'catalog/product_collection' );
         
        foreach ($products->getItems() as $key => $_product){
            $arr_categoryids[$key] = $_product->getCategoryIds();
            
            if($catsid && $catsid !="1"){    
                if(stristr($catsid, ',') === FALSE) {
                    $arr_catsid[$key] =  array(0 => $catsid);
                }else{
                    $arr_catsid[$key] = explode(",", $catsid);
                }
                
                $return[$key] = $this->inArray($arr_catsid[$key], $arr_categoryids[$key]);
            }
        }
        
        foreach ($return as $k => $v){ 
            if($v==1) $pids[] = $k;
        }    
        
        return $pids;   
    }
}


?>