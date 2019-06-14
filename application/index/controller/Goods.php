<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Validate;

class Goods extends Controller
{

	public function index()
	{
    //	$this->view->engine->layout(false); 
	return view('index');
	}
public function upload($img){
    // 获取表单上传文件
    $files = request()->file('file');//var_dump($files);die;
    foreach($files as $file){
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
          
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            return $info->getSaveName(); 

        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }    
    }

}

public function save()
{
  $data = request()->param(); 
  $img = request()->file('goods_picture');// var_dump($img);die;
  $info = $img->move(ROOT_PATH . 'public' . DS . 'uploads');
    if($info){
          
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
             $img= $info->getSaveName(); 

        }else{
            // 上传失败获取错误信息
         //   echo $file->getError();
        }  

    $arr = [
      'goods_name'=> $data['goods_name'],
      'goods_picture'=> $img,
      'category_id'=>$data['category_id'],
      'goods_desc'=>$data['goods_desc']

    ];
   $sql= Db::table('goods')->insert($arr);

    $this->success('添加成功','index/index/select');
    
}

public function lis()
{

  $sql =  Db::table('goods')->paginate(5);

  return view('list',['data'=>$sql]);

}
 public function edit($id)
  {
      //$data  =   Db::table('admin')->where(id,$id)->select();
           $data = \app\index\model\Goods::find($id);
  return view('index/edit',['d'=>$data]);
  }


}
