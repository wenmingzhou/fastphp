<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 15:51
 */
namespace fastphp\core;

//选择指定的redis数据库连接，默认端口号为6379
class redis
{
    public $redis;
    public function connect($ip,$port)
    {
        $this->redis = new \Redis();
        $this->redis->connect($ip, $port);
    }

    public function get($key)
    {
        $val = $this->redis->get($key);
        return $val;
    }
    public function set($key,$val,$flag,$timeout)
    {
        //$this->redis->set($key, $val);
        $this->redis->setex($key, $timeout, $val);
    }

    public function delete($key)
    {
        $this->redis->delete($key);
    }
}