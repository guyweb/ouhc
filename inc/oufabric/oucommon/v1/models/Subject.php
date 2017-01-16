<?php

namespace OUFabric\OUCommon\Models;

use \OUFabric\OUCommon\OUCommon;

class Subject extends AbstractModel {
	
	public $id;
	public $title;
	public $uri;
	
	public function __construct( $uri ) {
		$stmt = OUCommon::$db->prepare( "SELECT * FROM `oucommon_1-subjects` WHERE `uri` = ?" );
		$stmt->execute( [ $uri ] );
		
		if ( $data = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
			$this->uri = $uri;
			$this->id = $data['idInternal'];
			$this->title = $data['title'];
		}
	}
	
}

// EOF