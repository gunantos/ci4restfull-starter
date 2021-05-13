<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class RestfullAutoList extends BaseConfig
{
    /**
     * Create property to new api
     * name must be lowercase
     * "-" caracter is replace to "_"
     */

     
    /**
     * followe Model Codeigniter 4 to configuration model
     * if you wont costume function add in APPATH/Restfull/FunctionAuto
     * or you can set in database function list
     */
    
    public $testing = [
        'model_name'=>null, //if you using create model
        'model_config'=> [
          'DBGroup'=>'default',
          'table'=>'dbTesting',
          'primaryKey'=>'id',
          'useAutoIncrement' => true,
          'useSoftDeletes'=>true,
          'allowedFields'=>[], 
          'useTimestamps'=>true,
          'createdField'=>'created',
          'updatedField'=>'updated',
          'deletedField'=>'deleted',
          'validationRules'=>[], 
          'validationMessages'=>[],
          'skipValidation'=>false,
          'beforeInsert'=>[],
          'beforeUpdate'=>[],
          'beforeDelete'=>[],
          'beforeFind'=>[],
          'afterFind'=>[],
          'afterInsert'=>[],
          'afterUpdate'=>[],
          'afterDelete'=>[]
        ]
    ];
}