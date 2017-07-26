<?php
class H_Config extends CI_Config{
    public $_model_paths = [APPPATH];
    function __construct(){
        parent::__construct();

    }

    function model($modelName){
        $modelName = strtolower($modelName);
        foreach ($this->_model_paths as $location) {
            $file_path = $location.'config/models/'.$modelName.'.php';
            if(!file_exists($file_path)){
                continue;
            }
            include($file_path);
            if(!isset($config) || !is_array($config)){
                show_error('Your '.$file_path.' file does not appear to contain a valid configuration array.');
            }
            return $config;
        }

        show_error('The configuration file '.$modelName.'.php does not exist.');
    }
}
?>