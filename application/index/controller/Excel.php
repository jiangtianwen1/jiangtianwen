<?php
namespace app\index\Controller;
use think\Controller;
use think\Db;
use think\Model;
class Excel extends Controller {
 

  public function index()
  {
  	if(request()->isGet()){
  			return view();
  	}
  
  	$list = Db::table('admin')->select();  //var_dump($list);
        vendor("PHPExcel.Classes.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
       

        //设置行高度
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);

        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

        //set font size bold
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        //设置水平居中
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       

        //合并cell
        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

        // set table header content
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单数据汇总  时间:'.date('Y-m-d H:i:s'))
            ->setCellValue('A2', 'ID')
            ->setCellValue('B2', '用户名称')
            ->setCellValue('C2', '密码')
            ->setCellValue('D2', '库存');
            


        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<count($list)-1;$i++){
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($i+3), $list[$i]['id']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B'.($i+3), $list[$i]['name']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('C'.($i+3), $list[$i]['password']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D'.($i+3), $list[$i]['role_id']);
         
            //$objPHPExcel->getActiveSheet()->getStyle('A'.($i+3).':J'.($i+3))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //$objPHPExcel->getActiveSheet()->getStyle('A'.($i+3).':J'.($i+3))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(16);
        }


        //  sheet命名
        $objPHPExcel->getActiveSheet()->setTitle('订单汇总表');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // excel头参数
       ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="admin('.date('Ymd-His').').xls"');  //日期为文件名后缀
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //excel5为xls格式，excel2007为xlsx格式

        $objWriter->save('php://output');

  }

  //将excel数据插入数据库
    public function insertExcelArray($info){
         $exclePath = $info->getSaveName();  //获取文件名
         $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址
         //截取文件后缀 xlsx xls
         $extension = strtolower( pathinfo($file_name, PATHINFO_EXTENSION) );

         //区分上传文件格式
         if($extension == 'xlsx') {
             $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
             $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');
         }else if($extension == 'xls'){
             $objReader =\PHPExcel_IOFactory::createReader('Excel5');
             $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');
         }
         $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
         array_shift($excel_array);  //删除第一个数组(标题);

         $data = [];

         foreach($excel_array as $k=>$v) {
                $data[$k]['xxx']        = $v[0];
                $data[$k]['xxx']        = $v[1];
                $data[$k]['xxx']        = $v[4];
         }
        Db::name('tableName')->insertAll($data);
      
          return ['code'=>0,'msg'=>'添加成功'];
    }

// 导出功能

    public function excellist()
    {
     $expTitle = 'admin';
      
     $expCellName=array(
    array('id','供应商ID'),
    array('name','商品ID'),
    array('password','商品密码'),
    array('role_id','id'),
);
     $expTableData = 
     Db::table('admin') 
    ->select();
    $a = exportExcel($expTitle, $expCellName, $expTableData); // var_dump($a);die;
     
     return  view('excellist',$a);
    }

    public function import(){



    	
    }
    public function add()
    {

    $this->view->engine->layout(false); 
      $data =  \app\index\model\Category::where('id',1)->find();  

       $sql =  \app\index\model\Goods::where('Category_id',1)->select(); // var_dump($sql);die;
 


        return view('add',[
      'data' =>$data,   
     'sql' =>$sql
        ]);
    }

    public function ceshi()
    {
   $this->view->engine->layout(false); 
  //  $a= \app\index\model\Goods::all(['goods_name'=>'苹果','goods_name'=>'香蕉']);
  //  $a= \app\index\model\Goods::all(['category_id'=>'1','category_id'=>'2']);
  //  $a= collection($a->toArray());
   // $a= Db::table('goods')->where('goods_name','苹果')->whereor('goods_name','香蕉')->select();

       // var_dump($a);die;
         
       
      return view(); 
    }

  public function cesi(){

 $this->view->engine->layout(false); 

 $data = \app\index\model\Admin::select();  // var_dump($a);die;

 /*$res = [
           'code' => 0,
           'msg' => 'success',
           'data' => $data
       ];*/
          return json($data);

         
  //return view();
  }

public function upload()
{
     /* $files = request()->param('file');//var_dump($files);die;
    foreach($files as $file){
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
          
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            $a =$info->getSaveName();
              $res = [
           'code' => 0,
           'msg' => 'success',
           'data' => $a
       ];
            return json($res);

        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }


        $ret = array();  //返回的上传文件状态数组      
          if ($_FILES["file"]["error"] > 0){ 
        $ret["message"] =  $_FILES["file"]["error"] ;     
        $ret["status"] = 0;          
         $ret["src"] = "";            
        return json($ret);         
                     }else{               
                     $pic =  $this->upload();               
                     if($pic['info']== 1){ 
                              $url = '/uploads/'.$pic['savename'];               
                                     }  else {                   
                                     $ret["message"] = $this->error($pic['err']);                                                        $ret["status"] = 0;                 
                                   }                
                                   $ret["message"]= "图片上传成功！";                
                                   $ret["status"] = 1;                  
                                   $ret["src"] = $url;               
                                    return json($ret);       
                                     }     }     
                                     //图片上传代码     
                                     private  function upload(){        
                                     $file = request()->file('file');        
                                     // 移动到框架应用根目录/public/uploads/ 目录下        
                                     $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');        
                                     $reubfo = array();  //定义一个返回的数组        
                                     if($info){            
                                     $reubfo['info']= 1;            
                                     $reubfo['savename'] = $info->getSaveName();        
                                   }else{           
                                    // 上传失败获取错误信息            
                                    $reubfo['info']= 0;            
                                    $reubfo['err'] = $file->getError();;        
                                  }        
                                    return $reubfo; 

*/
 $files = request()->file('file'); // var_dump($files);die;
    
   
 // 移动到框架应用根目录/public/uploads/ 目录下
    if($files){
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
           
           
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getFilename(); 
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
 
    
}
}

 public function imgdemo(Request $request){
    //接收上传的文件
    $this->view->engine->layout(false); 
    $file = $this->request->file('file');
  
            if(!empty($file)){
                //图片存的路径
                $imgUrl= ROOT_PATH . 'public' . DS . 'uploads'. '/' .  date("Y/m/d");
                
                // 移动到框架应用根目录/public/uploads/ 目录下
                
                $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,gif'])->rule('uniqid')->move($imgUrl);
                $error = $file->getError();
                //验证文件后缀后大小
                if(!empty($error)){
                    dump($error);exit;
                }
                if($info){
                    // 成功上传后 获取上传信息
                //获取图片的名字
                $imgName = $info->getFilename();
                //获取图片的路径
                $photo=$imgUrl . "/" . $imgName;

                }else{
                    // 上传失败获取错误信息
                    $file->getError();
                }
            }else{
                $photo = '';
            }
    if($photo !== ''){
        return ['code'=>1,'msg'=>'成功','photo'=>$photo];
    }else{
        return ['code'=>404,'msg'=>'失败'];
    }

}

public function addBnaner(){
    if(input('isAjax') == 1){ //异步上传图片
        // 获取表单上传文件
        $file = request()->file('file'); //layui默认的文件name 即为 file
        if(empty($file)){
            return json(['info'=>'请选择上传文件！','status'=>0]);
        }
        // 移动到框架应用根目录/public/upload/ 目录下，并修改文件名为时间戳
        $info = $file->move(ROOT_PATH.'public'.DS.'upload'.DS.'image'.DS,time());
            //下面两行即为多图上传的文件名重定义
            //$filename = time().rand(10,100); //时间戳+随机数
            //$info = $file->move(ROOT_PATH.'public'.DS.'upload'.DS.'article'.DS,$filename);
        if($info){      
        return json(['info'=>$info->getSaveName(),'status'=>1]); //文件名称
        }else{
        return json(['info'=>'文件上传失败啦！','status'=>0]);
        }
    }
}    
public function doUpload(){

        $files = request()->file('image');

        $info="";
        foreach($files as $picFile){

            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $picFilevalidate(['size'=>156780,'ext'=>'jpg,png,gif'])->move(ROOT_PATH.'public' . DS . 'uploads');


            /*获取存储路径，以便插入数据库*/
            $path= "/public/uploads/".$info->getSaveName();

        }

 
    if($info!==""){
            return $this->success('上传成功！');
            // 成功上传后 获取上传信息
            // 输出 jpg
            /* echo $info->getExtension();*/
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            /*echo $info->getFilename();*/
        }else{
            // 上传失败获取错误信息
            /* echo $file->getError();*/


            return $this->error('上传失败！');
        }


}
}