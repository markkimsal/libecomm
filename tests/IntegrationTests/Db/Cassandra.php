<?php
define('LEC_LIB_DIR', '../src/lib/Lec/');
define('PHM_LIB_DIR', '../src/lib/Phemto/');
define('TRF_LIB_DIR', '../src/lib/Thrift/');
require_once(dirname(__FILE__).'/../../../src/lib/Lec/Resource/Model.php');
require_once(dirname(__FILE__).'/../../../src/lib/Lec/Resource/Loader.php');
require (PHM_LIB_DIR.'phemto.php');


class CassandraTest extends UnitTestCase {

	public function testGiveUuidAfterSave() {
		$y = new Lec_Resource_Model('Standard1');
		$y->foo = 'bar';

//		$db = new Lec_Resource_Driver_Storage_Cassandra();
		Lec_Resource_Loader::$storageReadOpts  = array('host'=>'localhost', 'database'=>'biz_joomla', 'user'=>'root', 'password'=>'mysql');
		$y->save();

		var_dump($y->getStorageId());
	}
}

function lec_setting($key) {
	if ($key == 'res_storage_driver') {
		require_once('3rdparty/cognifty/Cgn_Db_Mysql_Bridge.php');
		return 'Cgn_Db_Mysql_Bridge';
		return 'Lec_Resource_Driver_Storage_Mysql';
		//return 'Lec_Resource_Driver_Storage_Cassandra';
	}
	if ($key == 'res_search_driver') {
		//return 'Lec_Resource_Search_Driver_Cassandra';
		return 'Lec_Resource_Driver_Search_Dummy';
	}
}

