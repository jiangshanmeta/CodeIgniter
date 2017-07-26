<?
require_once('Fields.php');
class Field_ts extends Fields{
	public $real_value;
	public function __construct($show_name,$name,$is_must_input=FALSE){
		parent::__construct($show_name,$name,$is_must_input);
		$this->typ = 'Field_ts';
		$this->editor_typ = 'field_ts';
		$this->default = '';
	}

	function init($input){
		$this->real_value = $this->gen_value($input);
		$this->value = $this->gen_show_value();
	}

	public function gen_value($input){
		if(is_string($input) && !is_numeric($input)){
			$input = strtotime($input);
		}
		return (int)$input;
	}
	public function gen_show_value(){
		if($this->real_value<86400){
			return "";
		}
		return date('Y-m-d H:i',$this->real_value);
	}

	public function gen_vm_value(){
		return $this->gen_show_value();
	}
}
?>