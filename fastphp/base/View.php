<?php
namespace fastphp\base;
class View{

    public $variables =array();
    public $_controller;
    public $_action;

    public function __construct($controller,$action)
    {
        $this->_controller  =$controller;
        $this->_action  =$action;
    }

    public function assign($name, $value)
    {
        $this->variables[$name] =$value;
    }
    public function render()
    {
        //extract函数从数组中将变量导入到当前的符号表
        extract($this->variables);

        $defaultHeader = APP_PATH . 'app/views/header.php';
        $defaultFooter = APP_PATH . 'app/views/footer.php';

        $controllerHeader =APP_PATH.'app/views/'.$this->_controller.'/header.php';
        $controllerFooter =APP_PATH.'app/views/'.$this->_controller.'/footer.php';
        $controllerLayout =APP_PATH.'APP/views/'.$this->_controller.'/'.$this->_action.'.php';

        //加载头部文件
        if(is_file($controllerHeader))
        {
            include ($controllerHeader);
        }else
        {
            include ($defaultHeader);
        }

        if(is_file($controllerLayout))
        {
            include ($controllerLayout);
        }else
        {
            echo "无法加载视图文件";
        }

        //加载尾部文件
        if(is_file($controllerFooter))
        {
            include ($controllerFooter);
        }else
        {
            include ($defaultFooter);
        }



    }
}