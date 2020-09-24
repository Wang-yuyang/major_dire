<?php
namespace app\admin\controller;
use app\admin\model\Student;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;
class Exportimport extends Base
{
    public function index()
    {
    	return " ";
     }
//  学生表数据 导入数据
    public function studentImportExcel()
    {
        if(request() -> isPost())
        {
			// echo "123";die;
            vendor("PHPExcel.PHPExcel"); //方法一
            $objPHPExcel =new \PHPExcel();
            //获取表单上传文件
            $file =request()->file('file');
            $info = $file->validate(['ext' => 'xls'])->move(ROOT_PATH. 'public');
            //上传验证后缀名,以及上传之后移动的地址  E:\wamp\www\bick\public
//            echo $info ; die();
            if($info)
            {
              //echo $info->getFilename();
                $exclePath = $info->getSaveName();  //获取文件名
//                $file_name = ROOT_PATH . 'public' . DS . $exclePath;//上传文件的地址
                $file_name = ROOT_PATH . 'public' . DS . $exclePath ;
//                echo $file_name ; die;
                $objReader =\PHPExcel_IOFactory::createReader("Excel5");
                $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array=$obj_PHPExcel->getSheet(0)->toArray();   //转换为数组格式
                array_shift($excel_array);  //删除第一个数组(标题);
                $city = [];
                $i=0;
                foreach($excel_array as $k=>$v) {
                    $city[$k]['student'] = $v[0];//学号
				   $city[$k]['stuname'] = $v[1];//姓名
				   $city[$k]['gender'] = $v[2];//性别
				   $city[$k]['majorname'] = $v[3];//专业名称
				   $city[$k]['stuclass'] = $v[4];//行政班级
				   $city[$k]['year'] = $v[5];//入学年份
				   $city[$k]['category'] = $v[6];//学生类别
				   $city[$k]['aftersix'] = $v[7];//密码
				   $city[$k]['groupid'] = $v[8];//专业群
				   $city[$k]['major'] = $v[8];
				   $city[$k]['state'] = 0;//状态
                    $i++;
                }
//				 echo "<pre>";
//              \var_dump($city);die;
//			  echo "</pre>";
                Db::name("student")->insertAll($city);
            }else
            {
                echo $file->getError();
            }
        }
          return json(['code' => 0,'msg' => '上传成功']);
                        
        // return $this->fetch("user-excel");
    }
//  导出学生excel  ==> 学号 | 姓名 | 性别 | 班级 | 已选专业方向 | 填报时间
    public function studentExportExcel(){
        $stu = new Student() ;
        $users = $stu->studentExcel()->select();     //数据库查询
        $path = dirname(__FILE__); //找到当前脚本所在路径
 
        vendor("PHPExcel.PHPExcel"); //方法一
 
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称
//          学号 | 姓名 | 性别 | 班级 | 已选专业方向 |
        $PHPSheet->setCellValue("A1", "序列")
                ->setCellValue("B1", "学号")
                ->setCellValue("C1", "姓名")
                ->setCellValue("D1", "性别")
                ->setCellValue("E1", "班级")
                ->setCellValue("F1", "已选专业方向")
                ->setCellValue("G1", "专业填报时间");
        $i = 2;
        foreach($users as $data){
            $PHPSheet->setCellValue("A" . $i, $i - 1 )
                ->setCellValue("B" . $i, $data['student'])
                ->setCellValue("C" . $i, $data['stuname'])
                ->setCellValue("D" . $i, $data['gender'])
                ->setCellValue("E" . $i, $data['stuclass'])
                ->setCellValue("F" . $i, $data['majorname'])
                ->setCellValue("G" . $i, $data['createtime']);
            $i++;
        }
		ob_end_clean();
		\ob_start();
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007");
        header('Content-Disposition: attachment;filename="学生专业方向填报信息表.xlsx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Cache-Control: max-age=0');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
		exit;
    }
//  导入专业方向信息
    public function majorImportExcel()
    {
        if(request() -> isPost())
        {
            vendor("PHPExcel.PHPExcel"); //方法一
            $objPHPExcel =new \PHPExcel();
            //获取表单上传文件
            $file =request()->file('file');
            $info = $file->validate(['ext' => 'xls'])->move(ROOT_PATH. 'public');
            //上传验证后缀名,以及上传之后移动的地址  E:\wamp\www\bick\public
//            echo $info ; die();
            if($info)
            {
                //echo $info->getFilename();
                $exclePath = $info->getSaveName();  //获取文件名
//                $file_name = ROOT_PATH . 'public' . DS . $exclePath;//上传文件的地址
                $file_name = ROOT_PATH . 'public' . DS . $exclePath ;
//                echo $file_name ; die;
                $objReader =\PHPExcel_IOFactory::createReader("Excel5");
                $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array=$obj_PHPExcel->getSheet(0)->toArray();   //转换为数组格式
                array_shift($excel_array);  //删除第一个数组(标题);
                $city = [];
                $i=0;
                foreach($excel_array as $k=>$v) {
                    $city[$k]['pid'] = $v[0];//专业父类ID = 通信类/计算机类
                    $city[$k]['majorname'] = $v[1];//专业名称
                    $city[$k]['majorintroduce'] = $v[2];//专业介绍
                    $city[$k]['majornumber'] = $v[3];//专业人数

                    $i++;
                }
//				 echo "<pre>";
//              \var_dump($city);die;
//			  echo "</pre>";
                Db::name("majordire")->insertAll($city);
            }else
            {
                echo $file->getError();
            }
        }
        return json(['code' => 0,'msg' => '上传成功']);

        // return $this->fetch("user-excel");
    }
}
