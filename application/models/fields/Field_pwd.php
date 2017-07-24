<?
require_once('Field_string.php');
class Field_pwd extends Field_string{
	public function __construct($show_name,$name,$is_must_input=FALSE){
		parent::__construct($show_name,$name,$is_must_input);
		$this->typ = 'Field_pwd';
		$this->editor_typ = 'field_pwd';
	}
	public function gen_value($value){
		return strtolower(md5($value));
	}

	public function gen_show_value(){
		return '';
	}

	public function gen_vm_value(){
		return '';
	}

	public function init($value){
		$this->value = (string)$value;
	}
	public function set_default($default){
		$this->default = (string)$default;
	}



}
?>