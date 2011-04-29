<?php

interface Lec_Order_Model {
	
	/**
	 * Return a list of strings which represent the type of this order.
	 * Types indicate which plugins should handle order processing.
	 * Types can be dynamically generated from inspecting the order contents.
	 *
	 * @return Array  list of strings
	 */
	public function getTypes();

	/**
	 * Accept a transaction plugin and call $plugin->visit($this);
	 */
	public function accept($plugin);

	/**
	 * Return a clone of this object
	 *
	 * @return Object Lec_Order_Model
	 */
	public function cloneNew();
}


class Lec_Order_Model_Default implements Lec_Order_Model {

	public $_typeList = array();
	public $_itemList = array();
	public $_propList = array();
	public $_taxList  = array();
	public $_adjList  = array();

	public $_phaseList        = array();
	public $_paymentList      = array();
	public $_shipmentList     = array();
	public $_origByItemList   = array();
	public $_destByItemList   = array();
	public $_splrByItemList   = array();
	public $_validByItemList  = array();
	public $_errorByItemList  = array();
	public $_multiOrderList   = array();

	public $_accountId   = -1;
	public $_userId      = -1;

	public function getTypes() {
		return $this->_typeList;
	}


	/**
	 * Accept a transaction plugin and call $plugin->visit($this);
	 */
	public function accept($plugin) {
		return $plugin->visit($this);
	}


	public function cloneNew() {
		return clone $this;
	}

	/**
	 * Return a list of phases this order is in
	 *
	 * @return Array list of phases this order is in
	 */
	public function getPhases() {
		return $this->_phaseList;
	}

	/**
	 * Return true if $phase is in $_phaseList;
	 *
	 * @return boolean true if $phase is in $_phaseList
	 */
	public function inPhase($phase) {
		return in_array($phase, $this->_phaseList);
	}

	/**
	 * Ensure $phase is listed only once in $_phaseList
	 */
	public function setPhase($phase) {
		$this->removePhase($phase);
		$this->_phaseList[] = $phase;
	}

	/**
	 * Clear out all references to $phase from $_phaseList
	 */
	public function removePhase($phase) {
		foreach ($this->_phaseList as $_k => $_p) {
			if ($phase == $_p) {
				unset($this->_phaseList[$_k]);
			}
		}
	}
}

