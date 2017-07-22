<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Common extends H_Controller{
    public $isLogin = false;
    function __construct(){
        parent::__construct();
    }

    function login_verify($force=false){
        $this->load->model('records/User_model',"userInfo");
        $this->load->library('Login','login');
        if(!$this->login->isLogin()){
            $this->isLogin = false;
            if($force){
                $this->goto_login();
            }
            return;
        }

        $this->isLogin = true;
        $this->userInfo->init_with_id($this->login->uid);
    }

    function goto_login(){
        if($this->login->mode === 'token'){
            set_status_header(412);
            echo $this->exportData([],-1);
        }
    }


    function show_error($msg='数据有误',$status=-1){
        $this->output->output_headers();
        switch ($this->viewType) {
            // case VIEW_TYPE_HTML:
            //     $this->topTyp = "gobacktop";
            //     $this->top_title = $msg;
            //     ob_start();
            //     $buffer = $this->template->load('default_page', 'common/404','',TRUE);
            //     @ob_end_clean();
            //     echo $buffer;
            //     break;
            case VIEW_TYPE_JSON:
                $jsonRst = $status;
                $jsonData = array();
                $jsonData['err']['msg'] = $msg;
                echo $this->exportData($jsonData,$jsonRst);  
                break;
            // case VIEW_TYPE_PAGE:
            //     $this->topTyp = "gobacktop";
            //     $this->top_title = $msg;
            //     ob_start();
            //     $buffer = $this->template->load('default_overlay', 'common/404','',TRUE);
            //     @ob_end_clean();
            //     echo $buffer;
            //     break;
            default:
                # code...
                break;
        }
        exit();
    }
    function create(){

    }

    function doCreate($typ,$id){
        $modelName = 'records/'.ucfirst($typ).'_model';
        $this->load->model($modelName,'dataInfo');

        $data = [];
        foreach ($this->dataInfo->field_list as $key => $value) {
            $v = $this->input->post($key);
            if($v===NULL){
                if($this->dataInfo->field_list[$key]->isMustInput){
                    $this->show_error("请填写必填字段");
                }
                continue;
            }
            $data[$key] = $this->dataInfo->field_list[$key]->gen_value($v);
        }
        $id = $this->dataInfo->insert_db($data);

        $this->exportData(['id'=>(string)$id],1);
    }


    function update(){

    }
    function doUpdate($typ,$id){
        $modelName = 'records/'.ucfirst($typ).'_model';
        $this->load->model($modelName,'dataInfo');
        $this->dataInfo->init_with_id($id);
        $this->dataInfo->check_inited();

        $data = [];
        foreach ($this->dataInfo->field_list as $key => $value) {
            $v = $this->input->post($key);
            if($v===NULL){
                continue;
            }
            $newValue = $this->dataInfo->field_list[$key]->gen_value($v);
            if($newValue!==$this->dataInfo->field_list[$key]->value){
                $data[$key] = $newValue;
            }
        }
        if(empty($data)){
            $this->show_error("无变化");
        }
        $this->dataInfo->update_db($data);

        $this->exportData([],1);
    }


    function doDelete($typ,$id){
        $modelName = 'records/'.ucfirst($typ).'_model';
        $this->load->model($modelName,'dataInfo');
        if(!$this->dataInfo->check_can_delete($id)){
            $lastError = $this->dataInfo->getLastError();
            $this->show_error($lastError['msg'],$lastError['errno']);
        }
        $this->dataInfo->delete_db($id);
        $this->exportData([],1);
    }



}
?>