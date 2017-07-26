<?php
include_once(APPPATH."models/record_model.php");
class User_model extends Record_model{
    function __construct(){
        parent::__construct('aUser');

        $this->field_list['_id'] = $this->load->field('Field_mongoid','uid','otherContactor');
        $this->field_list['name'] = $this->load->field('Field_string',"姓名","name",true);
        $this->field_list['phone'] = $this->load->field('Field_string',"电话","phone",true);
        $this->field_list['pwd'] = $this->load->field('Field_pwd',"pwd","pwd",true);

        $this->field_list['typ'] = $this->load->field('Field_enum',"身份","typ",false,__CLASS__);

        $this->field_list['typ']->set_enum(array(
            0=>"离职",
            1=>"维修技师",
            2=>"客服",
            3=>'前台行政',
            4=>'美容技师',
            5=>'服务顾问',
            10=>'店长',
            11=>'外部销售',
            99=>'总部员工',
            999=>'总部管理',
            
        ));

        $this->field_list['tags'] = $this->load->field('Field_tag',"擅长","tags",false,__CLASS__);
        $this->field_list['tags']->set_enum(array(0=>"不限",1=>"德系",2=>"标雪、DS"));

        $this->field_list['privilege'] = $this->load->field('Field_tag',"权限","privilege",false,__CLASS__);
        $this->field_list['privilege']->set_enum(array(
            0=>"核对审核",1=>"审批付款",2=>"修改付款状态",
            50=>"店长",51=>"门店库管",52=>"门店前台行政",53=>"门店服务顾问",54=>"门店运营",
            55=>"门店技师",56=>'维修工组',57=>'美容工组',
            60=>"品牌/配件经理",61=>"总店运营",62=>'客服',63=>'配件自助修改',80=>'财务权限',
            100=>"服务等底层配置",101=>"网站新闻",102=>"促销活动",103=>"客户管理",104=>"订单管理",105=>'电商管理',
            201=>'投资人',202=>'销售',203=>'开奖通知',204=>'代付权限',205=>'人事',
            206=>'工资计算',
            301=>'电销',302=>'投诉分析'
        ));

        $this->field_list['quitTS'] = $this->load->field('Field_ts',"注册时间","quitTS");
    }

    public function verify_login($phone,$pwd){
        $this->init_with_where(['phone'=>$phone]);
        if(!$this->is_inited){
            $this->setLastError('用户名不存在');
            return false;
        }

        if($this->field_list['pwd']->gen_value($pwd) !== $this->field_list['pwd']->real_value ){
            $this->setLastError('密码有误');
            return;
        }

        return true;
    }

    function buildCreateShowFields(){
        return [
            ['name','phone'],
            ['pwd','typ'],
            ['tags'],
            ['quitTS']

        ];
    }

    function buildEditShowFields(){
        return [
            ['name','phone'],
            ['pwd','typ'],
            ['tags','quitTS'],
            ['privilege'],
        ];  
    }


}
?>