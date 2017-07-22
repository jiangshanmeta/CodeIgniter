<?php
class H_Output extends CI_Output{
    function __construct(){
        parent::__construct();
        $this->set_header("Access-Control-Allow-Origin: *");
        $this->set_header("Access-Control-Allow-Credentials: true");
        $this->set_header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        // 因为复杂cors会先发options请求，查询多余的请求头字段是否允许
        $this->set_header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Token, Accept, System");

        $charset = strtoupper(config_item('charset'));
        $SEC =& load_class('Security', 'core', $charset);
        $input = & load_class('Input', 'core',$SEC);
        
        $method = $input->server('REQUEST_METHOD');
        if($method==='OPTIONS'){
            $this->output_headers();
            exit();
        }

    }

}
