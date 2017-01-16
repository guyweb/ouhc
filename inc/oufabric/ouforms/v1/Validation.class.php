<?php

namespace OUFabric\OUForms;

class Validation {

	public static $label_required = "Required";
	public static $label_requiredAll = "Required";
	public static $label_requiredMax = "Required";
	public static $label_requiredEmailFormat = "Required in valid email format";
	
	public function required($value, $field, $params = []) {
	
		$errMsg = "This field is required.";
		
		if ( is_array( $value ) ) {
			$err = false;
			
			foreach ( $value as $v ) {
				if ( ! trim($v) ) $err = true;
			}
			
			if ( $err ) return $errMsg;

		} else {

			if ( ! trim( $value ) ) return $errMsg;

		}

		return false;
	}

	public function requiredAll($value, $field, $params = []) {

		if ( is_array($value) ) {

			$errMsg = "This field is incomplete.";
			if ( count( $field->input ) != count( $value ) ) return $errMsg;

		} else {

			$errMsg = "This field is required.";
			if ( ! trim( $value ) ) return $errMsg;

		}

		return false;
	}

	public function requiredMax($value, $field, $params = []) {

		$max = ( isset( $params[0] ) ) ? $params[0] : count( $field->input );

		if ( is_array($value) ) {

			$errMsg = "This field can only contain a maximum of $max options. ";
			if ( count( $value ) > $max ) return $errMsg;

		} else {

			$errMsg = "This field is required.";
			if ( ! trim( $value ) ) return $errMsg;

		}

		return false;
	}

	public function requiredEmailFormat($value) {

		if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
  			
  			$errMsg = "This field is required.";
			if ( ! trim( $value ) ) return $errMsg;

		} else {

			$errMsg = "This field must contain a valid email address. ";
  			return $errMsg;

		}
	}
}
?>