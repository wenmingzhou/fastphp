<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 10:29
 */
namespace fastphp\core;
class verify{
    /**
     * 是否为空值
     */
    public function is_empty($str)
    {
        $str = trim($str);
        return !empty($str) ? 1 : 0;

    }

    /**
     * 是否2位的浮点数
     */

    public function is_float($subject)
    {
        $pattern ='/\d+\.\d{2}$/';
        $num =preg_match($pattern,$subject);
        return $num;
    }

    /**
     * 是否11位的手机号
     */

    public function is_phone($subject)
    {
        $pattern ='/1[35789]\d{9}$/';
        $num =preg_match($pattern,$subject);
        return $num;
    }

    /*
     * 是否是邮箱
     * 524797132@qq.com
     * zhou.wen.ming@yahoo.com.cn
     */

    public function is_email($subject)
    {
        $pattern  ='/\w+(\.\w+)*@\w+(\.\w+)+/';
        $num =preg_match($pattern,$subject);
        return $num;
    }


    /*
     * 是否为网址
     *http://www.baidu.com
     *https://www.suing.com
     *https://pay.com
     *www.cctv.com
     *cctv.net
     *
     */
    public function is_url($subject)
    {
        $pattern ='/(https?:\/\/)?(\w+\.)+(com|cn)/';
        $num =preg_match($pattern,$subject);
        return $num;
    }


}