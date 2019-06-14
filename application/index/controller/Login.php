<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use  think\Verify;
use think\Validate;
class Login extends Controller
{
    
    public function index()
    {
  
    	if(request()->isGet()){
    		$this->view->engine->layout(false); 
        return $this->fetch();
    	}

    	$data =request()->param(); // var_dump($data);die;
     $rule = [
    'name'  => 'require|max:25',
    'password'   => 'require',
    'code' => 'require',
];
 $msg = [
    'name.require' => '名称必须',
    'name.max'     => '名称最多不能超过25个字符',
    'password.require'   => '密码不能为空',
    'code.require'  => '验证码',

 ];
 $validate = new Validate($rule,$msg);

   if(!$validate->check($data)){
       $error = $validate->getError();
       $this->error($error);
   }
    if(!captcha_check($data['code'])){
 //验证失败
     $this->error('验证码错误');
};	
  $password = encrypt_password($data['password']);

  $array = [
     'name' =>$data['name'],
     'password'=>$password
  ];
$sql =  Db::table('admin')->where($array)->find();// var_dump($sql);die;

  if(empty($sql)){
    	$this->error('用户名或密码错误');
  
    }else{
    Session('admin_info',$sql); 
    session('admin_info.name');
 
 // var_dump($a);die;
        $this ->success('登录成功','index/index/index');
    }

  
} 
 public  function tuichu()
 {

  // $a =  encrypt_password('520520');  var_dump($a);die;
   session(null);
   $this->redirect('index/login/index');
 }


}