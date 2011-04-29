<?php

class Lec_Txn_Plugin_ErrorShipment {


	public function canHandleType($type) {
		return true;
	}

	/**
	 * Return 0 if everything works, else return an error object.
	 */
	public function visit($order) {
		return "Cannot process shipment";
	}
}
