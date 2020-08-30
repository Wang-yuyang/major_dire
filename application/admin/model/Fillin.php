<?php


namespace app\admin\model;


use think\Model;

class Fillin extends Model
{


    public function getStudentFillin( $data )
    {

    }

    // 根据学号删除填报的单数据
    public function removeSingleFillin( $data )
    {
        $result = $this->where('student' , $data)->delete();
//        删除成功返回收到影响的行数，删除失败则返回 0
        return $result ;
    }
}