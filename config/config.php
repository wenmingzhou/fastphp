<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/7
 * Time: 12:13
 */

//数据库配置
//主库
$config['master']['host'] = '127.0.0.1';
$config['master']['username'] = 'root';
$config['master']['password'] = '';
$config['master']['dbname'] = 'fastphp';

//从库
$config['slave']['host'] = '127.0.0.1';
$config['slave']['username'] = 'root';
$config['slave']['password'] = '';
$config['slave']['dbname'] = 'fastphp';

// 默认控制器和操作名
$config['defaultController'] = 'Item';
$config['defaultAction'] = 'index';

$config['cache']['type']='memcache';
$config['cache']['ip']='localhost';
$config['cache']['port']='11211';

return $config;