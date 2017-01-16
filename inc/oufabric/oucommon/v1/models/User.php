<?php

namespace OUFabric\OUCommon\Models;

class User extends AbstractModel {
	
	const TYPE_STAFF 		= 'staff';
	const TYPE_STUDENT 		= 'student';
	const TYPE_TUTOR 		= 'tutor';
	const TYPE_VISITOR 		= 'visitor';
	const TYPE_SELFREG 		= 'selfreg';
	const TYPE_UNKNOWN		= 'unknown';
	
	public $type;
	public $id;
	public $oucu;
	public $displayName;
	public $authIDs = [];
	
	public function getFirstName() {
		if ( trim( $this->displayName ) ) {
			return explode( ' ', $this->displayName )[0];
		}
		
		return '';
	}
	
}

// EOF