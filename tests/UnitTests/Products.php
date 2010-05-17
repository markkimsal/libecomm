<?php
require_once(dirname(__FILE__).'/../../src/lib/Lec/Resource/Model.php');
require_once(dirname(__FILE__).'/../../src/lib/Lec/Product/Item.php');

class ProductsTest extends UnitTestCase {

	public function testAllProductsHaveResourceModel() {
		$y = new Lec_Product_Item();
		$this->assertTrue(is_object($y->getResModel()));

		$this->assertEqual("lec_product", $y->getResModel()->getStorageKind());
	}
}
