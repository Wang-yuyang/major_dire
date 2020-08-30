<?php


namespace app\index\model;


use think\Model;

class Fillin extends Model
{
//  根据学号查询学生选择的子方向专业
    public function getCheckedMajor($data){
        $result = $this
            ->where('student',$data)
            ->value('majorname');
        return $result;
    }
//   查询一个子方向的选择数量
    public function getMajorConut($data){
        $result = $this
            ->where('majorname' , $data)
            ->count();
        return $result;
    }
//  防止重复提交 查询是否在表中存在数据
    public function onlyQuery( $data ) {
        $result = $this
            -> where( 'student' , $data)
            -> count();

        if ($result){ return true; } else { return false; }

        return $result ;
    }
}