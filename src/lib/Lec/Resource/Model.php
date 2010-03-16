<?php

/**
 * Gateway to an ORM.  Lec_Resource_Driver_Storage knows 
 * how to interact with Lec_Resource_Models.  If you 
 * want to easily save your data, sub-class Lec_Resource_Model
 */
class Lec_Resource_Model {

	public $__tableName  = '';
	public $__id         = -1;
	public $__isNew      = TRUE;
	public $__searchable = FALSE;
	public $__excludes   = array();
	public $__nuls       = array();
	public $__bins       = array();
	public $__uniqs      = array();

	public function __construct($storageKind='', $id=-1) {
		if ($storageKind != '') {
			$this->setStorageKind($storageKind);
		}
		$this->_init();
		if ($id > 0) {
			$this->load($id);
		}
	}

	/**
	 * For extension
	 */
	public function _init() {
	}

	public function load($id=-1) {
		$mapper = Lec_Resource_Loader::loadMapper();
		$mapper->load($this, $id);
	}

	public function save() {
		$mapper = Lec_Resource_Loader::loadMapper();
		$mapper->save($this);
		if ($this->__searchable) {
			$indexer = Lec_Resource_Loader::loadIndexer();
			$indexer->indexAdd($this, $this->getStorageId());
		}
	}

	public function preSave() {
	}

	public function postSave() {
	}

	public function preLoad($id=-1) {
	}

	public function postLoad($id=-1) {
	}

	/**
	 * Return the configured storage kind or this object's class name.
	 *
	 * With SQL relational drivers, this value will be the 
	 * table name.  With non SQL drivers, this value might
	 * be the supercolumn for this data.
	 */
	public function getStorageKind() {
		if ($this->__tableName == '') {
			return strtolower(get_class($this));
		}
		return $this->__tableName;
	}

	/**
	 * Set a configured storage kind.
	 *
	 * With SQL relational drivers, this value will be the 
	 * table name.  With non SQL drivers, this value might
	 * be the supercolumn for this data.
	 */
	public function setStorageKind($storageKind) {
		$this->__tableName = $storageKind;
	}

	/**
	 * return this object's "id"
	 */
	public function getStorageId() {
		return $this->__id;
	}

	/**
	 * set this object's "id"
	 */
	public function setStorageId($id) {
		$this->__id = $id;
	}

	/**
	 * Return a list of storable attributes and values
	 *
	 * @return Array  list of attributes 
	 */
	public function getStorable() {
		$vars = get_object_vars($this);
		$storable = array();
		foreach ($vars as $k=>$v) {
			if (substr($k,0,1) == '_') { continue; }
			$storable[$k] = $v;
		}
		return $storable;
	}
}
