<?php
namespace app\controllers;
use fastphp\base\Controller;


class ItemController extends Controller
{
    // 首页方法，测试框架自定义DB查询

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        $this->assign('title', '全部条目');
        $this->assign('descrition', '描述');

        echo "welcome to my first php <br/>";
        $this->render();
    }


}