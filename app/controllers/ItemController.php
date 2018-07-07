<?php
namespace app\controllers;



class ItemController
{
    // 首页方法，测试框架自定义DB查询

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        
        echo "welcome to php ";
    }


}