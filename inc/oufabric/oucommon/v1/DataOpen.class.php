<?php

namespace OUFabric\OUCommon;

class DataOpen {
	
	const DATA_URL = 'http://data.open.ac.uk/';
	const QUERY_PATH = 'sparql?query=';
	
	public static function query( $query ) {
		return self::getURI( $query, TRUE );
	}

	public static function uri( $uri ) {
		return self::getURI( $uri, FALSE );
	}

	private static function getURI( $data, $query = FALSE ) {
		if ( ! $query && preg_match( '#^https?://.*$#', $data ) ) {
			$url = $data;
		} else {
			if ( $query ) {
				$url = self::DATA_URL . self::QUERY_PATH . urlencode( $data );
			} else {
				$url = self::DATA_URL . ltrim( $data, '/' );
			}
		}
		
		// Cache the data from data.open.ac.uk locally to improve reliability and performance
		$cache = new URICache( $url );
		
		if ( $cacheData = $cache->get() ) {
			return $cacheData;
		}
		else {
			$requestTimeout = ( $query ) ? 300 : 3;
			
			$headers = array();
			
			if ( ! $query ) {
				$headers[] = "Accept: application/rdf+json";
			} else {
				if ( preg_match( "#^\s*(?:describe|construct)#i", $data ) ) {
					$headers[] = "Accept: application/rdf+json";
				} else {
					$headers[] = "Accept: application/sparql-results+json";
				}
			}
			
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_USERAGENT, "SSLDT-OUGRAPH" );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, $requestTimeout );
			$response = curl_exec( $ch );
			curl_close( $ch );
			
			if ( $response ) {
				// Cache the response
				return $cache->set( $response );
			}
		}
		
		return FALSE;
	}
		
}

// EOF