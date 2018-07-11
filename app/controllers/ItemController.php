<?php
namespace app\controllers;
use fastphp\base\Controller;
use app\models\itemModel;


class ItemController extends Controller
{
    // 首页方法，测试框架自定义DB查询

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        if($keyword)
        {
            $items =(new itemModel())->search($keyword);
        }else
        {
            $items=(new itemModel())->where()->order(['id DESC'])->fetchAll();
        }

        $this->assign('title', '全部条目');
        $this->assign('descrition', '描述');

        echo "welcome to my first php <br/>";
        $this->render();
    }



}