<?php


$dir = dirname(__FILE__);
if (!defined('BASE_DIR')) {
	throw new Exception('Required define "BASE_DIR" not found.');
}
chdir(BASE_DIR);
if (! include(BASE_DIR.'./boot/bootstrap.php') ) {
	if (! include(BASE_DIR.'../boot/bootstrap.php') ) {
		include('bootstrap.php');
	}
}

require_once(CGN_LIB_PATH.'/lib_cgn_data_item.php');

class Cgn_Data_Item_Bridge extends Lec_Resource_Driver_Storage_Default {
	/* implements Lec_Resource_Driver_Storage { */

	public $_debugSql = false;

	/**
	 * Insert or update
	 *
	 * @return mixed FALSE on failure, integer primary key on success
	 */
	public function save($object) {
		//$db = Cgn_DbWrapper::getHandle($object->getStorageKind());
		$bridge = new Cgn_DataItem($object->getStorageKind());
		$bridge->setPrimaryKey( $object->getStorageId() );
		$bridge->_isNew = $object->__isNew;
		$bridge->_typeMap = $object->__typeMap;
		$storableVars = $object->getStorable();
		foreach ($storableVars as $k => $v) {
			$bridge->set($k, $v);
		}
		if ($object->getStorageId() < 1 ) {
			//cognifty's default data_item value is null, but LEC is -1
			$bridge->setPrimaryKey(NULL);
		}


		$id = $bridge->save();
		$object->setStorageId($id);
		$object->__isNew = $bridge->_isNew;
		return $object->getStorageId();
	}

	public function load($object, $id) {
		$bridge = new Cgn_DataItem($object->getStorageKind(), $id);
		$loadedVars = get_object_vars($bridge);
		foreach ($loadedVars as $k =>$v) {
			$bridge->set($k, $v);
		}
	}

	public function find($object, &$list, $where, $limit=null) {
		die('jsdf');
	}

	public function update($object, $id){ die('sdjf');
	}

	public function getStorageKind($object) {
		return $object->getStorageKind();
	}

	public function getIdName($object) {die('sdjf');
	}

	public function createNew($object) {die('sdjf');
	}
}
