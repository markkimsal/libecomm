<?php
require_once(dirname(__FILE__).'/../../../src/lib/Lec/Resource/Model.php');
require_once(dirname(__FILE__).'/../../../src/lib/Lec/Resource/Loader.php');
require_once(PHM_LIB_DIR.'phemto.php');


class CassandraTest extends UnitTestCase {

	public function testGiveUuidAfterSave() {
		$y = new Lec_Resource_Model('Standard1');
		$y->foo = 'bar';
		global $cashost;

		$storageReadOpts  = array(
			'host'=>$cashost); 

		Lec_Resource_Loader::getDi();
		Lec_Resource_Loader::setStorageDriver('Lec_Resource_Driver_Storage_Cassandra', 
				$storageReadOpts);

		$newId = $y->save();

		$this->assertTrue($y->getStorageId() > 0);
		$this->assertEqual($newId, $y->getStorageId());
	}
}

