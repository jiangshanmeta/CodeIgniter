<?
class Fields{
	// 统一成下划线命名
	// 属性在前，方法在后
	// 静态在前，实例在后
	// 公有在前，私有在后
	static protected $_cache_enum = [];
	static protected $_cache_model = [];
	static protected $_cache_model_candidate = [];

	public $tips;
	public $value;
	public $default;
	public $typ;
	public $placeholder;

	protected $show_name;
	protected $name;
	protected $is_must_input;
	protected $editor_typ;

	public function __construct($show_name,$name,$is_must_input=FALSE){
		$this->show_name = $show_name;
		$this->name = $name;
		$this->is_must_input = $is_must_input;
	}
	public function gen_show_name(){
		return $this->show_name;
	}

	public function init($value){
		$this->value = $this->gen_value($value);
	}
	public function gen_value($input){
		return $input;
	}
	public function set_default($value){
		$this->default = $this->gen_value($value);
	}
	public function gen_show_value(){
		return $this->value;
	}
	public function gen_vm_value(){
		return $this->value;
	}

	public function gen_editor($typ){

	}

	public function gen_editor_info($mode='value'){
		$data = [];
		$data['editor'] = $this->editor_typ;
		$data['placeholder'] = $this->placeholder;
		$data['value'] = $this->$mode;
		$data['field'] = $this->name;
		$data['label'] = $this->show_name;
		if(method_exists($this, 'gen_candidate')){
			$data['candidate'] = $this->gen_candidate();
		}
		return $data;
	}

}
?>