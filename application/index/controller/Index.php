<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Validate;

class Index extends Controller
{

  public function index()
    {
       return view();
        //return $this->fetch();
    }

 public function select()
    {
        // error_reporting(0);
           // $this->view->engine->layout(false); 
           
      //    $data = \app\index\model\Admin::limit(4)->select(); //var_dump($data['name']);
         
         //  $id = $data['id'];
      /* // 
       if(request()->isGet()){
        $data = \app\index\model\Admin::limit(4)->select(); //var_dump($data['name']);
      return view('select',['d' => $data]);
       }*/
           
       $name = request()->param('name'); //var_dump($data['name']);die;
        $pass = request()->param('password');

      $sql = Db::table('admin');
        if(!empty($name)){
       $sql=  $sql->where('name',$name);
      }
     if(!empty($pass)){
       $sql= $sql->where('password',$pass);
      }
      // ->page(1,3);
        $sql=  $sql->paginate(3);

      /*  $sql = Db::table('admin')       
     ->where('name',$name)
     //->where('password',$pass)      
    ->select();*/

       /* $sql = Db::table('admin')
        ->where('password',$pass)
        ->select();*/
   /*   if(!empty($id)){
        $sql->where('id',$id);
      }
      if(!empty($name)){
       $sql-> where('name',$name);
      }
    */
   
 
        return view('select',['d' => $sql]);
    }



  
    public function add()
    {
        return $this->fetch();
    }
   
  
  public function save()
  {
    

  $data =request()->param(); // var_dump($data);die;
     $rule = [
    'name'  => 'require|max:25',
    'password'   => 'require',
    'role_id' => 'require',
];
 $msg = [
    'name.require' => '名称必填',
    'name.max'     => '名称最多不能超过25个字符',
    'password.require'   => '密码不能为空',
    'role_id.require'  => '职位必选',

 ];
 $validate = new Validate($rule,$msg);

   if(!$validate->check($data)){
       $error = $Validate->getError();
       $this->error($error);
   }

  $password = encrypt_password($data['password']);

  $array = [
     'name' =>$data['name'],
     'password'=>$password,
     'role_id'=>$data['role_id']
  ];
$sql =  Db::table('admin')->insert($array);



  
 
 
        $this->success('添加成功','index/index/select');
    
      

  }

  public function edit($id)
  {
      //$data  =   Db::table('admin')->where(id,$id)->select();
           $data = \app\index\model\Admin::find($id);
  return view('index/edit',['d'=>$data]);
  }

  public function delete($id)
  {
      //$data  =   Db::table('admin')->where(id,$id)->select();
           $data = \app\index\model\Admin::destroy($id);
  return  $this->redirect('index/select');  
  }

   public function insert()
   {
    $data =   request()->param();


   }

   public function uploa(){
    // 获取表单上传文件 例如上传了001.jpg
    $file = request()->file('image');
    
    // 移动到框架应用根目录/public/uploads/ 目录下
    if($file){
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
        
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getSaveName();

        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }
}

}