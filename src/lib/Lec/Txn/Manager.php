<?php

class Lec_Txn_Manager {

	public static $plugins = array();

	/**
	 * Checks to see if the order can be processed
	 * @void
	 * @throws Lec_Txn_Exception when an order cannot be processed
	 */
	public static function process($order) {

		$able = Lec_Txn_Manager::canProcessOrder($order);
		if (!$able) {
			throw new Lec_Txn_Exception_UnableToProcess();
		}

		$errorList = Lec_Txn_Manager::_doProcess($order);
		return $errorList;
	}

	/**
	 * Call "accept" on every plugin which can handle an order type
	 *
	 * @return Array a list of errors that occured in processing.
	 */
	public static function _doProcess($order) {
		$oTypes = $order->getTypes();
		$errorList = array();
		foreach ($oTypes as $_type) {
			foreach (Lec_Txn_Manager::$plugins as $_plug) {
				if ($_plug->canHandleType($_type)) {
					if ($err = $order->accept($_plug)) {
						$errorList[] = $err;
					}
				}
			}
		}
		return $errorList;
	}

	/**
	 * Ensure that we have plugins which can handle all the types of
	 * this order
	 */
	public static function canProcessOrder($order) {

		$oTypes = $order->getTypes();
		//assume we can't handle
		

		$canHandle = array();
		foreach ($oTypes as $_type) {
			foreach (Lec_Txn_Manager::$plugins as $_plug) {
				if ($_plug->canHandleType($_type)) {
					$canHandle[$_type] = true;
					continue;
				}
			}
		}
		if (count($canHandle) == count($oTypes)) {
			return TRUE;
		}
		return FALSE;
	}

	public static function addPlugin($plugin) {
		Lec_Txn_Manager::$plugins[] = $plugin;
	}
}
