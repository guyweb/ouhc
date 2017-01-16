<?php

namespace OUFabric\OUCommon;

class OUCommon {

	const ENV_LIVE 	= 'live';
	const ENV_TEST 	= 'test';
	const ENV_DEV 	= 'dev';
	
	const SESS_ID_KEY = 'oucommon-session-id';
	
	/**
	 * @var \PDO
	 */
	public static $db;
	public static $isOUServer = TRUE;
	
	public static function init() {
		self::setEnvironment();
		
		if ( ENVIRONMENT == self::ENV_DEV && ! self::$isOUServer ) {
			$spoofFile = OUCOMMON_PATH_ROOT . '/_config/sams.json';
			
			if ( file_exists( $spoofFile ) ) {
				$spoofData = (array) json_decode( file_get_contents( $spoofFile ) );
				OUSams::setSpoofData( $spoofData );
			}
		}
		
		$db = new Database();
		self::$db = $db->connect();
	}
	
	public static function generateUuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
				
			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),
	
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,
				
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,
	
			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	
	public static function getSessionId() {
		if ( ! isset( $_SESSION[ self::SESS_ID_KEY ] ) ) {
			$_SESSION[ self::SESS_ID_KEY ] = self::generateUuid();
		}
		
		return $_SESSION[ self::SESS_ID_KEY ];
	}
	
	public static function writeLog( $logName, $logDir, $logData ) {
		// Log file
		$logFolder = $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/_data/_private/logs/' . $logDir . '/';
		$logFile = $logName . '.log';
		$logData = date( 'd-M-Y H:i:s' ) . ' | ' . $logData . "\n";
		
		// If the log folder does not exists, create it
		if ( ! is_dir( $logFolder ) ) mkdir( $logFolder, 0777, TRUE );
		
		// Append the log file (create if it does not exist)
		return ( file_put_contents( $logFolder . $logFile, $logData, FILE_APPEND ) === TRUE );
	}
	
	public static function getCurrentURL() {
		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public static function getLoginUrl() {
		switch ( ENVIRONMENT ) {
			case self::ENV_DEV:
				$env = 'http://msds-acct';
				break;
			default:
				$env = 'https://msds';
				break;
		}

		return $env . '.open.ac.uk/signon/SAMSDefault/SAMS001_Default.aspx?URL=';
	}
	
	private static function setEnvironment() {
		if ( defined( 'ENVIRONMENT' ) ) {
			return FALSE;
		}
		
		switch ( getenv('OUENV') ) {
			case "LIVE":			
				define( 'ENVIRONMENT', self::ENV_LIVE );										
				break;
			
			case "ACCT":			
				define( 'ENVIRONMENT', self::ENV_TEST );										
				break;
			
			case "TEST";			
			default:
				define( 'ENVIRONMENT', self::ENV_DEV );												
				break;
		}
		
		self::$isOUServer = ( getenv( 'OUENV' ) !== FALSE );
	}
	
}

// EOF