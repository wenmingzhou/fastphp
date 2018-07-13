<?php
namespace app\controllers;
use fastphp\base\Controller;
use app\models\itemModel;
use fastphp\core\page;


class ItemController extends Controller
{
    // 首页方法，测试框架自定义DB查询

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $showrow = 10; //一页显示的行数
        $curpage = empty($_GET['page']) ? 1 : $_GET['page'];
        $limit = ($curpage - 1) * $showrow;
        if($keyword)
        {
            $items =(new itemModel())->where(["item_name like '%$keyword%'"])->order(['id DESC'])->limit(array($limit,$showrow))->fetchAll();
            $items_total =(new itemModel())->where(["item_name like '%$keyword%'"])->order(['id DESC'])->fetchAll();
        }else
        {
            //['id = :id'], [':id' => $id]
            $items=(new itemModel())->where()->order(['id DESC'])->limit(array($limit,$showrow))->fetchAll();
            $items_total=(new itemModel())->where()->order(['id DESC'])->fetchAll();
        }
        $total_num =count($items_total);//总条数
        $url = "?page={page}&keyword=".$keyword;
        $page =new page($total_num,$showrow,$curpage,$url);

        $new_pagenavi= $page->myde_write();

        $this->assign('title', 'Welcome To My First Php Frame');
        $this->assign('descrition', '描述');
        $this->assign('items',$items);
        $this->assign('new_pagenavi',$new_pagenavi);
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

    public function edit($id)
    {
        $item =(new itemModel())->where(["id = ?"], [$id])->fetch();
        if(isset($_POST['item_name']))
        {
            //['id = 1','and title="Web"', ...]
            //["id = $id"]
            //['id = :id'], [':id' => $id]
            $data  = array('item_name' => $_POST['item_name'], 'description' => trim($_POST['description']));
            $count = (new ItemModel)->where(['id = :id'], [':id' => $id])->update($data);
            $this->assign('title', '修改成功');
            $this->assign('count', $count);

        }else {
            $this->assign('title', '条目详情编辑');
        }
        $this->assign('item',$item);
        $this->render();
    }


    public function add()
    {
        if(isset($_POST['item_name']))
        {
            $data  = array('item_name' => $_POST['item_name'], 'description' => trim($_POST['description']));
            (new ItemModel)->add($data);
            $this->assign('title', '添加成功');
        }else {
            $this->assign('title', '添加数据');
        }
        $this->render();
    }

    public function delete($id)
    {
        $count =(new ItemModel)->delete($id);
        if($count)
        {
            echo '<script>window.history.go(-1); </script>';
            echo '<script>location.reload(); </script>';exit;
        }


    }


}