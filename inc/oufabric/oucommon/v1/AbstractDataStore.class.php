<?php

namespace OUFabric\OUCommon;

abstract class AbstractDataStore {
	private $keyBase;
	private $hashKey;

	public function __construct( $keyBase = NULL, $hashKey = FALSE ) {
		switch ( gettype($keyBase) ) {
			case 'object':
				$this->keyBase = get_class( $keyBase );
				break;

			case 'string':
				$this->keyBase = $keyBase;
				break;
			
			default:
				$this->keyBase = get_class();
				break;
		}
		$this->hashKey = $hashKey;
	}

	abstract protected function getTable();
	
	public function get( $key, $cacheExp = NULL ) {
		// let's check the key
		$key = $this->prepareKey( $key );

		// Check to see if a valid cache exists for the URL
		$q = OUCommon::$db->prepare( "SELECT `data`, `timestamp` FROM `" . $this->getTable() . "` WHERE `key` = ? LIMIT 1" );
		$q->execute( array( $key ) );
		
		if ( $results = $q->fetch() ) {
			// If a valid cache exists then return it
			if ( !$cacheExp || ( $results['timestamp'] + $cacheExp ) >= date("U") ) return $this->unserializeData( $results['data'] );
		}
		
		// If no cache exists or it has expired then return FALSE
		return FALSE;
	}
	
	public function set( $key, $data ) {
		// let's check the key
		$key = $this->prepareKey( $key );

		// let's prepare the data
		$data = $this->serializeData( $data );

		// Delete any existing data for this key
		$q = OUCommon::$db->prepare( "DELETE FROM `" . $this->getTable() . "` WHERE `key` = ?" );
		$q->execute( array( $key ) );
				
		// Insert the new value data for this URL
		$q = OUCommon::$db->prepare( "INSERT INTO `" . $this->getTable() . "` (`key`, `data`, `timestamp`) VALUES (?, ?, ?)" );
		$q->execute( array(
			$key,
			$data,
			date("U") 
		) );
		
		return $data;
	}

	// A little sprinkling of magic for direct property access
	public function __get( $key ) {
		return $this->get( $key ); 
	}

	public function __set( $key, $data ) {
		return $this->set( $key, $data );
	}

	private function prepareKey( $key ) {
		// if it's an array of values then we'll implode them
		// TODO: Error handling for complex values within the array
		if ( is_array($key) ) $key = implode(':', $key);

		// should we hash this total key, as it may contain sensitive information
		if ( $this->hashKey ) $key = sha1($key);

		// if we have a base key defined, then use this. The default is the class name.
		if ( $this->keyBase ) $key = $this->keyBase . ':' . $key;
		
		return $key;
	}

	private function serializeData( $data ) {
		return serialize( $data );
	}

	private function unserializeData( $data ) {
		return unserialize( $data );
	}

}

// EOF