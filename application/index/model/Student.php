<?php
namespace app\index\model;
use think\Db;
use think\Model;
class Student extends Model
{
//  查询学生表的第一行数据
    public function getDb()
    {
        $result = $this->find();
        return $result;
    }
// 获取当前学生的表内信息 session存储在用户手中
    public function getAdminUserByUsername($username)
    {
        if(empty($username))
        {
            return false;
        }
        // 根据学号判断是否有该学生
        $where=["student"=>trim($username)];
        $result = $this->where($where)->find();
		
        return $result;
    }
    // 以学号为条件，$data为数据进行更新
    public function updateById($student,$data)
    {
    	$student=intval($student);
        if (empty($student) || empty($data) || !is_array($data)) {
            return false;
        }
        $where = [
            'student' => $student
        ];
        return $this->save($data,$where);

    }
    public function get_client_ip()
	{
     $ip = $_SERVER['REMOTE_ADDR'];
     // echo $ip;die;
   	 return $ip;
	}
//  查看学生的填报状态
	public function catState( $data ) {
        $result = $this
            ->where('student' , $data)
            ->value('state');

        return $result ;
    }
//  查看数据表中的token值
    public function catToken( $data ) {
        $result = $this
            -> where('student' , $data)
            -> value('token');

        return $result  ;
    }
//  查看系统开关状态
    public function catSwitch( $data ){
        $result = $this
            ->where('student' , $data )
            ->value('switch');

        return $result ;
    }
//  查看登录时间
    public function catVisittime(){
        $result = $this
            ->where('student' , session('students.student'))
            ->value('visittime');

        return $result ;
    }


}
?>