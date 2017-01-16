<?php namespace OUFabric\OUCommon\Models;

use OUFabric\OUCommon\OUCommon;

class Logger 
{

    const DEBUG = 100; // Detailed debug information.
    const INFO = 200; // Interesting events. Examples: User logs in, SQL logs.
    const NOTICE = 250; // Normal but significant events.
    const WARNING = 300; // Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
    const ERROR = 400; // Runtime errors that do not require immediate action but should typically be logged and monitored.
    const CRITICAL = 500; // Critical conditions. Example: Application component unavailable, unexpected exception.
    const ALERT = 550; // Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.
    const EMERGENCY = 600; // Emergency: system is unusable.
    
    
    public static function debug( $model, $message, $data )
    {
        self::log( self::DEBUG, $model, $message, $data );
    }

    public static function info( $model, $message, $data )
    {
        self::log( self::INFO, $model, $message, $data );
    }

    public static function notice( $model, $message, $data )
    {
        self::log( self::NOTICE, $model, $message, $data );
    }

    public static function warning( $model, $message, $data )
    {
        self::log( self::WARNING, $model, $message, $data );
    }

    public static function error( $model, $message, $data )
    {
        self::log( self::ERROR, $model, $message, $data );
    }

    public static function critical( $model, $message, $data )
    {
        self::log( self::CRITICAL, $model, $message, $data );
    }

    public static function alert( $model, $message, $data )
    {
        self::log( self::ALERT, $model, $message, $data );
    }

    public static function emergency( $model, $message, $data )
    {
        self::log( self::EMERGENCY, $model, $message, $data );
    }

    private static function log( $level, $model, $message, $data )
    {
        // Ben Gurney : removed this for the time being as it is creating too many log entries!
        
        // $stmt = OUCommon::$db->prepare( 'INSERT INTO `oucommon_1-log` ( `level`, `model`, `message`, `data`, `created_at` ) VALUES ( ?, ?, ?, ?, NOW() )' );
        // $stmt->execute( [
        //     $level,
        //     $model,
        //     $message,
        //     $data,
        // ] );
    }
    
}
