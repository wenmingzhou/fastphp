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

            $uploadpic =new uploadpic(true, array('jpg', 'jpeg', 'png'));
            $uploadpic->setMaxSize(10240);

            //是否添加水印
            $uploadpic->setWatermark(true);
            $uploadpic->setWatermarkType(2);
            $uploadpic->setWatermarkPosition("rb");

            $logo = 'http://esf.s.house365.com/zsb/images/v1.0/logo_watermark.png';

            $logo = APP_PATH."static/images/logo.png";  //fix by : wmc
            $uploadpic->setWatermarkImage($logo);//水印



            //压缩图片,小图需要的尺寸
            $uploadpic->setResizeImage(true);
            $uploadpic->setResizeWidth(240);
            $uploadpic->setResizeHeight(180);

            $uploadpic->upload_file($file);
            echo $uploadpic->get_msg();


            $data  = array('name' => $_POST['name'], 'password' => $password);
            (new UserModel('master'))->add($data);

        }
        $this->assign('title', 'Welcome To register');
        $this->render();
    }
}