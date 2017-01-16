<?php

namespace OUFabric\OUCommon\Models;

use \OUFabric\OUCommon\DataOpen;

class Qualification extends AbstractModel {
	
	const LEVEL_UNDERGRADUATE	= 'undergraduate';
	const LEVEL_POSTGRADUATE 	= 'postgraduate';
	
	const FRAMEWORK_TYPE_M 	= 'M';
	const FRAMEWORK_TYPE_Q0 = 'Q0';
	const FRAMEWORK_TYPE_Q1 = 'Q1';
	
	public $code;
	public $title;
	public $description;
	public $urlInfo;
	public $level;
	public $framework;
	public $minAwardPoints;
	public $subjects = [];
	
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
					'Qualification properties are missing for ' . $this->code,
					print_r( $this->error, TRUE )
				);
			}
		}
		else 
		{
			Logger::critical( 
				get_class( $this ),
				'Qualification code not found or invalid format returned',
				$this->code
			);
		}
	}


	public static function getQualifications() {
	 $qualsSPARQL =
		"select distinct ?qualification
		from <http://data.open.ac.uk/context/qualification>
		where {
		?qualification a <http://purl.org/net/mlo/qualification>
		}";
		
		if ( $response = DataOpen::query( $qualsSPARQL ) ) {
			if ( $json = @json_decode( $response ) ) {
				if ( isset ( $json->results->bindings ) ) {
					$quals = [];
					
					foreach ( $json->results->bindings AS $result ) {
						$qualURI = $result->qualification->value;
						
						if ( preg_match( "#^http://data\.open\.ac\.uk/qualification/(?:of/)?(.+?)$#", $qualURI, $matches ) ) {
							$qualCode = $matches[1];
							$quals[] = new Qualification( $qualCode );
						}
					}
					
					return $quals;
				}
			}
		}
			
		return [];
	}


	private function validate()
	{
		$missing = [];

		if ( is_null( $this->title ) ) $missing[] = 'title';
		if ( is_null( $this->level ) ) $missing[] = 'level';

		if ( $missing ) $this->error = $missing;

		return ( $missing ) ? FALSE : TRUE;
	}

	private function getJson() {
		$uri = 'lookup?qualification=' . strtolower( $this->code );
		
		if ( $response = DataOpen::uri( $uri ) ) {
			if ( $json = json_decode( $response ) ) {
				if ( $qualification = $this->extractQualificationObject( $json ) ) {
					return $qualification;
				}
			}
		}
		
		return FALSE;
	}

	private function extractQualificationObject( $json ) {

		$keys = [
			'of' => 'http://data.open.ac.uk/qualification/of/' . strtolower( $this->code ),
			'nf' => 'http://data.open.ac.uk/qualification/' . strtolower( $this->code )
		];

		if ( isset( $json->{$keys['of']} ) )
		{
			$this->framework = self::FRAMEWORK_TYPE_Q0;
			return $json->{ $keys['of'] };
		}
		else if ( isset( $json->{$keys['nf']} ) )
		{
			$this->framework = self::FRAMEWORK_TYPE_Q1;
			return $json->{ $keys['nf'] };
		}

		return FALSE;
	}

	private function mapFromJson( $json ) {
		$subjects = [];

		if ( isset( $json->{ 'http://purl.org/dc/terms/subject' } ) )
		{
			foreach ( $json->{ 'http://purl.org/dc/terms/subject' } AS $nodeSubject )
			{
				$subjectUri = $nodeSubject->value;
				$subject = new Subject( $subjectUri );
				
				if ( $subject->id )
				{
					$alreadyExists = FALSE;
						
					foreach ( $subjects AS $subjectCompare )
					{
						if ( $subjectCompare->id == $subject->id )
						{
							$alreadyExists = TRUE;
							break;
						}
					}
						
					if ( ! $alreadyExists )
					{
						$subjects[] = $subject;
					}
				}
			}
		}
		
		$this->title = isset( $json->{ 'http://purl.org/dc/terms/title' }[0]->value ) ? $json->{ 'http://purl.org/dc/terms/title' }[0]->value : NULL;
		$this->urlInfo = isset( $json->{ 'http://purl.org/net/mlo/url' }[0]->value ) ? $json->{ 'http://purl.org/net/mlo/url' }[0]->value : NULL;
		$this->description = isset( $json->{ 'http://purl.org/dc/terms/description' }[1]->value ) ? $json->{ 'http://purl.org/dc/terms/description' }[1]->value : NULL;
		$this->minAwardPoints = isset( $json->{ 'http://data.open.ac.uk/saou/ontology#minAwardPoints' }[0]->value ) ? (int) $json->{ 'http://data.open.ac.uk/saou/ontology#minAwardPoints' }[0]->value : NULL;
		$this->level = isset( $json->{ 'http://data.open.ac.uk/saou/ontology#awardLevel' }[0]->value ) ? explode( '#', $json->{ 'http://data.open.ac.uk/saou/ontology#awardLevel' }[0]->value )[1] : NULL;
		$this->subjects = $subjects;
	}
	
}

// EOF