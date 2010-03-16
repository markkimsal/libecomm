<?php
require_once(dirname(__FILE__).'/../../src/lib/Lec/Resource/Model.php');

class ModelsTest extends UnitTestCase {

	public function testTableName() {
		$y = new Lec_Resource_Model('foobar');
		$this->assertEquals('foobar', $y->getStorageKind());
	}
}
