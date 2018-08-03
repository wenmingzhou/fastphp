<?php
namespace app\controllers;
use fastphp\base\Controller;
use fastphp\core\cache;
use fastphp\core\verify;
use fastphp\core\tool;

class TestController extends Controller
{
    public function index()
    {
        global $config;
        $cache =cache::factory($config['cache']['type']);
        $cache->connect($config['cache']['ip'],$config['cache']['port']);
        $key ="00000";
        $val ='zhou wenming liujing 10';
        $cache->set($key,$val,false,15);
        //$cache->delete($key);
        echo $cache->get($key);
    }


    public function verify()
    {
        $verify = new verify();
        $str ='111.com';
        $result =$verify->is_url($str);
        echo $result;
    }

    public function search()
    {
        $dir  ='D:\wamp\www\workbench\admin';
        $stime ='1532102399';
        $tool =new tool($stime);
        $tool->loopdir($dir);
    }
}