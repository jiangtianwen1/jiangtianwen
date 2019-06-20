<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Validate;

class Category extends Controller
{
 

  public function index()
  {
  	$this->view->engine->layout(false); 

  	  return  view();


  }

  public function add()
  {


   	$this->view->engine->layout(false); 
 	  
 	  $data = request()->param();
  
     echo  $data['name'];
     echo "\n";
     echo   $data['url'];
       
 // return view();

  }
 public function getCateByPid($id)
   {
       //接收id参数
       //$id = request()->param('id');
       //检测id参数
       if(!preg_match('/^\d+$/', $id)){
           //id参数格式错误
           $res = [
               'code' => 10001,
               'msg' => '参数错误'
           ];
           return json($res);
       }
       //根据分类id查询 其 子分类
       $data = \app\admin\model\Category::where('pid', $id)->select();
       //返回数据
       $res = [
           'code' => 10000,
           'msg' => 'success',
           'data' => $data
       ];
       return json($res);
   }


}