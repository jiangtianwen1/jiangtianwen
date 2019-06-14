<?php
namespace app\index\Controller;
use think\Controller;
use think\Db;
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
        return view();
    }


}