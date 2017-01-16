<?php

namespace OUFabric\OUCommon;

class Database {
	
	public static $errorPage;
	
	private static $connections = array();
	private $settings;
	
	function __construct( $settings = array() ) {
		if ( ENVIRONMENT == OUCommon::ENV_DEV && ! OUCommon::$isOUServer ) {
			$dbConfigFile = OUCOMMON_PATH_ROOT . '/_config/db-conf.php';
			if ( file_exists( $dbConfigFile ) ) require( $dbConfigFile );
		} else {
			switch ( ENVIRONMENT ) {
				case "live":
					$db['host'] = "kagu.open.ac.uk";
					$db['user'] = "ldtsharedUser";
					$db['pass'] = "pk0n4mq9";
					$db['name'] = "ldtshared";
					break;
				
				case "test":
					$db['host'] = "piculet.open.ac.uk";
					$db['user'] = "ldtsharedUser";
					$db['pass'] = "plst09sd";
					$db['name'] = "ldtshared_acct";
					break;
				
				case "dev";
				default:
					$db['host'] = "crake.open.ac.uk";
					$db['user'] = "ldtUser";
					$db['pass'] = "lDt10Dec";
					$db['name'] = "ldt_shared";
					break;
			}
		}
		
		if ( isset( $db ) ) $this->settings = array_merge( $db, $settings );
	}
	
	public function connect() {
		try {
			$connHash = md5( $this->settings['host'] . $this->settings['name'] . $this->settings['user'] . $this->settings['pass'] );
			
			if ( isset( self::$connections[ $connHash ] ) ) {
				$conn = self::$connections[ $connHash ];
			} else {
				$conn = new \PDO("mysql:host=" . $this->settings['host'] . ";dbname=" . $this->settings['name'], $this->settings['user'], $this->settings['pass'], array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
				self::$connections[ $connHash ] =& $conn;
			}
			
			return $conn;
		}
		catch( \PDOException $e ) {
			self::dbErrorHandler( $e );
		}	
	}
	
	public function connectLegacy() {
		$link = @mysql_connect($this->settings['host'], $this->settings['user'], $this->settings['pass'], true);
		$conn = @mysql_select_db($this->settings['name'], $link);
		
		if (!$link || !$conn) {
			self::dbErrorHandler();
		}
		else {
			return $link;
		}
	}
	
	private function dbErrorHandler( $e ) {
		if ( ENVIRONMENT == OUCommon::ENV_DEV && ! OUCommon::$isOUServer ) {
			echo '<h1>OU Common database exception:</h1>';
			echo '<pre>';
			print_r( $e );
			echo '</pre>';
			exit();
		}
		
		OUCommon::writeLog( 'errors', 'database', 'Could not connect to database on page ' . $_SERVER['REQUEST_URI'] );
		
		if ( isset( self::$errorPage ) && file_exists( self::$errorPage ) ) {
			include( self::$errorPage );
		} else {
			include( 'inc/dbErrorPage.php' );
		}
		
		exit();
	}
		
}

?>