<?php
namespace fastphp\core;
class uploadpic{
    private $intMaxSize     = '';       //设置上传文件的大小，此为2M
    private $rand_name      = true;     //是否采用随机命名
    private $allow_type     = array();  //允许上传的文件扩展名
    private $error          = 0;        //错误代号
    private $msg            = '';       //信息
    private $new_name       = '';       //上传后的文件名
    private $save_path      = '';       //文件保存路径
    private $uploaded       = '';       //路径.文件名
    private $file           = '';       //等待上传的文件
    private $file_type      = array();  //文件类型
    private $file_ext       = '';       //上传文件的扩展名
    private $file_name      = '';       //文件原名称
    private $file_size      = 0;        //文件大小
    private $file_tmp_name  = '';       //文件临时名称
    private $needImageCut   =false;     //是否需要裁剪

    private $needResizeCut  =false;     //是否需要调整的大小
    private $intResizeWidth ='720';     //默认调整后的宽度
    private $intResizeHeight='480';     //默认调整后的高度

    //以下为添加水印相关
    var $needWatermark=false;//是否添加水印
    var $intWatermarkType=1;//添加水印类型(1为文字,2为图片)
    var $strWatermarkPosition="rb";//水印位置 lt左上；lb左下；rt右上；rb右下；ct中上；cb中下；lc左中；rc右中；cc中间；sj随机
    var $strWatermarkString="HOUSE365.COM"; //水印字符串
    var $strWatermarkImage="";//水印图片
    var $intWatermarkSize=24;//文字水印字体大小
    var $strWatermarkFont="";//文字水印字体

    private $needResizeReality=true;//设置是否保持调整图逼真不变形（按宽高大比例项缩放）
    private $strResizeImagePrefixion;
    /**
     * 构造函数，初始化
     * @param string $rand_name 是否随机命名
     * @param string $save_path 文件保存路径
     * @param string $allow_type 允许上传类型
    $allow_type可为数组  array('jpg', 'jpeg', 'png', 'gif');
    $allow_type可为字符串 'jpg|jpeg|png|gif';中间可用' ', ',', ';', '|'分割
     */
    public function __construct($rand_name=true, $allow_type=''){
        $this->rand_name = $rand_name;
        $this->save_path =  APP_PATH.'/images/';;
        $this->allow_type = $this->get_allow_type($allow_type);
    }

    /**
     * 上传文件
     * 在上传文件前要做的工作
     * (1) 获取文件所有信息
     * (2) 判断上传文件是否合法
     * (3) 设置文件存放路径
     * (4) 是否重命名
     * (5) 上传完成
     * @param array $file 上传文件
     *     $file须包含$file['name'], $file['size'], $file['error'], $file['tmp_name']
     */

    //设置允许上传的最大文件大小(单位：KB)
    function setMaxSize($intMaxSize=200)
    {
        $this->intMaxSize=round($intMaxSize)*1024;
    }

    //设置调整图宽度
    function setResizeWidth($intResizeWidth=0)
    {
        $this->intResizeWidth=$intResizeWidth;
    }

    //设置调整图高度
    function setResizeHeight($intResizeHeight=0)
    {
        $this->intResizeHeight=$intResizeHeight;
    }

    //设置是否缩放原图多余部分
    function setResizeImage($needResizeImage=false)
    {
        $this->needResizeImage=$needResizeImage;
    }

    //以下为添加水印相关
    //是否添加水印
    function setWatermark($needWatermark=false)
    {
        $this->needWatermark=$needWatermark;
    }

    //添加水印类型(1为文字,2为图片)
    function setWatermarkType($intWatermarkType=1)
    {
        $this->intWatermarkType=$intWatermarkType;
    }


    //设置水印类型(1为文字,2为图片)
    function setWatermarkPosition($strWatermarkPosition="rb")
    {
        $this->strPosition=$strWatermarkPosition;
    }

    //设置水印字符串
    function setWatermarkString($strWatermarkString="HOUSE365.COM")
    {
        $this->strWatermarkString=$strWatermarkString;
    }

    //设置水印图片
    function setWatermarkImage($strWatermarkImage="")
    {
        if($strWatermarkImage=="") $strWatermarkImage=APP_PATH."static/images/logo.png";

        $this->strWatermarkImage=$strWatermarkImage;
    }

    //设置文字水印字体
    function setWatermarkFont($strWatermarkFont="")
    {
        if($strWatermarkFont=="") $strWatermarkFont=APP_PATH."static/font/ariblk.ttf";
        $this->strWatermarkFont=$strWatermarkFont;
    }

    //设置文字水印字体大小
    function setWatermarkSize($intWatermarkSize=24)
    {
        $this->intWatermarkSize=$intWatermarkSize;
    }

    public function get_file_name(){
            return $this->new_name;
    }

    /**
     * 设置上传后的文件名
     * 当前的毫秒数和原扩展名为新文件名
     */
    public function set_file_name(){
        if($this->rand_name==true){
            $a = explode(' ', microtime());
            $t = $a[1].($a[0]*1000000);
            $this->new_name = $t.'.'.($this->ext);
        }else{
            $this->new_name = $this->file_name;
        }
    }

    /**
     * 获取上传文件类型
     * @param string $filename 目标文件
     * @return string $ext 文件类型
     */
    public function get_file_type($filename){
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return $ext;
    }

    /**
     * 获取可上传文件的类型
     */
    public function get_allow_type($allow_type){
        $s = array();
        if(is_array($allow_type)){
            foreach($allow_type as $value){
                $s[] = $value;
            }
        }else{
            $s = preg_split("/[\s,|;]+/", $allow_type);
        }
        return $s;
    }

    //获取错误信息
    public function get_msg(){
        return $this->msg;
    }

    //调整图片的大小
    public function resize_image()
    {
        $objFile=$_FILES['file'];

        if(!@file_exists($objFile['tmp_name']))
        {
            return  "noneFileError";
        }
        list($intWidth,$intHeight) = getimagesize($objFile['tmp_name']);//获得上传图片的长宽

        $intResizeWidth=$this->intResizeWidth;
        $intResizeHeight=$this->intResizeHeight;

        if($intResizeWidth>0 && $intResizeHeight<=0)
            $intResizeHeight=$intHeight*($intResizeWidth/$intWidth);
        elseif($intResizeHeight>0 && $intResizeWidth<=0)
            $intResizeWidth=$intWidth*($intResizeHeight/$intHeight);
        elseif($intResizeWidth<=0 && $intResizeHeight<=0 && $this->intResizeImageSize>0)
        {
            if($intWidth > $intHeight)//规定产生的调整图大小
            {
                $intResizeWidth=$this->intResizeImageSize;
                $intResizeHeight=$intHeight*($intResizeWidth/$intWidth);
            }
            else
            {
                $intResizeHeight=$this->intResizeImageSize;
                $intResizeWidth=$intWidth*($intResizeHeight/$intHeight);
            }
        }
        elseif($intResizeWidth<=0)
        {
            $intResizeWidth=150;
            $intResizeHeight=$intHeight*($intResizeWidth/$intWidth);
        }
        elseif($this->needResizeCut)
        {
            if($intWidth/$intResizeWidth>$intHeight/$intResizeHeight)
                $intWidth=$intResizeWidth*$intHeight/$intResizeHeight;
            else
                $intHeight=$intResizeHeight*$intWidth/$intResizeWidth;
        }
        elseif($this->needResizeReality)
        {
            if($intWidth/$intResizeWidth>$intHeight/$intResizeHeight)
                $intResizeHeight=$intHeight*$intResizeWidth/$intWidth;
            else
                $intResizeWidth=$intWidth*$intResizeHeight/$intHeight;
        }

        //check the image size
        if($intWidth<=$intResizeWidth && $intHeight<=$intResizeHeight && $this->needForceResizeImage==false)
        {
            return "noNeedResize";
        }

        $image1 = imagecreatetruecolor($intResizeWidth,$intResizeHeight); //生成一张调整图
        imagealphablending($image1, false);//取消默认的混色模式
        imagesavealpha($image1,true);//设定保存完整的 alpha 通道信息
        $backgroundColor = imagecolorallocatealpha($image1,255,255,255,127);
        imageFilledRectangle($image1,0,0,$intResizeWidth-1,$intResizeHeight-1,$backgroundColor);

        $aryImageInfo=getimagesize($objFile['tmp_name'],$aryImageInfo);
        switch ($aryImageInfo[2])
        {
            case 1:
                $image2 = imagecreatefromgif($objFile['tmp_name']);//将上传图片赋值给image2
                break;
            case 2:
                $image2 = imagecreatefromjpeg($objFile['tmp_name']);
                break;
            case 3:
                $image2 = imagecreatefrompng($objFile['tmp_name']);
                break;
            case 6:
                $image2 = imagecreatefromwbmp($objFile['tmp_name']);
                break;
            default:
            {
                return  "imageTypeError";
            }
        }
        //判断是否图片复制成功
        if(!$image2)
        {
            return "imageTypeError";
        }

        imagecopyresampled($image1,$image2,0,0,0,0,$intResizeWidth,$intResizeHeight,$intWidth,$intHeight); //全图拷贝

        if(!@file_exists($this->save_path))
        {
            if(!@mkdir($this->save_path,0777))
                return "mkdirError";
        }

        $image_name= $this->get_file_name();
        $save_path =$this->save_path.'/thumb/';

        switch ($aryImageInfo[2])
        {
            case 1:
                //$isOK=@imagegif($image1,$this->strSaveDir.$strResizeDir.$this->strResizeImagePrefixion.$this->strSavaFileName);//保存调整图
                $isOK=@imagepng($image1,$save_path.$image_name);//保存调整图
                break;
            case 2:
                $isOK=imagejpeg($image1,$save_path.$image_name,100);//保存调整图
                break;
            case 3:
                $isOK=@imagepng($image1,$save_path.$image_name);//保存调整图
                break;
            case 6:
                $isOK=@imagewbmp($image1,$save_path.$image_name);//保存调整图
                break;
            default:
            {
                return  "imageTypeError";
            }
        }

        if($isOK)
            return "success";
        else
            return "error";
    }


    public function watermark()
    {
        $objFile=$_FILES['file'];

        $aryImageInfo=getimagesize($objFile['tmp_name'],$aryImageInfo);

        switch ($aryImageInfo[2])
        {
            case 1:
                $sourceImage = imagecreatefromgif($objFile['tmp_name']);
                break;
            case 2:
                $sourceImage = imagecreatefromjpeg($objFile['tmp_name']);
                break;
            case 3:
                $sourceImage = imagecreatefrompng($objFile['tmp_name']);
                break;
            case 6:
                $sourceImage = imagecreatefromwbmp($objFile['tmp_name']);
                break;
            default:
                return "imageTypeError";
                exit;
        }
        //判断是否图片复制成功
        if(!$sourceImage)
            return "imageTypeError";

        //设置水印位置
        if($this->intWatermarkType!=2)//文字水印
        {
            $ary = imagettfbbox(ceil($this->intWatermarkSize),0,$this->strWatermarkFont,$this->strWatermarkString);//取得使用 TrueType 字体的文本的范围
            $intWaterWidth = $ary[4] - $ary[6];
            $intWaterHeight = $ary[7] - $ary[1];
            unset($ary);
        }
        else//图片水印
        {
            $aryWaterImageInfo=getimagesize($this->strWatermarkImage,$aryWaterImageInfo);
            $intWaterWidth = $aryWaterImageInfo[0];
            $intWaterHeight = $aryWaterImageInfo[1];
        }
        //水印是否超过图片大小
        if( ($aryImageInfo[0]<$intWaterWidth) || ($aryImageInfo[1]<$intWaterHeight) )
        {
            return "水印超过图片大小";
        }

        switch($this->strPosition)
        {
            case 'sj':
                $posX = rand(0,($aryImageInfo[0] - $intWaterWidth));
                $posY = rand(50,($aryImageInfo[1] - $intWaterHeight));
                break;
            case 'lt':
                $posX = 0;
                $posY = 50;
                break;
            case 'rt':
                $posX = $aryImageInfo[0] - $intWaterWidth;
                $posY = 50;
                break;
            case 'lb':
                $posX = 0;
                $posY = $aryImageInfo[1] - $intWaterHeight;
                break;
            case 'ct':
                $posX = ($aryImageInfo[0] - $intWaterWidth) / 2;
                $posY = 50;
                break;
            case 'cb':
                $posX = ($aryImageInfo[0] - $intWaterWidth) / 2;
                $posY = $aryImageInfo[1] - $intWaterHeight;
                break;
            case 'lc':
                $posX = 0;
                $posY = ($aryImageInfo[1] - $intWaterHeight) / 2;
                break;
            case 'rc':
                $posX = $aryImageInfo[0] - $intWaterWidth;
                $posY = ($aryImageInfo[1] - $intWaterHeight) / 2;
                break;
            case 'cc':
                $posX = ($aryImageInfo[0] - $intWaterWidth) / 2;
                $posY = ($aryImageInfo[1] - $intWaterHeight) / 2;
                break;
            case 'rb':
                //$posX = $aryImageInfo [0] - $intWaterWidth - 20;
                //$posY = $aryImageInfo [1] - $intWaterHeight - 40;
                //break;
            default:
                $posX = $aryImageInfo[0] - $intWaterWidth-20;
                $posY = $aryImageInfo[1] - $intWaterHeight-10;
                break;
        }

        if($this->intWatermarkType!=2)//文字水印
        {
            $white=imagecolorallocatealpha($sourceImage,255,255,255,60);
            imagettftext($sourceImage,$this->intWatermarkSize,0,$posX,$posY,$white,$this->strWatermarkFont,$this->strWatermarkString);
        }
        else//加水印图片
        {
            switch ($aryWaterImageInfo[2])
            {
                case 1:
                    $waterImage = imagecreatefromgif($this->strWatermarkImage);
                    break;
                case 2:
                    $waterImage = imagecreatefromjpeg($this->strWatermarkImage);
                    break;
                case 3:
                    $waterImage = imagecreatefrompng($this->strWatermarkImage);
                    break;
                case 6:
                    $waterImage = imagecreatefromwbmp($this->strWatermarkImage);
                    break;
                default:
                    return "typeError";
                    exit;
            }
            //判断是否图片复制成功
            if(!$waterImage)
                return "imageTypeError";

            imagealphablending($sourceImage, true);
            imagecopy($sourceImage,$waterImage,$posX,$posY,0,0,$aryWaterImageInfo[0],$aryWaterImageInfo[1]);
            imagedestroy($waterImage);
        }

        switch ($aryImageInfo[2])
        {
            case 1:
                //imagegif($sourceImage, $objFile[tmp_name]);
                imagejpeg($sourceImage, $objFile['tmp_name']);
                break;
            case 2:
                imagejpeg($sourceImage, $objFile['tmp_name']);
                break;
            case 3:
                imagepng($sourceImage, $objFile['tmp_name']);
                break;
            case 6:
                imagewbmp($sourceImage, $objFile['tmp_name']);
                //imagejpeg($sourceImage, $objFile[tmp_name]);
                break;
        }

    }

    public function upload_file($file){
        //$this->file   = $file;
        $this->file_name   = $file['name'];
        $this->file_size   = $file['size'];
        $this->error       = $file['error'];
        $this->file_tmp_name = $file['tmp_name'];
        //print_r($file);die;
        $this->ext = $this->get_file_type($this->file_name);

        switch($this->error){
            case 0: $this->msg = ''; break;
            case 1: $this->msg = '超出了php.ini中文件大小'; break;
            case 2: $this->msg = '超出了MAX_FILE_SIZE的文件大小'; break;
            case 3: $this->msg = '文件被部分上传'; break;
            case 4: $this->msg = '没有文件上传'; break;
            case 5: $this->msg = '文件大小为0'; break;
            default: $this->msg = '上传失败'; break;
        }
        if($this->error==0 && is_uploaded_file($this->file_tmp_name)){
            //检测文件类型
            if(in_array($this->ext, $this->allow_type)==false){
                $this->msg = '文件类型不正确';
                return false;
            }
            //检测文件大小
            if($this->file_size > $this->intMaxSize){
                $this->msg = '文件过大';
                return false;
            }
        }
        $this->set_file_name();

        //是否要添加水印
        if($this->needWatermark)
        {
            $this->watermark();
        }
        //是否要压缩图片
        if($this->needResizeImage)
        {
            $this->resize_image();
        }
        $this->uploaded = $this->save_path.$this->new_name;

        if(move_uploaded_file($this->file_tmp_name, $this->uploaded)){
            $this->msg = '文件上传成功';
            return true;
        }else{
            $this->msg = '文件上传失败';
            return false;
        }
    }

}
?>