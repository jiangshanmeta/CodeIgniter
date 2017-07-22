<?
defined('BASEPATH') OR exit('No direct script access allowed');
include_once('Common.php');
class Index extends Common{
    function __construct(){
        parent::__construct();
    }

    function doLogin(){
        $phone = $this->input->post('phone');
        $pwd = $this->input->post('pwd');
        $this->load->model('records/User_model',"userInfo");
        if(!$this->userInfo->verify_login($phone,$pwd)){
            // 验证失败
            $error = $this->userInfo->getLastError();
            $this->show_error($error['msg'],$error['errno']);
        }

        $this->load->library('Login','login');
        $token = $this->login->doLogin($this->userInfo->id);

        echo $this->exportData(['token'=>$token],1);
    }

    function fetchUser(){
        $this->login_verify(true);
        $data['name'] = $this->userInfo->field_list['name']->value;
        $data['privilege'] = $this->userInfo->field_list['privilege']->value;

        echo $this->exportData($data,1);
    }

    function doLogout(){
        $this->load->library('Login','login');
        $this->login->doLogout();

        echo $this->exportData([],1);
    }

}
?>