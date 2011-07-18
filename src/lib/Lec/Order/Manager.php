<?php

class Lec_Order_Manager {

	protected static $_flyweight = NULL;


	/**
	 * Set a model class to be used for all
	 * order operations.  This class must implement 
	 * an Lec_Order_Model interface
	 */
	public function setFlyweight($fly) {
		Lec_Order_Manager::$_flyweight = $fly;
	}

	/**
	 * Return a flyweight
	 */
	public function getFlyweight($fly) {
		if (Lec_Order_Manager::$_flyweight === NULL) {
			$fly = new Lec_Order_Model_Default();
			Lec_Order_Manager::$_flyweight = $fly;
		}
		return Lec_Order_Manager::$_flyweight;
	}

	/**
	 * Return a clone of the order Model
	 *
	 * @return Lec_Order_Model 
	 */
	public function getModel() {
		$fly = Lec_Order_Manager::getFlyweight();
		return $fly->cloneNew();
	}
}
