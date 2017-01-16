<?php

namespace OUFabric\OUCommon;

class URICache {
	
	public $cacheExp = 86400;
	public $key;
	private $db;

	private static $_cache = [];
	
	public function __construct( $uri ) {
		$this->key = md5( $uri );
	}
	
	public function get() {
		// Internal caching to prevent multiple calls to the database
		if ( array_key_exists( $this->key, self::$_cache ) ) {
			return self::$_cache[ $this->key ];
		}

		// Check to see if a valid cache exists for the URL
		$q = OUCommon::$db->prepare( "SELECT `data`, `timestamp` FROM `uri-cache` WHERE `key` = ? LIMIT 1" );
		$q->execute( array( $this->key ) );
		
		if ( $results = $q->fetch() ) {
			// If a valid cache exists then return it
			if ( ( $results['timestamp'] + $this->cacheExp ) >= date("U") ) {
				self::$_cache[ $this->key ] = base64_decode( $results['data'] );
				return self::$_cache[ $this->key ];
			}
		}
		
		// If no cache exists or it has expired then return false
		return false;
	}
	
	public function set( $data ) {
		// Delete any existing cache for this URL
		$q = OUCommon::$db->prepare( "DELETE FROM `uri-cache` WHERE `key` = ? LIMIT 1" );
		$q->execute( array( $this->key ) );
				
		// Insert the new cache data for this URL
		$q = OUCommon::$db->prepare( "INSERT INTO `uri-cache` (`key`, `data`, `timestamp`) VALUES (?, ?, ?)" );
		$q->execute( array(
			$this->key,
			base64_encode( $data ),
			date("U") 
		) );
		
		return $data;
	}

	private function getSessionCache( $key ) {
		return ( isset( $_SESSION[ 'OUCommonURICache' ][ $key ] ) ) ? $_SESSION[ 'OUCommonURICache' ][ $key ] : FALSE;
	}

	private function setSessionCache( $key, $data ) {
		$_SESSION[ 'OUCommonURICache' ][ $key ] = $data;
		return $data;
	}
		
}

// EOF