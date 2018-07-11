<?php
namespace fastphp\base;
use fastphp\base\view;
class Controller{
    public $_controller;
    public $_action;
    public $_view;

    public function __construct($controller,$action)
    {
        $this->_controller      =$controller;
        $this->_action          =$action;
        $this->_view            =new View($controller,$action);

    }


    public function assign($name, $value)
    {
        $this->_view->assign($name, $value);

    }

    public function render()
    {
        $this->_view->render();
    }

}