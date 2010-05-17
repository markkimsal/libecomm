<?php

/**
 * Dependency Injector for ORM
 */
class Lec_Resource_Loader {

	static $phemto                = null;
	static $driverMap             = array();
	static $searchReadOpts        = array();
	static $searchWriteOpts       = array();
	static $storageReadOpts       = array();
	static $storageWriteOpts      = array();
	static $drivers               = array('storage'=>'', 'search'=>'');

	/**
	 * Load a configured ORM 
	 */
	public static function loadMapper() {
		$phemto = self::getDi();
		return $phemto->create('Lec_Resource_Driver_Storage', self::$storageReadOpts, self::$storageReadOpts);
	}

	/**
	 * Load a configured ORM for a specific data model
	 */
	public static function loadMapperFor($model='') {
		$phemto = self::getDi();
		if (isset (self::$driverMap[$model])) {
			return $phemto->create(self::$driverMap[$model], self::$storageReadOpts, self::$storageReadOpts);
		}
		return $phemto->create('Lec_Resource_Driver_Storage', self::$storageReadOpts, self::$storageReadOpts);
	}

	/**
	 * Set a resource class name for a module type
	 */
	public static function useResourceWhenLoading($model, $resClassName) {
		$phemto = self::getDi();
		$phemto->willUse($resClassName);
		self::$driverMap[$model] = $resClassName;
	}

	/**
	 * Load a configured search indexer
	 */
	public static function loadIndexer() {
		$phemto = self::getDi();
		return $phemto->create('Lec_Resource_Driver_Search', self::$searchReadOpts, self::$searchReadOpts);
	}


	public static function getDi() {
		if (Lec_Resource_Loader::$phemto === NULL) {
			Lec_Resource_Loader::$phemto = new Phemto();
			Lec_Resource_Loader::buildPhemto();
		}
		return Lec_Resource_Loader::$phemto;
	}

	/**
	 * Apply a configuration file to the Dependency Injector
	 */
	public static function buildPhemto() {
		if (!class_exists('Phemto')) {
			throw new Exception('Phemto library not loaded.');
		}
		//run through a configuration file to 
		//apply classnames to DI
		require (LEC_LIB_DIR.'Resource/Driver/Storage.php');
		require (LEC_LIB_DIR.'Resource/Driver/Search.php');

		//scope
		$d = Lec_Resource_Loader::$drivers;
		if ($d['storage'] == '') {
			if (function_exists('lec_setting')) {
				$storageDriver = lec_setting('res_storage_driver');
			} else {
				//hardcoded examples
				$storageDriver = 'Lec_Resource_Driver_Storage_Dummy';
			}
		}
		if ($d['search'] == '') {
			if (function_exists('lec_setting')) {
				$searchDriver  = lec_setting('res_search_driver');
			} else {
				$searchDriver  = 'Lec_Resource_Driver_Search_Dummy';
			}
		}
		Lec_Resource_Loader::setStorageDriver($storageDriver, self::$storageReadOpts, self::$storageWriteOpts);
		Lec_Resource_Loader::setSearchDriver($searchDriver);
	}

	public static function setStorageDriver($driver, $readOpts='', $writeOpts='') {
		Lec_Resource_Loader::$drivers['storage'] = $driver;

		$p = Lec_Resource_Loader::$phemto;
		if ($readOpts == '') {
			$readOpts = self::$storageReadOpts;
		}
		if ($writeOpts == '') {
			$writeOpts = self::$storageWriteOpts;
		}
		$p->willUse(new Reused($driver));
		$p->fill('driverReadOpts', 'driverWriteOpts')->with($readOpts, $writeOpts);
	}

	public static function setSearchDriver($driver, $readOpts='', $writeOpts='') {
		Lec_Resource_Loader::$drivers['search'] = $driver;

		$p = Lec_Resource_Loader::$phemto;
		if ($readOpts == '') {
			$readOpts = self::$storageReadOpts;
		}
		if ($writeOpts == '') {
			$writeOpts = self::$storageWriteOpts;
		}
		$p->willUse(new Reused($driver));
		$p->fill('driverReadOpts', 'driverWriteOpts')->with($readOpts, $writeOpts);
	}


		/*
$p->whenCreating('Lec_Resource_Driver_Storage')->forVariable('driverReadOpts')->willUse(new Value('path/to/templatedir'));
$p->whenCreating('Lec_Resource_Driver_Storage_Cassandra')->forVariable('driverReadOpts')->willUse(new Value('path/to/templatedir'));
		 */

		/*
		$p->whenCreating('Lec_Resource_Driver_Storage_Cassandra')
			->forVariable('driverReadOpts')
			->willUse(new Value( self::$storageReadOpts));

		$p->whenCreating('Lec_Resource_Driver_Storage_Cassandra')
			->forVariable('driverWriteOpts')
			->willUse(new Value( self::$storageWriteOpts));
		 */

		/*
		$p->whenCreating($searchDriver)
			->forVariable('driverReadOpts')
			->willUse(new Value( self::$searchReadOpts));

		$p->whenCreating($searchDriver)
			->forVariable('driverWriteOpts')
			->willUse(new Value( self::$searchWriteOpts));
		 */
}
