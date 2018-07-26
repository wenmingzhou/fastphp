<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 16:34
 */
namespace fastphp\core;
class tool
{

    private $stime;

    public function __construct($stime)
    {
        $this->stime=$stime;
    }

    public function loopdir($dir)
    {

        $handle =opendir($dir);
        while (($file=readdir($handle))!==false)
        {

            if($file!='.' && $file!='..' && $file!='.idea' && $file!='.git') {

                if(is_dir($dir . '/' . $file))
                {
                    $desc ="<div style='color: red;'>目录名     ++</div>";
                }else
                {
                    $desc ="<div style='color:blue'></div>";
                }

                $edittime =filemtime($dir . '\\' . $file);  //文件修改时间
                //echo $edittime;die;
                $searchtime =$this->stime;
                if($edittime>=$searchtime) {

                    $showtime = date('Y-m-d H:m:s', $edittime);
                    echo $desc;
                    echo "<span style='margin-right:60px; '>$file</span>" . "<span style='margin-right:60px; '>修改时间</span>" . $edittime . "-------------" . $showtime . "<br/>";
                    if (filetype($dir . '\\' . $file) == 'dir') {
                        //echo $this->dir . '\\' . $file;die;
                        $this->loopdir($dir . '\\' . $file);
                    }
                }
            }
        }
    }
}