<?php

namespace OUFabric\OUCommon\Models;

use Exception;
use ReflectionClass;

abstract class AbstractModel {
	
	/**
	 * Gets an array of allowed values for the given property, by looking for constants in the class prefixed with PROPNAME_
	 * 
	 * e.g. $type = TYPE_VAL1, TYPE_VAL2
	 * or
	 * $status = STATUS_VAL, STATUS_ANOTHERVAL
	 * 
	 * @param string $propName
	 * 
	 * @return array
	 */
	public static function getPropertyAllowedValues( $propName ) {
		$class = new ReflectionClass( get_called_class() );
		
		try {
			$property = $class->getProperty( $propName );
		} catch ( Exception $e ) {
			$getterName = 'get' . ucfirst( strtolower( $propName ) );
			
			try {
				$class->getMethod( $getterName );
			} catch ( Exception $e ) {
				return [];
			}
		}
		
		$constantPrefix = strtoupper( $propName ) . '_';
		
		$constants = $class->getConstants();
		
		$vals = [];
		
		foreach ( $constants AS $k => $v ) {
			if ( substr( $k, 0, strlen( $constantPrefix ) ) == $constantPrefix ) {
				$vals[ $k ] = $v;
			}
		}
		
		return $vals;
	}
	
}

// EOF;