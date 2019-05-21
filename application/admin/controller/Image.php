<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/21
 * Time: 下午3:16
 */

namespace  app\admin\controller;

class Image
{
    public function index()
    {
        //上传成功后，图片的访问路径
        //http://wudy.live.cn:8090/static/upload/20190521/0f270b5d5dea3ac32da1b9de00497236.png
//        print_r($_FILES);
//        print_r(request()->file('file'));
        $file = request()->file('file');
        $info = $file->move('/Users/yuliang/swooleTp/public/static/upload');
        print_r($info);
    }
}