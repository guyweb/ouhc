<?php

namespace OUFabric\OUCommon\Models;

use \OUFabric\OUCommon\DataOpen;

class Module extends AbstractModel {
	
	const LEVEL_UNDERGRADUATE 	= 'undergraduate';
	const LEVEL_POSTGRADUATE	= 'postgraduate';
	
	const STATUS_CURRENT = 'current';
	const STATUS_COMPLETE = 'complete';
	const STATUS_CANCELLED = 'cancelled';
	
	public $name;
	public $code;
	public $level;

	private $error;
	
	public function __construct( $code ) {
		$this->code = strtoupper( trim( $code ) );

		if ( $json = $this->getJson() )
		{
			$this->mapFromJson( $json );

			if ( ! $this->validate() )
			{
				Logger::critical(
					get_class( $this ),
					'Module properties are missing for ' . $this->code,
					print_r( $this->error, TRUE )
				);
			}
		}
		else 
		{
			Logger::critical( 
				get_class( $this ),
				'Module code not found or invalid format returned',
				$this->code
			);
		}
	}

	private function validate()
	{
		$missing = [];

		if ( is_null( $this->name ) ) $missing[] = 'name';
		if ( is_null( $this->level ) ) $missing[] = 'level';

		if ( $missing ) $this->error = $missing;

		return ( $missing ) ? FALSE : TRUE;
	}

	private function getJson() {
		$url = 'http://data.open.ac.uk/course/' . strtolower( $this->code );
		$query = "DESCRIBE <{$url}>";
		
		if ( $response = DataOpen::query( $query ) ) {
			if ( $json = json_decode( $response ) ) {
				if ( isset( $json->{ $url } ) ) {
					return $json->{ $url };
				}
			}
		}
		
		return FALSE;
	}

	private function mapFromJson( $json )
	{
		$this->name = isset( $json->{ 'http://purl.org/dc/terms/title' }[0]->value ) ? $json->{ 'http://purl.org/dc/terms/title' }[0]->value : NULL;
		$this->level = isset( $json->{ 'http://data.open.ac.uk/saou/ontology#courseLevel' }[0]->value ) ? explode( '#', $json->{ 'http://data.open.ac.uk/saou/ontology#courseLevel' }[0]->value )[1] : NULL;
	}
	
}

// EOF