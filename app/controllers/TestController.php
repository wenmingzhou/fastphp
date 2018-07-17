<?php
namespace app\controllers;
use fastphp\base\Controller;
use fastphp\core\cache;
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
        $cache->delete($key);
        echo $cache->get($key);

    }
}