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
            //['id = :id'], [':id' => $id]
            $items=(new itemModel())->where()->order(['id DESC'])->fetchAll();
        }

        $this->assign('title', 'Welcome To My First Php Frame');
        $this->assign('descrition', '描述');
        $this->assign('items',$items);
        $this->render();
    }


    public function detail($id)
    {
        //$item =(new itemModel())->where(["id = ?"], [$id])->fetch();
        $item =(new itemModel())->where(["id = ?"], [$id])->fetch();
        $this->assign('title','条目详情');
        $this->assign('item',$item);
        $this->render();
    }



}