<?
require_once('Fields.php');
class Field_int extends Fields{
	public function __construct($show_name,$name,$is_must_input=FALSE){
		parent::__construct($show_name,$name,$is_must_input);
		$this->set_default(0);
		$this->typ = 'Field_int';
		$this->editor_typ = 'field_int';
	}
	public function gen_value($input){
		return (int)$input;
	}



}
?>