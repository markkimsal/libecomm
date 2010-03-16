<?php

/**
 * The storage driver needs to interface Lec Models and 
 * external storage requirements.  It needs to know how
 * both sides work.  It has methods that work with 
 * each face.
 */
interface Lec_Resource_Driver_Storage {

	public function __construct($driverReadOpts, $driverWriteOpts);

	public function save($object);

	public function load($object, $id);

	public function find($object, &$list, $where, $limit=null);

	public function update($object, $id);

	public function getStorageKind($object);

	public function getIdName($object);

	public function createNew($object);

	public function setDriverReadOpts($opts);

	public function setDriverWriteOpts($opts);
}

/**
 * Implement standard behaviors between any driver and 
 * the object, but not driver specific ones
 */
abstract class Lec_Resource_Driver_Storage_Default implements Lec_Resource_Driver_Storage {
	protected $driverReadOpts  = array();
	protected $driverWriteOpts = array();

	/*
	abstract public function save($object);

	abstract public function load($object, $id);

	abstract public function find($object, &$list, $where, $limit=null);

	abstract public function update($object, $id);
	 */

	public function __construct($driverReadOpts, $driverWriteOpts) {
		if (count($driverWriteOpts) == 0) {
			$driverWriteOpts = $driverReadOpts;
		}
		$this->setDriverReadOpts($driverReadOpts);
		$this->setDriverWriteOpts($driverWriteOpts);
	}

	public function getStorageKind($object) {
		return $object->getStorageKind();
	}

	public function getIdName($object) {
		return $object->getStorageKind().'_id';
	}

	public function createNew($object) {
		$c = get_class($object);
		return new $c(); 
	}

	public function setDriverReadOpts($opts) {
		$this->driverReadOpts  = $opts;
	}

	public function setDriverWriteOpts($opts) {
		$this->driverWriteOpts = $opts;
	}
}


/**
 * Just echo statements
 */
class Lec_Resource_Driver_Storage_Dummy extends Lec_Resource_Driver_Storage_Default {

	public function save($object) {
		echo "[Storage-Dummy]: Will save object of type ".$this->getStorageKind($object).".\n";
	}

	public function load($object, $id) {
		echo "[Storage-Dummy]: Will load object of type ".$this->getStorageKind($object)." and ID ".$id.".\n";
	}

	public function find($object, &$list, $where, $limit=null) {
		echo "[Storage-Dummy]: Will load a list of objects of type ".$this->getStorageKind($object)." where ".$where;
		if (is_array($limit)) {
			echo " and limiting to ".$limit['count']." results";
		}
		echo ".\n";
	}

	public function update($object, $id) {
		echo "[Storage-Dummy]: Will save an existing  object of type ".$this->getStorageKind($object).".\n ";
	}
}


/**
 * Save to Mysql
 */
class Lec_Resource_Driver_Storage_Mysql extends Lec_Resource_Driver_Storage_Default {

	protected $readRes  = null;
	protected $writeRes = null;

	public function __construct($driverReadOpts, $driverWriteOpts) {
		parent::__construct($driverReadOpts, $driverWriteOpts);
		try {
			$ro = $this->driverReadOpts;
			// Make a connection to the Thrift interface to Cassandra
			$this->writeRes = mysql_connect($ro['host'], $ro['user'], $ro['password']);
			/*
			$this->transport = new TBufferedTransport($socket, 1024, 1024);
			$protocol = new TBinaryProtocolAccelerated($this->transport);
			$this->client = new CassandraClient($protocol);
			$this->transport->open();
			 */
		} catch(Exception $e) {
			//eliminate any NPEs
		}
	}


	public function save($object) {

		$statementString = 'INSERT INTO `'.$this->getStorageKind($object).'` 
			(`'.$this->getIdName($object).'`)
			VALUES '.$object->getStorageId().')';
		$rst = mysql_query($statementString, $this->writeRes);

		$object->setStorageId(mysql_insert_id($this->writeRes));
		return $rst;

		//instead of returning raw results from raw php drivers, 
		//the result should be mapped to a consistent EVR_STORAGE_RESULT_GOOD/BAD
		//		return $this->mapResult($rst);
	}

	public function load($object, $id) {
		echo "[Storage-Mysql]: Will load object of type ".$this->getStorageKind($object)." and ID ".$id.".\n";
	}

	public function find($object, &$list, $where, $limit=null) {
		echo "[Storage-Mysql]: Will load a list of objects of type ".$this->getStorageKind($object)." where ".$where;
		if (is_array($limit)) {
			echo " and limiting to ".$limit['count']." results";
		}
		echo ".\n";
	}

	public function update($object, $id) {
		echo "[Storage-Mysql]: Will save an existing  object of type ".$this->getStorageKind($object).".\n ";
	}
}


/**
 * Use prepared statements
 */
class Lec_Resource_Driver_Storage_PdoMysql extends Lec_Resource_Driver_Storage_Mysql {
}


/**
 * Save to Mysql
 */
class Lec_Resource_Driver_Storage_Cassandra extends Lec_Resource_Driver_Storage_Default {

	protected $readRes   = null;
	protected $writeRes  = null;
	protected $transport = null;
	protected $client    = null;

	public function __construct($driverReadOpts, $driverWriteOpts) {
		parent::__construct($driverReadOpts, $driverWriteOpts);
		Lec_Resource_Driver_Storage_Cassandra::init();
		try {
			// Make a connection to the Thrift interface to Cassandra
			$socket = new TSocket($this->driverReadOpts['host'], 9160);
			$this->transport = new TBufferedTransport($socket, 1024, 1024);
			$protocol = new TBinaryProtocolAccelerated($this->transport);
			$this->client = new CassandraClient($protocol);
			$this->transport->open();
		} catch(Exception $e) {
			//eliminate any NPEs
			$this->client = new CassandraClient(NULL);
		}
	}

	public static function init() {
		//define THRIFT
		require_once TRF_LIB_DIR.'/packages/cassandra/Cassandra.php';
		require_once TRF_LIB_DIR.'/packages/cassandra/cassandra_types.php';
		require_once TRF_LIB_DIR.'/transport/TSocket.php';
		require_once TRF_LIB_DIR.'/protocol/TBinaryProtocol.php';
		require_once TRF_LIB_DIR.'/transport/TFramedTransport.php';
		require_once TRF_LIB_DIR.'/transport/TBufferedTransport.php';
	}

	public function save($object, $timestamp='', $consistency_level='') {
		if ($timestamp===''){
			$timestamp=time();
		}
		if ($consistency_level==='') {
			$consistency_level = cassandra_ConsistencyLevel::ONE;
		}
		$storable = $object->getStorable();
		$key = $object->getStorageId();
		if ($key == null || $key == -1) {
			$key = Lec_Resource_Driver_Storage_Cassandra::gen_uuid();
		}

		// build super column containing the columns
		$super_column = new cassandra_SuperColumn();
		$super_column->name = $object->getStorageKind() .'_attribs';

		foreach ($storable as $k => $v) {
			// build columns to insert
			$column = new cassandra_Column();
			$column->name = $k;
			$column->value = $v;
			$column->timestamp = $timestamp;
			$super_column->columns[] = $column;
		}

		// create columnorsupercolumn holder class that batch_insert uses
		$c_or_sc = new cassandra_ColumnOrSuperColumn();
		$c_or_sc->super_column = $super_column;

		// create the mutation (a map of ColumnFamily names to lists ColumnsOrSuperColumns objects
		$mutation["Super1"] = array($c_or_sc);
		$this->client->send_batch_insert($this->driverWriteOpts['database'], $key, $mutation, $consistency_level);
		$this->client->recv_batch_insert();
		$object->setStorageId($key);
	}

	public function load($object, $id) {
	}

	public function find($object, &$list, $where, $limit=null) {
	}

	public function update($object, $id) {
	}

	/**
	 * Universal Unique ID (UUID or GUID)
	 * Taken from http://us2.php.net/manual/en/function.uniqid.php
	 * Thanks to all the comment posters, including:
	 * maciej dot strzelecki, dholmes, and mimic
	 */
	public static function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) 
		);
	}
}
