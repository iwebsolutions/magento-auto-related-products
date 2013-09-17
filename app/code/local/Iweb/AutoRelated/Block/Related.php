<?php

class Iweb_AutoRelated_Block_Related extends Mage_Catalog_Block_Product_List_Related
{
	protected function _construct() {
		// Only cache if we have something thats keyable..
		if($cacheKey = $this->_cacheKey()) {
			$this->addData(array(
				'cache_lifetime'    => 3600,
				'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
				'cache_key'         => $cacheKey,
			));
		}
    }

    protected function _cacheKey(){
		$product = Mage::registry('current_product');
		if($product) {
			return get_class() . '::' .  Mage::app()->getStore()->getCode() . '::' . $product->getId();
		}
		
		return false;
    }

	protected function _prepareData (){
		parent::_prepareData();
		
		$_enabled = Mage::getStoreConfig('autorelated/general/enabled');
		if ($_enabled && count($this->getItems()) == 0){
			$_products = Mage::getModel('autorelated/collection')->getRelatedProducts();
			if ($_products){
				$this->_itemCollection = $_products;
			}
		}

		return $this;
	}
}
