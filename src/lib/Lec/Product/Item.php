<?php

class Lec_Product_Item {

	protected $__resModel = null;
	public    $__resName  = 'lec_product';
	public    $types      = array();

	public function __construct() {
		$this->__initModel();
	}

	protected function __initModel() {
		$this->__resModel = new Lec_Resource_Model($this->__resName);
	}

	/**
	 * Return a reference to this object's resource model
	 *
	 * @return  Object  Lec_Recource_Model
	 */
	public function getResModel() {
		return $this->__resModel;
	}

	/**
	 * Add a product type to this product
	 *
	 * @param Object $type  A new product type to add to this product
	 * @void
	 */
	public function addType($type) {
		$this->types[] = $type;
	}

	/**
	 * Checks if this product is of a certain type
	 *
	 * @param String $type  Name of a product type
	 * @return Boolean
	 */
	public function isA($type) {
		return (bool)in_array($type, $this->types);
	}
}
