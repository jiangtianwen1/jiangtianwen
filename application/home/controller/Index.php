<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Validate;

class Index extends Controller
{

  public function index()
    {
     //  return view('home/index/index');
        return $this->fetch();
    }

}