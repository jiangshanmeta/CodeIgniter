<?
require_once('Fields.php');
class Field_string extends Fields{
	public function __construct($show_name,$name,$is_must_input=FALSE){
		parent::__construct($show_name,$name,$is_must_input);
		$this->set_default('');
		$this->typ = 'Field_string';
		$this->editor_typ = 'field_string';
	}
	public function gen_value($input){
		return (string)$input;
	}

}
?>