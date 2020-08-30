<?php


namespace app\admin\model;


use think\Db;
use think\Model;

class Student extends Model
{

//    通过学号判断是否存在该学生
    public function studentQuery ($student){
        $result = $this->where('student' , $student)->count() ;
        return $result ;
    }

    public function studentViewAll ()
    {
        if (session('userInfo.roles') == '0') {
            $result = Db::name('student')
                ->alias('stu')
                ->join('fillin fill', 'fill.student = stu.student', 'LEFT')
                ->field('stu.student , stu.stuclass , stu.gender , stu.stuname , stu.aftersix , stu.major , stu.state , stu.visittime , fill.majorname')
                ;
        } else {
            $result = Db::name('student')
                ->alias('stu')
                ->join('fillin fill', 'fill.student = stu.student', 'LEFT')
                ->field('stu.student , stu.stuclass , stu.gender , stu.stuname , stu.aftersix , stu.major , stu.state , stu.visittime , fill.majorname')
                ->where('stu.major', session('userInfo.roles')) ;
        }

        return $result ; // 返回的是一个模型对象，便于之后的操作例如排序

    }

    //  excel导出 ==> 学号 | 姓名 | 性别 | 班级 | 已选专业方向 | 填报时间
    public function studentExcel ()
    {
        if (session('userInfo.roles') == '0') {
            $result = Db::name('student')
                ->alias('stu')
                ->join('fillin fill', 'fill.student = stu.student', 'LEFT')
                ->field('stu.student , stu.stuname , stu.gender  , stu.stuclass  , fill.majorname , fill.createtime')
            ;
        } else {
            $result = Db::name('student')
                ->alias('stu')
                ->join('fillin fill', 'fill.student = stu.student', 'LEFT')
                ->field('stu.student , stu.stuname , stu.gender  , stu.stuclass  , fill.majorname , fill.createtime')
                ->where('stu.major', session('userInfo.roles')) ;
        }

        return $result ; // 返回的是一个模型对象，便于之后的操作例如排序

    }


//  系统开关权限更新
    public function switchFieldUpdate( $key )
    {
        if(true){
            if ( $key > 0 ){
                $data = ['switch' => '0'] ;
            } else {
                $data = ['switch' => '1'] ;
            }
            $result = $this->where('switch' , $key)->update($data);
            return $result ;
        }
    }
//  查看系统开关权限值状态
    public function switchState()
    {
        $result = $this->find()->value('switch') ;
        return $result ;
    }
//  重置填报状态
    public function stateReset( $data )

    {
        $result = $this->where( 'student' , $data)->update(['state'=>'0']);
        return $result ;
    }
//  查看已选/未选的人数
    public function countState( $data )
    {
        $result = $this->where( 'state' , $data)->count('state');
        return $result ;
    }

    public function deleteStu( $data )
    {
        $result = $this->where('student' , $data)->delete();
        return $result ;
    }
}