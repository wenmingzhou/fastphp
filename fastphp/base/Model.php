<?php
namespace fastphp\base;
use fastphp\db\Sql;


class Model extends Sql{

    protected $model;

    protected $db;

    public function __construct($db)
    {

        if(!$this->table)
        {
            $this->model =get_class($this);
            $this->model =substr($this->model,0,-5);
            $this->model =strtolower($this->model);
        }
        $this->db =$db;

    }
}