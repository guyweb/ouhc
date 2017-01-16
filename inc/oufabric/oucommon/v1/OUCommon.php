<?php

define( 'OUCOMMON_PATH_ROOT', dirname( __FILE__ ) );

// Start PHP session
if ( session_status() == PHP_SESSION_NONE ) {
	session_start();
}

// Load environment config
require_once( OUCOMMON_PATH_ROOT . '/_config/conf.php' );

foreach ( glob( OUCOMMON_PATH_ROOT .  '/*.class.php' ) AS $file ) { 
	require_once( $file );
}

// Load OU Common business models

// Load AbstractModel
require_once( OUCOMMON_PATH_ROOT .  '/models/AbstractModel.php' );

// Load 'User' model first as it is extended by other models
require_once( OUCOMMON_PATH_ROOT .  '/models/User.php' );

foreach ( glob( OUCOMMON_PATH_ROOT .  '/models/*.php' ) AS $file ) { 
	require_once( $file );
}


\OUFabric\OUCommon\OUCommon::init();

// EOF;