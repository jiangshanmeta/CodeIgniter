<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Common extends H_Controller{
    public $isLogin = false;
    function __construct(){
        parent::__construct();
        $this->load->model('records/User_model',"userInfo");
        $this->output->output_headers();
    }

    function login_verify($force=false){
        
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

    protected function _common_list($listInfo='listInfo'){
        $this->$listInfo->limit = $this->$listInfo->pagesize;
        $page = is_null($this->input->get('page'))?1:$this->input->get('page');
        $page = (int)$page;
        $this->$listInfo->skip = ($page-1)*$this->$listInfo->pagesize;

        $sort = $this->input->get('sort');
        if(!is_null($sort)){
            $ascOrDesc = substr($sort,0,1);
            if($ascOrDesc==='a'){
                $sortKey = 'asc';
            }else{
                $sortKey = 'desc';
            }
            $sortField = substr($sort,1);
            $this->$listInfo->orderBy = [$sortField=>$sortKey];
        }


        $this->$listInfo->load_data_with_where();

        $rst = [];
        $rst['data'] = $this->$listInfo->gen_show_array($this->$listInfo->fields);
        $rst['fields'] = $this->$listInfo->gen_table_title();
        $rst['pagesize'] = $this->$listInfo->pagesize;


        

        $modelConfig = $this->$listInfo->dataModel->loadConfig();
        $userPri = $this->userInfo->field_list['privilege']->value;
        if(isset($modelConfig['create_link']) && isset($modelConfig['create_privilege'])){
            if($this->__check_pri($modelConfig['create_privilege'],$userPri)){
                $rst['create_link'] = $modelConfig['create_link'];
                $rst['docreate_link'] = $modelConfig['docreate_link'];
            }
        }
        if(isset($modelConfig['edit_link']) && isset($modelConfig['edit_privilege'])){
            // 校验权限
            if($this->__check_pri($modelConfig['edit_privilege'],$userPri)){
                $rst['edit_link'] = $modelConfig['edit_link'];
                $rst['doedit_link'] = $modelConfig['doedit_link'];
            }
        }

        if(isset($modelConfig['detail_link'])){
            $rst['detail_link'] = $modelConfig['detail_link'];
        }

        if(isset($modelConfig['operators']) && is_array($modelConfig['operators']) ){
            $operators = [];
            foreach ($modelConfig['operators'] as $this_operator) {
                if(!isset($this_operator['privilege'])){
                    continue;
                }
                if($this->__check_pri($this_operator['privilege'],$userPri)){
                    $operators[] = [
                        'label'=>$this_operator['label'],
                        'icon'=>$this_operator['icon'],
                        'link'=>$this_operator['link'],
                    ];
                }
            }

            if(!empty($operators)){
                $rst['operators'] = $operators;
            }
        }

        $rst['filter'] = $this->_gen_filters($listInfo);
        // $rst['__config'] = $this->$listInfo->dataModel->loadConfig();
            // $rst['filter'] = $this->$listInfo->gen_filter_info();
        // }
        $rst['total'] = $this->$listInfo->count_data_with_where();

        echo $this->exportData($rst,1);
    }

    protected function _gen_filters($listInfo='listInfo'){
        $rst = [];
        if(isset($this->filters)){
            foreach ($this->filters as $key => $value) {
                if(!isset($value['value']) ){
                    continue;
                }
                $item = [];
                if(substr($key,0,5)==='enum_'){
                    
                    $field_name = substr($key,5);
                    $item['editor'] = 'filter_enum';
                    $item['value'] = $value['value'];
                    if(isset($value['label'])){
                        $item['label'] = $value['label'];
                    }else{
                        $item['label'] = $this->$listInfo->dataModel->field_list[$field_name]->gen_show_name();
                    }

                    if(isset($value['candidate'])){
                        $item['candidate'] = $value['candidate'];
                    }else{
                        $item['candidate'] = $this->$listInfo->dataModel->field_list[$field_name]->gen_candidate();
                    }
                    $item['field'] = $key;
                }else if(substr($key,0,3)=== 'ts_' ){

                    if(isset($value['field'])){
                        $field_name = $value['field'];
                    }else{
                        $field_name = substr($key,3);
                    }
                    $item['editor'] = isset($value['editor'])?$value['editor']:'filter_day';
                    $item['value'] = $value['value'];
                    if(isset($value['label']) ){
                        $item['label'] = $value['label'];
                    }else{
                        $item['label'] = $this->$listInfo->dataModel->field_list[$field_name]->gen_show_name();
                    }
                    $item['field'] = $key;


                }

                $rst[] = $item;
            }
        }
        return $rst;
    }

    private function __check_pri($itemPri,$userPri){
        $hasPri = false;
        foreach ($userPri as $this_pri) {
            if(in_array($this_pri,$itemPri)){
                $hasPri = true;
                break;
            }
        }
        return $hasPri;
    }



    function create($typ){

        $modelName = 'records/'.ucfirst($typ).'_model';
        $this->load->model($modelName,'dataInfo');
        $fields = [];
        $create_show_fields = $this->dataInfo->buildCreateShowFields();
        foreach ($create_show_fields as $row) {
            $rowarr = [];
            foreach ($row as $this_field) {
                $rowarr[] = $this->dataInfo->field_list[$this_field]->gen_editor_info('default');
            }
            $fields[] = $rowarr;
        }
        $data = [];
        $data['fields'] = $fields;
        echo $this->exportData($data,1);
    }

    function doCreate($typ){
        $modelName = 'records/'.ucfirst($typ).'_model';
        $this->load->model($modelName,'dataInfo');

        $data = [];
        foreach ($this->dataInfo->field_list as $key => $value) {
            $v = $this->input->post($key);
            if(is_null($v)){
                if($this->dataInfo->field_list[$key]->is_must_input){
                    $this->show_error("请填写必填字段");
                }
                continue;
            }
            $data[$key] = $this->dataInfo->field_list[$key]->gen_value($v);
        }
        $this->dataInfo->insert_db($data);

        echo $this->exportData(['id'=>$this->dataInfo->id],1);
    }


    function update($typ,$id){
        $modelName = 'records/'.ucfirst($typ).'_model';
        $this->load->model($modelName,'dataInfo');
        $this->dataInfo->init_with_id($id);
        $fields = [];
        $edit_show_fields = $this->dataInfo->buildEditShowFields();
        foreach ($edit_show_fields as $row) {
            $rowarr = [];
            foreach ($row as $this_field) {
                $rowarr[] = $this->dataInfo->field_list[$this_field]->gen_editor_info('value');
            }
            $fields[] = $rowarr;
        }
        $data = [];
        $data['fields'] = $fields;
        echo $this->exportData($data,1);
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

        echo $this->exportData([],1);
    }


    function doDelete($typ,$id){
        $modelName = 'records/'.ucfirst($typ).'_model';
        $this->load->model($modelName,'dataInfo');
        if(!$this->dataInfo->check_can_delete($id)){
            $lastError = $this->dataInfo->getLastError();
            $this->show_error($lastError['msg'],$lastError['errno']);
        }
        $this->dataInfo->delete_db($id);
        echo $this->exportData([],1);
    }

    protected function _limitEnum($field,$obj='listInfo'){
        $filterKey = 'enum_'.$field;
        $frontValue = $this->input->get($filterKey);
        if(is_null($frontValue) && isset($this->filters) && isset($this->filters[$filterKey]) && isset($this->filters[$filterKey]['default']) ){
            $frontValue = $this->filters[$filterKey]['default'];
        }

        if(is_string($frontValue) &&  is_numeric($frontValue)){
            $frontValue = (int)$frontValue;
        }

        if(isset($this->filters) && isset($this->filters[$filterKey])){
            $this->filters[$filterKey]['value'] = $frontValue;
        }

        if(is_int($frontValue)){
            $this->$obj->add_where(WHERE_TYPE_WHERE,$field,$frontValue);
        }

    }

    protected function _limitTS($field,$fromOrTo=NULL,$obj='listInfo'){
        $filterKey = 'ts_'.$field;
        $frontValue = $this->input->get($filterKey);

        if(is_null($frontValue) && isset($this->filters) && isset($this->filters[$filterKey]) && isset($this->filters[$filterKey]['default']) ){
            $frontValue = date('Y-m-d',strtotime($this->filters[$filterKey]['default'] ) );
        }

        if(isset($this->filters) && isset($this->filters[$filterKey])){
            $this->filters[$filterKey]['value'] = $frontValue;
            if(!isset($this->filters[$filterKey]['mode'])){
                $this->filters[$filterKey]['mode'] = 'from';
            }
        }

        if(is_null($fromOrTo)){
            if(isset($this->filters[$filterKey]['mode'])){
                $finalFromOrTo = $this->filters[$filterKey]['mode'];
            }else{
                $finalFromOrTo = 'from';
            }
        }else{
            $finalFromOrTo = $fromOrTo;
        }

        $ts = strtotime($frontValue);
        if($finalFromOrTo==='from'){
            $where = WHERE_TYPE_WHERE_GTE;

        }else{
            $where = WHERE_TYPE_WHERE_LTE;
            $ts += 86400;
        }

        $this->$obj->add_where($where,$field,$ts);
    }



}
?>