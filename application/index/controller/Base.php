<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use  think\Request;
class Base extends Controller
{
 public function  __construct(Request $request)
 {
   parent::__construct($request);

   if(!session('admin_info')){
   	
   	$this->redirect('index/login/index');
   }

 }

}