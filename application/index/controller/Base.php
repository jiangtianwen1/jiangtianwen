<?php
namespace app\ndex\controller;
use think\Controller;
use think\Db;
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