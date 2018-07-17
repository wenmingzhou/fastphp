<?php
namespace fastphp\core;



class memcache
{
    public $memcache;
    function connect($ip,$port)
    {
        $this->memcache = memcache_connect($ip, $port);
    }
    function get($key)
    {
        $val =$this->memcache->get($key);
        return $val;
    }
    function set($key,$val,$flag,$timeout)
    {
        $this->memcache->set($key, $val,$flag,$timeout);
    }
    function delete($key)
    {
        $this->memcache->delete($key);
    }

}