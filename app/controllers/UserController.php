<?php
namespace app\controllers;
use fastphp\base\Controller;
use fastphp\core\cache;
use fastphp\core\verify;
use fastphp\core\uploadpic;
use app\models\UserModel;


class UserController extends Controller
{
    public function index()
    {
        echo "user info ...";
    }

    public function register()
    {

        if (isset($_POST) && !empty($_POST['name']))
        {
            $name =$_POST['name'];
            $password =md5(trim($_POST['password']));
            $file = $_FILES['file'];
            $dir =APP_PATH.'/images/';
            $uploadpic =new uploadpic(true, $dir, array('jpg', 'jpeg', 'png'));
            $uploadpic->upload_file($file);
            echo $uploadpic->get_msg();

            $data  = array('name' => $_POST['name'], 'password' => $password);
            (new UserModel('master'))->add($data);


        }
        $this->assign('title', 'Welcome To register');
        $this->render();
    }
}