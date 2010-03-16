<?php

class MysqlTest extends UnitTestCase {

	public function testGiveUuidAfterSave() {
		$y = new Lec_Resource_Model('Standard1');
		$y->foo = 'bar';

//		$db = new Lec_Resource_Driver_Storage_Mysql();
		Lec_Resource_Loader::$storageReadOpts  = array('host'=>'localhost', 'database'=>'biz_joomla', 'user'=>'root', 'password'=>'mysql');
		$y->save();
		var_dump($y->getStorageId());
	}
}
