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
        $val ='zhou wenming ';
        $cache->set($key,$val,false,30);
        $cache->delete($key);
        echo $cache->get($key);

    }
}