<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;
class Goods extends Model
{
	// use SoftDelete;
    //protected  $deleteTime = 'delete_time';
   
  /*   protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
        //
      
    }*/
    public function getStatusTextAttr($value)
    {
        $status = [1=>'水果',2=>'蔬菜',3=>'海鲜',4=>'人肉'];
        return $status[$value];
    }
   
}