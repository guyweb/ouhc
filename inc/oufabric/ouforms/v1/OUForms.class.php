<?php
/*
 *  ## OU FORMS ##
 *  ## A form framework for The Open University ##
 *  
 *  Author: Ben Gurney / bdg48 / ben.gurney@open.ac.uk
 *        + Jack Chapple / jc27976 / jchapple@gmail.com
 */

namespace OUFabric\OUForms;

use OUFabric\OUCommon\OUCommon;

class OUForms {
		
	public static $errorPage;
	public $errors;	
	
	private $pathUplTmp;
	private $sessionId;
	private $form;
	private $defaults;
	private $edit;
	private $antiSpamActive = FALSE;
	
	const ERROR_MISSING_DATA = 'missing_data';
	const SPAM_TIME_THRESHOLD = '2'; // 5 seconds
	
	function __construct($xml, $namespace) {
		if (!isset($_SESSION)) session_start();
		
		$this->pathUplTmp = $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/_data/_private/ouforms/';
		
		if ( ! is_dir( $this->pathUplTmp ) ) {
			mkdir( $this->pathUplTmp, 0777, TRUE );
		}
		
		require_once("Validation.class.php");
		
		$this->sessionId = md5("ouforms-" . $namespace);
		$this->defaults = new \stdClass();
		
		if (!$this->form = @simplexml_load_file($xml)) die('An error occurred when trying to parse the XML file.');
		
		// Clear the form
		if (isset($_GET['clear']) && $_GET['clear'] == "form") $this->clearForm();
		
		// Save submitted fields
		if ($_POST) {
			$this->save();
		}
		else {
			unset($_SESSION[$this->sessionId]['validation']);
		}
		
		// Check for Edit mode
		$this->checkEditMode();
	}
	
	
	
	/* ### Public functions ### */
	

	public function autoShow($s = NULL) {
		$section = $this->form->section;
		if ($s) $section = $this->form->xpath('//section[@name="' . $s . '"]');

		foreach ($section as $section) {
			echo '<h2>' . $section->title . '</h2>' . "\n\n";
			
			foreach ($section->fieldset as $fieldset) {
				echo '<fieldset>' . "\n";
				echo '<legend>' . $fieldset->legend . '</legend>' . "\n\n";
				
				foreach ($fieldset->field as $field) {
					$name = (string) $field->attributes()->name;
					$this->showField($name);
				}
				
				echo '</fieldset>' . "\n\n";	
			}
		}
	}
	
	public function showField($name) {
		// Output the anti-spam fields (will only run the first time it is called)
		$this->antiSpam();

		// Store the field in an array so that we know which fields to check for in validation
		unset($_SESSION[$this->sessionId]['validation'][$name]);
		$_SESSION[$this->sessionId]['validation'][$name] = true;
		
		$output = '';
		$wrapper = '';
		$describedby = '';
		
		if (stristr($name, "[")) die("Err: Field names cannot contain array keys.");
		
		$field = $this->form->xpath('//field[@name="' . $name . '"]');
		if (!$field) die("Err: '" . $name . "' could not be found.");
		$field = $field[0];
		
		if ($field->attributes()->type == "content") {
			$classes = $this->parseFieldClasses($this->getContentFieldClasses($field));
			
			$info['name'] = $name;
			$info['type'] = $field->type;
			$content = $field->content;
			
			echo 
				'<div' . $classes . ' id="fieldContainer_' . $name . '">' . "\n" . 
					$this->buildHTMLContent($info, $content) . "\n" . 
				'</div>' . "\n\n";
		}
		else {
			$classes = $this->parseFieldClasses($this->getInputFieldClasses($field));
			if ($describedby = $field->attributes()->describedby) $describedby = ' aria-describedby="fieldContainer_' . $describedby . '"';
			
			$wrapper .= '<div' . $classes . ' id="fieldContainer_' . $name . '">' . "\n";
			if ($field->title) $wrapper .= '<div class="title-container">' . "\n" . '<p class="title" id="fieldTitle-' . $name . '"' . $describedby . $this->validationAriaRequired($name) . '>' . $field->title . $this->validationLabel($name) . '</p>' . "\n" . $this->fieldInfo($field) . '</div>' . "\n";
			$wrapper .= '<div class="inputs-container inputs-' . $field->type . '">%s</div></div>' . "\n\n";
			
			foreach ($field->input as $input) {
				$info['name'] = $name;
				$info['type'] = $field->type;
				$info['describedby'] = $describedby;
				$output .= $this->buildHTMLInput($input, $info);
			}
		
			if ($field->type == "hidden") {
				echo $output;
			}
			else {
				echo sprintf($wrapper, $output);
			}
		}
	}
	
	public function nextButton($next_label = "Next", $save_label = "Save", $class_append = "") {
		if ($this->getEditField()) {
			echo '<input type="submit" class="ou-next ' . $class_append . '" name="save" value="' . $save_label . '" />';
		}
		else {
			echo '<input type="submit" class="ou-next ' . $class_append . '" name="next" value="' . $next_label . '" />';
		}
	}
	
	public function prevButton($page = "", $label = "Back", $class_append = "") {
		if (!$this->getEditField()) echo '<a href="' . $page . '" class="ou-previous ' . $class_append . '">' . $label . '</a>';
	}

	public function antiSpam() {
		
		// Prevent the function from running more than once
		if ( ! $this->antiSpamActive ) {
			$this->antiSpamActive = TRUE;

			$securityToken = md5( mt_rand() );
			$_SESSION[$this->sessionId]['security-token'] = $securityToken;

			$_SESSION[$this->sessionId]['security-time'] = time();

			echo 
				'<div class="ou-honigfalle">' . 
				'<input type="text" name="ou-honigfalle" />' . 
				'<input type="hidden" name="ou-token" value="' . $securityToken . '" />' . 
				'</div>';
		}
	}
	
	public function showConditionJSON() {
		$conditions = array();
		$fields = $this->form->xpath("//field");
		
		foreach ($fields as $field) {
			$name = (string) $field->attributes()->name;
			$conditionArray = array();
			
			if ($field->conditions->condition) {
				$i = 0;
				foreach ($field->conditions->condition as $condition) {
					$parts = explode(":", $condition);
					$conditionArray[$i]['name'] = $parts[0];
					$conditionArray[$i]['value'] = $parts[1];
					$i++;
				}
				$conditions[$name] = $conditionArray;
			}
		}
		
		echo json_encode($conditions);
	}
	
	public function getFieldLabel($name) {
		$field = $this->form->xpath('//field[@name="' . $name . '"]');
		if ($field) return (string) $field[0]->input->label;
	}
	
	public function getFieldTitle($name) {
		$field = $this->form->xpath('//field[@name="' . $name . '"]');
		if ($field) return (string) $field[0]->title;
	}
	
	public function getSectionTitle($name) {
		$section = $this->form->xpath('//section[@name="' . $name . '"]');
		if ($section) return (string) $section[0]->title;	
	}
	
	public function getLegend($name) {
		$fieldset = $this->form->xpath('//fieldset[@name="' . $name . '"]');
		if ($fieldset) return (string) $fieldset[0]->legend;
	}
	
	public function setDefault($name, $value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$this->defaults->{$name}[$k] = (string) $v;
			}
		}
		else {
			$this->defaults->$name = (string) $value;
		}
	}
	
	public function getFormArray($showHidden = false) {
		
		$form = array();
		
		$s = 0;
		foreach ($this->form->xpath('//section') as $section) {
			$form['section'][$s]['name'] = (string) $section->attributes()->name;
			$form['section'][$s]['title'] = (string) $section->title;
			
			$f = 0;
			foreach ($this->form->xpath('//section[@name="' . $form['section'][$s]['name'] . '"]/fieldset') as $fieldset) {
				$form['section'][$s]['fieldset'][$f]['name'] = (string) $fieldset->attributes()->name;
				$form['section'][$s]['fieldset'][$f]['legend'] = (string) $fieldset->legend;
				
				$i = 0;
				foreach ($this->form->xpath('//section[@name="' . $form['section'][$s]['name'] . '"]/fieldset[@name="' . $form['section'][$s]['fieldset'][$f]['name'] . '"]/field[not(@type = "content")]') as $field) {	
					
					$section = $form['section'][$s]['name'];
					$fieldset = $form['section'][$s]['fieldset'][$f]['name'];
					$name = (string) $field->attributes()->name;
					
					$value = (isset($_SESSION[$this->sessionId]['form'][$section][$fieldset][$name])) ? $_SESSION[$this->sessionId]['form'][$section][$fieldset][$name] : "";
				
					if ($this->matchConditions($field) && ($field->type != "hidden" || $showHidden)) {
						$form['section'][$s]['fieldset'][$f]['field'][$i]['name'] = $name;
											
						if (is_array($value)) {
							foreach ($value as $k => $v) {
								$form['section'][$s]['fieldset'][$f]['field'][$i]['friendlyValue'][] = (string) $this->getFriendlyValue($name, $k);
								$form['section'][$s]['fieldset'][$f]['field'][$i]['value'][] = $k;
							}
						}
						else {
							$form['section'][$s]['fieldset'][$f]['field'][$i]['friendlyValue'] = (string) $this->getFriendlyValue($name, $value);
							$form['section'][$s]['fieldset'][$f]['field'][$i]['value'] = $value;
						}
						
						$form['section'][$s]['fieldset'][$f]['field'][$i]['displayLabel'] = $this->getFriendlyLabel($name);
					}
					
					$i++;
				}
				
				$f++;
			}
			
			$s++;
		}
		
		return $form;
	}
	
	public function getFieldValue($name, $key = false) {
		$name = (string) $name;
		$section = $this->getParentSectionName($name);
		$fieldset = $this->getParentFieldsetName($name);
		
		if ($key) {
			$key = (string) $key;
			if ($_POST) {
				if (isset($_POST[$name][$key])) return $_POST[$name][$key];
			}
			else if (isset($_SESSION[$this->sessionId]['form'][$section][$fieldset][$name][$key])) {
				return $_SESSION[$this->sessionId]['form'][$section][$fieldset][$name][$key];
			}
			else if (isset($this->defaults->{$name}[$key]) && !$_POST) {
				return $this->defaults->{$name}[$key];
			}
		}
		else {
			if (isset($_POST[$name])) {
				return $_POST[$name];
			}
			else if (isset($_SESSION[$this->sessionId]['form'][$section][$fieldset][$name])) {
				return $_SESSION[$this->sessionId]['form'][$section][$fieldset][$name];
			}
			else if (isset($this->defaults->$name) && !$_POST) {
				return $this->defaults->$name;
			}
		}
		
		return false;
	}
	
	public function showSummary() {
		// Output the anti-spam fields (will only run the first time it is called)
		$this->antiSpam();

		$form = $this->getFormArray();
				
		echo '<div class="ouforms-summary">';
		
		if ( ! $this->formComplete() ) {
			echo 
				'<div class="errors" aria-live="polite" aria-atomic="true">' . 
				'<p>There appears to have been a blip in our system whilst you were completing the form:</p>' . 
				'<span class="field_name">Missing sections:</span> <span class="err_msg">Some of the sections are missing (indicated below). Please go back and <a href="' . $this->getStartPage() . '">update the form</a> with the missing data.</span>' . 
				'</div>';
		}
		
		echo '<table>';
		
		foreach ($form['section'] as $section) {
			$sectionEdit = ( isset( $_SESSION[ $this->sessionId ]['form'][ $section['name'] ] ) ) ? '' : 'Missing';
			echo '<tr class="section"><th colspan="2">' . $section['title'] . '</th><td>' . $sectionEdit . '</td></tr>';
			
			foreach ($section['fieldset'] as $fieldset) {
				if (isset($fieldset['field'])) {
					echo '<tr class="fieldset"><th colspan="3">' . $fieldset['legend'] . '</th></tr>';
					
					foreach ($fieldset['field'] as $field) {	
													
						if (is_array($field['value'])) {
							$displayValue = '<ul class="values">';
							
							foreach ($field['friendlyValue'] as $value) {
								$displayValue .= '<li class="value">' . $value . '</li>';
							}
							$displayValue .= '</ul>';
						}
						else {
							if ($pwdSafeValue = $this->isPasswordField($field['name'])) $field['friendlyValue'] = $pwdSafeValue;
							$displayValue = '<span class="value">' .  nl2br($field['friendlyValue']) . '</span>';
						}
						
						$editButton = "";
						if (isset($_SESSION[$this->sessionId]['pages'][$field['name']])) $editButton = ' <a href="?edit=' . $field['name'] . '">Edit</a>';
						
						echo 
							'<tr>' . 
							'<th class="label_column">' . $field['displayLabel'] . '</th>' . '<td class="value_column">' . $displayValue . '</td>' . '<td class="edit_column">' . $editButton . '</td>' . 
							'</tr>';
					}
				}
			}
		}
				
		echo 
			'</table>' . 
			'</div>';
	}
	
	public function outputForEmail() {
		$form = $this->getFormArray(true);
		$output = "";
		$uploads = array();
		
		foreach ($form['section'] as $section) {
			$output .= strtoupper($section['title']) . "\n";
			$output .= '-------------------------------------------------' . "\n\n";
			
			foreach ($section['fieldset'] as $fieldset) {
				if (isset($fieldset['field'])) {
					$output .= strtoupper($fieldset['legend']) . "\n";
						
					foreach ($fieldset['field'] as $field) {	
						if (is_array($field['value'])) {
							$displayValue = "";
							foreach ($field['friendlyValue'] as $value) {
								$displayValue .= $value  . ", ";
							}
							$displayValue = rtrim($displayValue, ", ");
						}
						else {
							$displayValue = $field['friendlyValue'];

							// Added to remove characters which cause voice problems e.g. [] sp 28/02/2015
							$displayValue = preg_replace('/\[/', '(', $displayValue);	
							$displayValue = preg_replace('/\]/', ')', $displayValue);	
							
						}
						
						$output .= "- " . $field['displayLabel'] . ": " . $displayValue . "\n";
					}
					
					$output .= "\n";
				}
			}
			
			$output .= "\n";
		}
		
		return $output;
	}

	public function emailToVoice ( $voiceFields = "", $mailSettings = array() ) {

		$message = $this->outputForVoice( $voiceFields );

		$this->sendEmail( $message, $mailSettings );

	}

	public function email ( $mailSettings = array(), $optionalMessage = "" ) {
		$optionalMessage = ( $optionalMessage ) ? $optionalMessage . "\n\n" : $optionalMessage;

		$message = $optionalMessage . $this->outputForEmail();

		$this->sendEmail( $message, $mailSettings );

	}
	
	
	public function outputForVoice($fields = "") {
		$output = '';
		
		if (isset($fields) && is_array($fields)) {
			foreach ($fields as $key => $value) {
				$output .= '[' . $key . ']' . "\n";
				$output .= $value . "\n\n";
				
				if (strtolower($key) == "description") $output .= "\n" . $this->outputForEmail();
			}
		}
		else {
			$output = "Voice fields not found.";
		}
		
		return $output;
		
	}
	
	public function errorHandler() {
		if (is_array($this->errors)) {
			echo '<div class="errors" aria-live="polite" aria-atomic="true">' . "\n";
			echo '<p>There is an error with one or more fields on this form. Please check your information.</p>' . "\n";
			echo '<ul>' . "\n";
			foreach ($this->errors as $field => $error) {
				echo '<li><a href="#fieldContainer_' . $field . '"><span class="field_name">' . $this->getFriendlyLabel($field) . '</span>: <span class="err_msg">' . $error . '</span></a></li>' . "\n";
			}
			echo '</ul>' . "\n";
			echo '</div>' . "\n";
		}
	}
	
	public function fatalError( $errorType ) {	
		switch ( $errorType ) {
			case self::ERROR_MISSING_DATA:
				$errorMessage = 'There appears to have been a blip in our system whilst you completed the form and some of the data you entered has gone missing. Please go back and <a href="' . $this->getStartPage() . '">update the form</a> with the missing data.';
				break;
			default:
				$errorMessage = 'An error has ocurred.';
				break;
		}
		
		if ( isset( self::$errorPage ) && file_exists( self::$errorPage ) ) {
			require_once( self::$errorPage );
		}
		else {
			require_once( 'inc/formErrorPage.php' );
		}
		exit();
	}
	
	public function getSession() {
		if ( isset( $_SESSION[ $this->sessionId ] ) ) {
			return $_SESSION[$this->sessionId];
		}
		return false;		
	}
	
	public function clearSession() {
		$this->clearFiles();
		
		if ( isset( $_SESSION[ $this->sessionId ] ) ) {
			unset( $_SESSION[ $this->sessionId ] );
			return true;
		}
		
		return false;
	}
	
	public function formExists() {
		if (isset($_SESSION[$this->sessionId])) return true;
		return false;
	}
	
	public function formComplete() {
		return $this->checkAllSections();
	}
	
	public function clearForm() {
		$this->clearSession();
		header("Location: " . $this->getStartPage());
		exit();
	}
	
	public function logData($userId) {
		$db = &OUCommon::$db;
		
		$data = $this->outputForEmail();
		$uri = $_SERVER['REQUEST_URI'];
		
		$stmt = $db->prepare("INSERT INTO `ouforms_1-log` (`user_id`,`request_uri`,`data`) VALUES (?, ?, ?)");
		$stmt->execute(array($userId, $uri, $data));
		
		return ($stmt->rowCount() > 0);
	}

	public function getTmpFilePaths() {
		$files = [];

		foreach ( $this->getFiles() as $file ) {
			$files[] = [
				'path' => $this->pathUplTmp . $file['uuid'],
				'name' => $file['name'],
				'type' => $file['type'],
			];
		}
		return $files;
	}
	
	/* ### Private functions ### */
	
	private function save() {

		foreach ( $_FILES AS $key => $file ) {
			if ( strpos( $key, 'file-' ) === 0 ) {
				$newKey = substr( $key, 5 );
				$_FILES[ $newKey ] = $file;
				
				if ( ! isset( $_POST[ $newKey ] ) ) {
					$_POST[ $newKey ] = $file['name'];
				}
				
				unset( $_FILES[ $key] );
			}
		}
		
		foreach ( $_POST as $key => $value ) {
			if ( ! $field = $this->form->xpath('//field[@name="' . $key . '"]') ) {
				continue;
			}
			
			if ( (string) $field[0]->type != 'file' ) {
				continue;
			}
			
			$section = $this->getParentSectionName($key);
			$fieldset = $this->getParentFieldsetName($key);
			
			switch( $value ) {
				case 'existing':
					if ( isset( $_SESSION[$this->sessionId]['uploads'][$section][$fieldset][$key] ) ) {
						$uplFilename = $_SESSION[$this->sessionId]['uploads'][$section][$fieldset][$key]['name'];
						$_POST[$key] = $uplFilename;
						unset( $_FILES[$key] );
					}
					break;
				case 'new':
				case 'none':
					if ( $value == 'new' ) {
						if ( isset( $_FILES[$key] ) ) {
							$_POST[$key] = $_FILES[$key]['name'];
						}
					} elseif ( $value == 'none' ) {
						unset( $_FILES[$key] );
						$_POST[$key] = '';
					}
					
					$_SESSION[$this->sessionId]['form'][$section][$fieldset][$key] = '';
					
					if ( isset( $_SESSION[$this->sessionId]['uploads'][$section][$fieldset][$key] ) ) {
						@unlink( $this->pathUplTmp . $_SESSION[$this->sessionId]['uploads'][$section][$fieldset][$key]['uuid'] );
						unset( $_SESSION[$this->sessionId]['uploads'][$section][$fieldset][$key] );
					}
					
					break;
			}
		}
		
		if ( ! $errors = $this->validate() ) {
			
			// Check for spam
			if ( $this->isSpam() ) {
				die( 'Thanks. The form was not submitted because you did not pass our anti-spam protection. Please go back and try again (unless you are actually trying to spam).' );
			}

			foreach ( $_POST as $key => $value ) {
				$section = $this->getParentSectionName($key);
				$fieldset = $this->getParentFieldsetName($key);
				$_SESSION[$this->sessionId]['form'][$section][$fieldset][$key] = $value;
				$_SESSION[$this->sessionId]['pages'][$key] = $_SERVER['REQUEST_URI'];
			}
			
			foreach ( $_FILES AS $key => $file ) {
				$_SESSION[$this->sessionId]['pages'][$key] = $_SERVER['REQUEST_URI'];
				
				if ( $file['error'] != UPLOAD_ERR_OK ) {
					$_SESSION[$this->sessionId]['form'][$section][$fieldset][$key] = '';
					continue;
				}
				
				$uuid = $this->generateUuid();
				
				move_uploaded_file( $file['tmp_name'], $this->pathUplTmp . $uuid );
				
				$section = $this->getParentSectionName($key);
				$fieldset = $this->getParentFieldsetName($key);
				
				$_SESSION[$this->sessionId]['uploads'][$section][$fieldset][$key] = array(
					'uuid' => $uuid,
					'type' => $file['type'],
					'name' => $file['name']
				);
				
				$_SESSION[$this->sessionId]['form'][$section][$fieldset][$key] = $file['name'];
			}
			
			// If an edit return URL exists, remove it and redirect back to the specified page
			if ($editField = $this->getEditField()) {
				unset($_SESSION[$this->sessionId]['settings']['edit']);
				
				header("Location: " . $editField['return']);
				exit();
			}
			
			header("Location: " . $_POST['nav']['next']);
			exit();
		}
		else {
			$this->errors = $errors;
		}
	}
	
	private function validate() {
		$validate = new Validation();
		
		$fields = array();
		if (isset($_SESSION[$this->sessionId]['validation'])) $fields = $_SESSION[$this->sessionId]['validation'];

		// Check to see if any of the fields are conditional
		foreach ($fields as $fieldName => $value) {
			$field = $this->form->xpath('//field[@name="' . $fieldName . '"]');
			
			// Conditions do not match so do not validate this field as it will be hidden
			if (!$this->matchConditions($field[0])) {
				if (isset($fields[$fieldName])) {
					unset($fields[$fieldName]);
					unset($_SESSION[$this->sessionId]['validation'][$fieldName]);
				}
			}
		}

		foreach ($fields as $fieldName => $bool) {
			$field = $this->form->xpath('//field[@name="' . $fieldName . '"]');
			$field = $field[0];
			
			$name = (string) $field->attributes()->name;
			
			if (isset($field->validation)) {

				$validation = (string) $field->validation;
				$validationArray = explode(":", $validation);

				$type = $validationArray[0];

				$params = (isset($validationArray[1])) ? $validationArray[1] : FALSE;
				$paramsArray = ($params) ? explode(",", $params) : [];

				$value = (isset($_POST[$name])) ? $_POST[$name] : NULL;

				if ($errMsg = $validate->$type($value, $field, $paramsArray)) $errors[$name] = $errMsg;
			}
			
			if ( isset( $_FILES[$name]['name'] ) && $_FILES[$name]['name'] ) {
				if ( $_FILES[$name]['error'] != UPLOAD_ERR_OK ) {
					$errors[$name] = "The file you have uploaded cannot be used.";
					continue;
				}
			}
		}
		
		if (isset($errors)) return $errors;
		return false;
	}

	private function isSpam() {
		$securityToken = ( isset( $_SESSION[$this->sessionId]['security-token'] ) ) ? $_SESSION[$this->sessionId]['security-token'] : FALSE;
		$ouToken = ( isset( $_POST['ou-token'] ) ) ? $_POST['ou-token'] : FALSE;
		$honigFalle = ( isset( $_POST['ou-honigfalle'] ) ) ? $_POST['ou-honigfalle'] : FALSE;
		$securityTime = ( isset( $_SESSION[$this->sessionId]['security-time'] ) ) ? $_SESSION[$this->sessionId]['security-time'] : FALSE;

		// If the security token is not valid
		if ( ! $securityToken || $ouToken !== $securityToken ) {
			return TRUE;
		}

		// If the honigfalle is filled in
		if ( $honigFalle && strlen( $honigFalle ) > 0 ) {
			return TRUE;
		}

		// If the security time has not been created, or the time between page load and submission is too short
		if ( ! $securityTime || $securityTime + self::SPAM_TIME_THRESHOLD > time() ) {
			return TRUE;
		}

		return FALSE;
	}
	
	private function buildHTMLInput($input, $info) {
		$fieldParams = $this->readParams($input->params);
		
		switch($info['type']) {
			case "radio":

				// If the checked flag exists, store the value as a default and remove the checked attribute
				if (isset($fieldParams['checked'])) {
					$this->setDefault($info['name'], $fieldParams['value']);
					unset($fieldParams['checked']);
				}
				
				$checked = '';
				if ($fieldParams['value'] == $this->getFieldValue($info['name'])) $checked = ' checked="checked"';
				
				return 
						'<div class="input-container input-radio">' . "\n" . 
							'<input' . $checked . ' type="' . $info['type'] . '" name="' . $info['name'] . '"' . $this->parseParams($fieldParams) . ' aria-labelledby="fieldTitle-' . $info['name'] . ' fieldLabel-' . $fieldParams['id'] . '" /> ' . "\n" . 
							'<div class="label-container"> ' . "\n" . 
								'<label for="' . $fieldParams['id'] . '" id="fieldLabel-' . $fieldParams['id'] . '">' . $input->label . '</label>' . $this->fieldInfo($input) . "\n" . 
							'</div> ' . "\n" . 
						'</div>';
						
			break;
			
			case "checkbox":
				$key = (string) $input->key;
				$name = $info['name'] . "[" . $key . "]";
				
				// If the checked flag exists, store the value as a default and remove the checked attribute
				if (isset($fieldParams['checked'])) {
					$this->setDefault($info['name'], array($key => $fieldParams['value']));
					unset($fieldParams['checked']);
				}
				
				$checked = '';
				if ($fieldParams['value'] == $this->getFieldValue($info['name'], $key)) $checked = ' checked="checked"';
				
				return 
						'<div class="input-container input-checkbox">' . "\n" . 
							'<input' . $checked . ' type="' . $info['type'] . '" name="' . $name . '"' . $this->parseParams($fieldParams) . ' aria-labelledby="fieldTitle-' . $info['name'] . ' fieldLabel-' . $fieldParams['id'] . '" /> ' . "\n" . 
							'<div class="label-container">' . "\n" . 
								'<label for="' . $fieldParams['id'] . '" id="fieldLabel-' . $fieldParams['id'] . '">' . $input->label . '</label>' . $this->fieldInfo($input) . "\n" . 
							'</div> ' . "\n" . 
						'</div>';
							
			break;
			
			case "select":
					
				$options = '';
				foreach ($input->options->option as $option) {
					$optionParams = $this->readParams($option->params);
					
					// If the selected flag exists, store the value as a default and remove the checked attribute	
					if (isset($optionParams['selected'])) {
						$this->setDefault($info['name'], $optionParams['value']);
						unset($optionParams['selected']);
					}
					
					$selected = '';
					if ($optionParams['value'] == $this->getFieldValue($info['name'])) $selected = ' selected="selected"';
					
					$options .= '<option' . $selected . $this->parseParams($optionParams) . '>' . $option->title . '</option>' . "\n";
				}
				
				return 
						'<div class="input-container input-select">' . "\n" . 
							'<div class="label-container">' . "\n" . 
								'<label for="' . $fieldParams['id'] . '" id="fieldLabel-' . $fieldParams['id'] . '"' . $info['describedby'] . $this->validationAriaRequired($info['name']) . '>' . $input->label . $this->validationLabel($info['name']) . '</label>' . $this->fieldInfo($input) . "\n" . 
							'</div> ' . "\n" . 
							'<select name="' . $info['name'] . '"' . $this->parseParams($fieldParams) . ' aria-labelledby="fieldLabel-' . $fieldParams['id'] . '">' . $options . '</select> ' . "\n" . 
						'</div>';
			
			break;
			
			case "textarea":
			
				// If a default value exists, store the value as a default
				if (isset($fieldParams['value'])) $this->setDefault($info['name'], $fieldParams['value']);
				
				// If a POST, SESSION or default value exists, set that as the value.
				if (strlen($this->getFieldValue($info['name'])) > 0) $fieldParams['value'] = $this->getFieldValue($info['name']);
				
				$value = '';
				if (isset($fieldParams['value'])) {
					$value = $fieldParams['value'];
					unset($fieldParams['value']);
				}
				
				return 
						'<div class="input-container input-textarea">' . "\n" . 
							'<div class="label-container">' . "\n" . 
								'<label for="' . $fieldParams['id'] . '" id="fieldLabel-' . $fieldParams['id'] . '"' . $info['describedby'] . $this->validationAriaRequired($info['name']) . '>' . $input->label . $this->validationLabel($info['name']) . '</label>' . $this->fieldInfo($input) . "\n" . 
							'</div> ' . "\n" . 
							'<textarea name="' . $info['name'] . '"' . $this->parseParams($fieldParams) . ' aria-labelledby="fieldLabel-' . $fieldParams['id'] . '">' . $value . '</textarea> ' . "\n" . 
						'</div>';
			
			break;
			
			case "hidden":
				
				// If a default value exists, store the value as a default
				if (isset($fieldParams['value'])) $this->setDefault($info['name'], $fieldParams['value']);
				
				// Clear the value if form has been POSTed. This wil prevent default values showing up if nothing is submitted.
				if ($_POST) $fieldParams['value'] = "";
				
				// If a POST, SESSION or default value exists, set that as the value.
				if (strlen($this->getFieldValue($info['name'])) > 0) $fieldParams['value'] = $this->getFieldValue($info['name']);
								
				return '<input type="' . $info['type'] . '" name="' . $info['name'] . '"' . $this->parseParams($fieldParams) . ' /> ' . "\n";
			
			break;
			
			case "file":
				
				$field = $this->form->xpath('//field[@name="' . $info['name'] . '"]');
				
				$section = $this->getParentSectionName( $info['name'] );
				$fieldset = $this->getParentFieldsetName( $info['name'] );
				
				$inputHtml = '<input type="' . $info['type'] . '" name="file-' . $info['name'] . '"' . $this->parseParams($fieldParams) . ' aria-labelledby="fieldLabel-' . $fieldParams['id'] . '" />';
				
				if ( isset( $_SESSION[ $this->sessionId ]['uploads'][$section][$fieldset][ $info['name'] ] ) ) {
					$inputHtml =
						'<label><input type="radio" name="' . $info['name'] . '" value="existing" checked /> ' . $this->getFieldValue( $info['name'] ) . '</label>' . "\n" .
						'<label><input type="radio" name="' . $info['name'] . '" value="new" /> Upload a new file:</label>' . "\n" .
						'<p>' . $inputHtml . '</p>' . "\n";
						
						if ( (string) $field[0]->validation != 'required' ) { 
							$inputHtml .= '<label><input type="radio" name="' . $info['name'] . '" value="none" /> No file</label>' . "\n";
						}
				} 
				
				return 
						'<div class="input-container input-file">' . "\n" . 
							'<div class="label-container">' . "\n" . 
								'<label for="' . $fieldParams['id'] . '" id="fieldLabel-' . $fieldParams['id'] . '"' . $info['describedby'] . $this->validationAriaRequired($info['name']) . '>' . $input->label . $this->validationLabel($info['name']) . '</label>' . $this->fieldInfo($input) . "\n" . 
							'</div> ' . "\n" .
							$inputHtml . "\n" .
						'</div>';
			
			default:
				
				// If a default value exists, store the value as a default
				if (isset($fieldParams['value'])) $this->setDefault($info['name'], $fieldParams['value']);
				
				// Clear the value if form has been POSTed. This wil prevent default values showing up if nothing is submitted.
				if ($_POST) $fieldParams['value'] = "";
				
				// If a POST, SESSION or default value exists, set that as the value.
				if (strlen($this->getFieldValue($info['name'])) > 0) $fieldParams['value'] = $this->getFieldValue($info['name']);
				
				return 
						'<div class="input-container input-text">' . "\n" . 
							'<div class="label-container">' . "\n" . 
								'<label for="' . $fieldParams['id'] . '" id="fieldLabel-' . $fieldParams['id'] . '"' . $info['describedby'] . $this->validationAriaRequired($info['name']) . '>' . $input->label . $this->validationLabel($info['name']) . '</label>' . $this->fieldInfo($input) . "\n" . 
							'</div> ' . "\n" . 
							'<input type="' . $info['type'] . '" name="' . $info['name'] . '"' . $this->parseParams($fieldParams) . ' aria-labelledby="fieldLabel-' . $fieldParams['id'] . '" /> ' . "\n" . 
						'</div>';
			
			break;
		}
		
	}
	
	private function buildHTMLContent($info, $content) {
		switch ($info['type']) {
			case "textblock":
					return $content;
			break;
		}
	}
	
	private function getInputFieldClasses($field) {
		$classArray[] = "field-container";
		$classArray[] = "field-input";
		if (!$field->title) $classArray[] = "no-title";
		if ($this->readConditions($field)) $classArray[] = "conditional";
		if (!$this->matchConditions($field)) $classArray[] = "conditional-hidden";
		
		if ($name = $field->attributes()->name) {
			$name = (string) $name;
			if (isset($this->errors[$name])) $classArray[] = "error";
			if (($editField = $this->getEditField()) && ($editField['field'] == $name)) $classArray[] = "edit";
		}
		
		return $classArray;
	}
	
	private function getContentFieldClasses($field) {
		$classArray[] = "field-container";
		$classArray[] = "field-content";
		if ($this->readConditions($field)) $classArray[] = "conditional";
		if (!$this->matchConditions($field)) $classArray[] = "conditional-hidden";
		
		return $classArray;
	}
	
	private function parseFieldClasses($classArray) {
		$classes = "";
		foreach ($classArray as $class) {
			$classes .= $class . ' ';
		}
		return ' class="' . $classes . '"';
	}
	
	private function readParams($paramObj) {
		$array = array();
		foreach ($paramObj as $param) {
			foreach ($param as $key => $value) {
				$array[$key] = $value;
			}
		}
		return $array;
	}
	
	private function parseParams($paramArray) {
		$params = '';
		foreach ($paramArray as $key => $value) {
			$params .= ' ' . $key . '="' . $value . '"';
		}
		return $params;
	}
	
	private function readConditions($field) {
		if ($conditions = $field->conditions) {
			foreach ($conditions->condition as $condition) {
				$output[] = $condition;
			}
			return $output;
		}
		return false;
	}
	
	private function matchConditions($field) {
		if ($conditions = $this->readConditions($field)) {
			
			foreach ($conditions as $condition) {
				$parts = explode(":", $condition);
				$name = $parts[0];
				$value = $parts[1];
				
				if (strstr($name, "[")) {
					$tmp = explode("[", $name);
					$name = $tmp[0];
					$key = rtrim($tmp[1], "]");
					
					$fieldValue = $this->getFieldValue($name, $key);
				}
				else {
					$fieldValue = $this->getFieldValue($name);
				}
				
				if ($fieldValue == $value) {							
					$field = $this->form->xpath('//field[@name="' . $name . '"]');
					$field = $field[0];
					return $this->matchConditions($field);
				}
			}
			return false;
		}
		return true;
	}
	
	private function getFriendlyLabel($name) {
		$title = $this->getFieldTitle($name);
		$label = $this->getFieldLabel($name);
		
		return (strlen($title) > 0) ? $title : $label;
	}
	
	private function getFriendlyValue($name, $value) {
		if ($field = $this->form->xpath('//field[@name="' . $name . '"]')) {
			$field = $field[0];
			
			if ($field->type == "checkbox") {
				foreach ($field->input as $input) {
					if ($input->key == $value) return $input->label;
				}
			}
			else if ($field->type == "radio") {
				foreach ($field->input as $input) {
					if ($input->params->value == $value) return $input->label;
				}
			}
			else if ($field->type == "select") {
				foreach ($field->input->options->option as $option) {
					if ($option->params->value == $value) return $option->title;
				}
			}		
		}
		
		return $value;
	}
	
	private function validationLabel($name) {
		$field = $this->form->xpath('//field[@name="' . $name . '"]');
		$field = $field[0];
		
		if ($field->validation) {
			$val = (string) $field->validation;
			$label = 'label_' . $val;
			
			if (isset(Validation::$$label)) return ' <span class="validation_label ' . $val . '">' . Validation::$$label . '</span>';
		}
		return false;
	}
	
	private function validationAriaRequired($name) {
		$field = $this->form->xpath('//field[@name="' . $name . '"]');
		$field = $field[0];
		
		if ($field->validation) {
			$val = (string) $field->validation;
			
			if ($val == "required") return ' aria-required="true"';
		}
	}
	
	private function fieldInfo($node) {
		if ($node->info) {
			return 
					"\n" . '<a href="#" class="info">More</a>' . 
					'<div class="toggle-info">' . $node->info . '</div>';
		}
	}
	
	private function getParentSectionName($name) {
		if ($section = $this->form->xpath('//field[@name="' . $name . '"]/../..')) return (string) $section[0]->attributes()->name;
		return "OUForms_NoSection";	
	}
	
	private function getParentFieldsetName($name) {
		if ($fieldset = $this->form->xpath('//field[@name="' . $name . '"]/..')) return (string) $fieldset[0]->attributes()->name;
		return "OUForms_NoFieldset";	
	}
	
	private function isPasswordField($name) {
		if ($field = $this->form->xpath('//field[@name="' . $name . '"]')) {
			if ($field[0]->type == "password") return "(Hidden)";
		}
		return false;
	}
	
	private function checkEditMode() {
		if (isset($_GET['edit'])) {
			$name = $_GET['edit'];
			$return_page = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Do not get the QS as this will cause a loop
			
			$_SESSION[$this->sessionId]['settings']['edit']['field'] = $name;
			$_SESSION[$this->sessionId]['settings']['edit']['return'] = $return_page;
			
			header("Location: " . $_SESSION[$this->sessionId]['pages'][$name] . '#fieldContainer_' . $name);
			exit();
		}
	}
	
	private function getEditField() {
		if (isset($_SESSION[$this->sessionId]['settings']['edit'])) return $_SESSION[$this->sessionId]['settings']['edit'];
		return false;
	}
	
	private function generateUuid() {
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
	
	private function getFiles( $getHidden = false ) {
		$files = array();
		$fields = $this->form->xpath( '//field' );
		
		foreach ( $fields AS $field ) {
			if ( (string) $field->type != 'file' ) {
				continue;
			}
			
			if ( ! $getHidden && ( ! $this->matchConditions( $field ) || $field->type == "hidden" ) ) {
				continue;
			}
			
			$key = (string) $field->attributes()->name;
			$section = $this->getParentSectionName( $key );
			$fieldset = $this->getParentFieldsetName ($key );
			
			if ( ! isset( $_SESSION[ $this->sessionId ]['uploads'][ $section ][ $fieldset ][ $key ] ) ) {
				continue;
			}
			
			$files[] = $_SESSION[ $this->sessionId ]['uploads'][ $section ][ $fieldset ][ $key ];
		}
		
		return $files;
	}
	
	private function clearFiles() {
		$files = $this->getFiles( true );
		
		foreach ( $files AS $file ) {
			$filePath = $this->pathUplTmp . $file['uuid'];
			@unlink( $filePath );
		}
	}
	
	private function checkAllSections() {
		$sections = $this->form->xpath('//section');

		foreach( $sections as $section ) {
			$sectionName = (string) $section[0]->attributes()->name;
			
			if ( ! isset( $_SESSION[ $this->sessionId ]['form'][ $sectionName ] ) ) {
				return false;
			}
		}
		return true;
	}
	
	private function getStartPage() {
		$startPage = $this->form->xpath('//startPage');
		return (string) $startPage[0];
	}

	private function sendEmail( $message, $mailSettings ) {

		$attachments = $this->getFiles();
		
		$headers = "From: {$mailSettings['from']}\n" .
				   "Reply-To: {$mailSettings['from']}\n" .
				   "X-Mailer: PHP/" . phpversion() . "\n" .
				   "MIME-Version: 1.0\n";
		
		if ( isset( $mailSettings['cc'] ) ) {
			$headers .= "Cc: {$mailSettings['cc']}\n";
		}
		
		if ( isset( $mailSettings['bcc'] ) ) {
			$headers .= "Bcc: {$mailSettings['bcc']}\n";
		}
		
		if ( count( $attachments ) > 0 ) {
			$semi_rand = md5( time() );
			$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
			
			$headers .= "Content-Type: multipart/mixed;\n" .
					    " boundary=\"{$mime_boundary}\"";
			
			$message = "This is a multi-part message in MIME format.\n\n" .
					   "--{$mime_boundary}\n" .
					   "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
					   "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n" .
					   "--{$mime_boundary}\n";
			
			foreach ( $attachments AS $i => $attachment ) {
				$data = chunk_split( base64_encode( file_get_contents( $this->pathUplTmp . $attachment['uuid'] ) ) );
				
				$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"{$attachment['name']}\"\n" .
		              		"Content-Disposition: attachment;\n" . " filename=\"{$attachment['name']}\"\n" .
		               		"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n" .
							"--{$mime_boundary}";
				
				$message .= ( $i + 1 == count( $attachments ) ) ? "--\n" : "\n";
			}
		}
		
		if ( mail( $mailSettings['to'], $mailSettings['subject'], $message, $headers ) ) {
			return true;
		}
		
		return false;
	}
	

}

// EOF