<?php
namespace fastphp\core;
use fastphp\core\memcache;

class cache{
    public static function factory($cache)
    {
        if($cache=='memcache') {
            $mem = new memcache();
        }else
        {
            $mem = new redis();
        }

        return new $mem;
    }


}