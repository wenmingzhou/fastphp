<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/7
 * Time: 12:10
 */

// Ӧ��Ŀ¼Ϊ��ǰĿ¼
define('APP_PATH', __DIR__ . '/');

// ��������ģʽ
define('APP_DEBUG', true);

// ���ؿ���ļ�
require(APP_PATH . 'fastphp/Fastphp.php');

// ���������ļ�
$config = require(APP_PATH . 'config/config.php');

// ʵ���������
$fastphp =new fastphp\Fastphp($config);
$fastphp->run();