<?php

namespace OUFabric\OUCommon;

class Sanitisation {
	
	public static function inputStringSafe( $str, $nl2br=false ) {
		$encoding = mb_detect_encoding( $str, 'auto' );
		$str1 = htmlentities( mb_convert_encoding( trim( $str ), 'HTML-ENTITIES', $encoding ), ENT_QUOTES, 'UTF-8', false );
		
		if ( $nl2br ) {
			$str1 = nl2br( $str1 );
		}
		
		return $str1;
	}
	
}

// EOF