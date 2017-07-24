<?php
class Menu {
    protected $menu;
    function __construct(&$menu=[]){
        $this->menu = $menu;
    }

    function getMenuByPrivilege($userPri){
        $rst = [];
        foreach ($this->menu as $key => $submenu) {
            $subitem = [];
            foreach ($submenu as $submenuKey => $submenuInfo) {
                if(!isset($submenuInfo['privilege'])){
                    continue;
                }
                $menuPri = $submenuInfo['privilege'];
                $hasPri = false;
                foreach ($userPri as $value) {
                    if(in_array($value, $menuPri)){
                        $hasPri = true;
                        break;
                    }
                }
                if($hasPri){
                    $subitem[$submenuKey] = $submenuKey;
                }
            }
            if(!empty($subitem)){
                $rst[$key] = $subitem;
            }
        }


        return $rst;
    }


}
?>