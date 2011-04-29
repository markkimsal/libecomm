<?php

class Lec_Txn_Plugin_FakePayment {


	public function canHandleType($type) {
		return true;
	}

	/**
	 * Return 0 if everything works, else return an error object.
	 */
	public function visit($order) {

		$order->setPhase('paid');
		return 0;
	}
}
