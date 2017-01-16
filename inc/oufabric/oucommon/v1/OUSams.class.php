<?php

namespace OUFabric\OUCommon;

class OUSams {
		
	public static function setSpoofData( $data ) {
		foreach ( $data AS $k => $v ) {
			switch ( $k ) {
				case 'displayName':
					$_COOKIE['HS7BDF'] = $v;
					break;
				case 'studentPi':
					$_SERVER['HTTP_SAMS_STUDENTPI'] = $v;
					break;
				case 'staffId':
					$_SERVER['HTTP_SAMS_STAFFID'] = $v;
					break;
				case 'tutorId':
					$_SERVER['HTTP_SAMS_TUTORID'] = $v;
					break;
				case 'visitorId':
					$_SERVER['HTTP_SAMS_VISITORID'] = $v;
					break;
				case 'oucu':
					$_SERVER['HTTP_SAMS_USER'] = $v;
					break;
				case 'authIds':
					$_SERVER['HTTP_SAMS_USER_AUTHIDS'] = $v;
					break;
				case 'ioeMenu':
					$_COOKIE['IOEMENU'] = $v;
					break;
			}
		}
	}

	public static function clearSpoofData()
	{
		unset($_COOKIE['HS7BDF']);
		unset($_SERVER['HTTP_SAMS_STUDENTPI']);
		unset($_SERVER['HTTP_SAMS_STAFFID']);
		unset($_SERVER['HTTP_SAMS_TUTORID']);
		unset($_SERVER['HTTP_SAMS_VISITORID']);
		unset($_SERVER['HTTP_SAMS_USER']);
		unset($_SERVER['HTTP_SAMS_USER_AUTHIDS']);
		unset($_COOKIE['IOEMENU']);
	}
	
	public static function authenticate( $whitelist = array() ) {
		if ( count( $whitelist ) == 0 ) {
			$whitelist[] = '*';
		}
		
		if ( isset( $whitelist[0] ) && strtolower( trim( $whitelist[0] ) ) == 'off' ) {
			return;
		}
		
		if ( ! isset( $_SERVER['HTTP_SAMS_USER'] ) ) {
			header( 'Location: ' . self::getLoginURL() );
			exit();
		}
		
		foreach ( $whitelist AS $k => $v ) {
			$whitelist[ $k ] = '_' . trim( $v );
		}
		
		$authIdsStr = ( isset( $_SERVER['HTTP_SAMS_USER_AUTHIDS'] ) ) ? $_SERVER['HTTP_SAMS_USER_AUTHIDS'] : '';
		$authIds = array_filter( explode( '|', $authIdsStr ) );
		
		$authIds[] = '*';
		
		foreach ( $authIds AS $k => $v ) {
			$authIds[ $k ] = '_' . trim( $v );
		}
		
		$accessAllowed = FALSE;
		
		foreach ( $authIds AS $authId ) {
			if ( in_array( $authId, $whitelist ) ) {
				$accessAllowed = TRUE;
				break;
			}
		}
		
		if ( $accessAllowed ) {
			return TRUE;
		} else {
			header( 'Location: ' . self::getDeniedURL() );
			exit();
		}
	}
	
	private static function getDeniedURL() {
		return 'https://' . self::getURLSubdomain() . '.open.ac.uk/signon/errorHandler/?CODE=E02&URL=' . OUCommon::getCurrentURL();
	}
	
	private static function getLoginURL() {
		return 'https://' . self::getURLSubdomain() . '.open.ac.uk/signon/SAMSDefault/SAMS001_Default.aspx?URL=' . OUCommon::getCurrentURL();
	}
	
	private static function getURLSubdomain() {
		$urlSubdomain = '';
		
		switch ( ENVIRONMENT ) {
			case 'live':
			case 'test':
				$urlSubdomain = 'msds';
				break;
			case 'dev':
				$urlSubdomain = 'msds-acct';
				break;
		}
		
		return $urlSubdomain;
	}
	
}

// EOF