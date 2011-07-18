<?php
require_once(dirname(__FILE__).'/../../src/lib/Lec/Txn/Manager.php');
require_once(dirname(__FILE__).'/../../src/lib/Lec/Txn/Exception.php');
require_once(dirname(__FILE__).'/../../src/lib/Lec/Txn/Plugin/FakePayment.php');
require_once(dirname(__FILE__).'/../../src/lib/Lec/Txn/Plugin/ErrorShipment.php');
require_once(dirname(__FILE__).'/../../src/lib/Lec/Order/Model.php');

class TxnTest extends UnitTestCase {

	public function test_ManagerCanProcessOrders() {

		$order = new Lec_Order_Model_Default();
		$bool  = Lec_Txn_Manager::canProcessOrder($order);
		$this->assertEquals(TRUE, $bool);


		$defaultPlugin = new Lec_Txn_Plugin_FakePayment();
		Lec_Txn_Manager::addPlugin($defaultPlugin);

		$order->_typeList[] = 'test';
		$bool  = Lec_Txn_Manager::canProcessOrder($order);
		$this->assertEquals(TRUE, $bool);

		Lec_Txn_Manager::process($order);
		$this->assertTrue( $order->inPhase('paid') );
	}

	/**
	 * Txn Manager should return a failed order
	 */
	public function test_ManagerFailsOrders() {

		Lec_Txn_Manager::clearPlugins();
		$order = new Lec_Order_Model_Default();
		$defaultPlugin = new Lec_Txn_Plugin_ErrorShipment();
		Lec_Txn_Manager::addPlugin($defaultPlugin);

		$order->_typeList[] = 'test';
		$errorList = Lec_Txn_Manager::process($order);
		$this->assertEqual( 1, count($errorList));
	}

	/*
	public function test_TooManyOrderTypesIsOk() {

		Lec_Txn_Manager::clearPlugins();
		$order = new Lec_Order_Model_Default();
		$order->_typeList[] = 'flooring';
		$order->_typeList[] = 'physical';
		$defaultPlugin = new Lec_Txn_Plugin_FakePayment();
		Lec_Txn_Manager::addPlugin($defaultPlugin);

		$bool  = Lec_Txn_Manager::canProcessOrder($order);
		$this->assertEquals(TRUE, $bool);

		$errorList = Lec_Txn_Manager::process($order);
		$this->assertEqual( 0, count($errorList));
	}
	 */
}
