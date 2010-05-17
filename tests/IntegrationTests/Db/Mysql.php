<?php
require_once('teststrap.php');

class MysqlTest extends UnitTestCase {

	public function testGiveUuidAfterSave() {
		$y = new Lec_Resource_Model('Standard1');
		$y->foo = 'bar';
		global $myhost, $mypass, $myuser, $mydb;

		$storageReadOpts  = array(
			'host'=>$myhost,
			'database'=>$mydb,
			'user'=>$myuser,
			'password'=>$mypass);

		Lec_Resource_Loader::getDi();

		//Lec_Resource_Loader::setStorageDriver('Lec_Resource_Driver_Storage_Mysql', 
		//		$storageReadOpts);
		require_once(TRD_SRC_DIR.'/cognifty/Cgn_Data_Item_Bridge.php');
		Lec_Resource_Loader::setStorageDriver('Cgn_Data_Item_Bridge', 
				$storageReadOpts);
	
		$newId = $y->save();

		$this->assertTrue($y->getStorageId() > 0);
		$this->assertEqual($newId, $y->getStorageId());
	}
}
