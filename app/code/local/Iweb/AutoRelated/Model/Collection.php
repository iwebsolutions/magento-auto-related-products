<?php

class Iweb_AutoRelated_Model_Collection extends Mage_Core_Model_Abstract
{
	public function getRelatedProducts($limit = false) {
		$products = $this->getData('related_products');
		if (!$products) {
			$product = Mage::registry('current_product');

			if ($category = Mage::registry('current_category')) {

			} elseif ($product) {
				$ids = $product->getCategoryIds();

				if (!empty($ids)) {
					$category = Mage::getModel('catalog/category')->load($ids[0]);
				}
			}

			if ($category) {
				if ($limit === false) {
					$limit = Mage::getStoreConfig('autorelated/general/limit');
				}

				$products = Mage::getResourceModel('reports/product_collection')
					->addAttributeToFilter('visibility', array(
						Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
						Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
					))
					->addAttributeToFilter('status', 1)
					->addCategoryFilter($category)
					->addAttributeToSelect('*')
					->setPageSize($limit);

				if ($product) {
					$products->addAttributeToFilter('entity_id', array(
						'neq' => Mage::registry('current_product')->getId())
					);
				}

				$products->getSelect()->order(new Zend_Db_Expr('RAND()'));
				Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($products);
				
				$this->setData('related_products', $products);
			} else {
				return false;
			}
		}
		
		return $products;
	}
}
