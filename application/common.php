<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use app\index\model\Student;

use app\admin\model\Users ;

function show($status, $message="error", $data=[], $httpStatus=200)
{
	$result=["status"=>$status,"message"=>$message,"result"=>$data];
	return json($result,$httpStatus);
}

function tokenKey($length)
{
    /**
     * Token的使用意义，可以防止越权访问、session篡改等现象
     * 也是确认session合法的重要一个参数方法
     * md5加密time()当前时间戳，由于存在一定的时间差关系，所以一般情况不会token不会被破解
     */
    $str = substr(md5(time()), 0, $length);
    session('TOKEN_login' , $str);
    return $str;
}

function judgeSwitch()
{
    $StuInfo = new Student() ;
    $result = $StuInfo->catSwitch( session('students.student') );
    if ($result == '0') {
        return 1 ;
    } else if ($result == '1') {
        return 0;
    }
}
// 检测访问数据库的次数 防止.......
function requestCount()
{
    if (session('request_count')>='10'){
        session(null);// 清理session
        $this->redirect("index/login");
    }

    $count_req = session('request_count') ;
    session('request_count' , $count_req + 1) ;
}
//核验登录角色的身份
function toIdentify()
{
    $student = new Student();
    $result = $student->catToken(session('students.student'));
    if (session('TOKEN_login') == $result) {
        return true;
    } else {
        session(null);// 清理session
    }
}

function md5Password( $password )
{
    $pass = $password . "admin@hcit" ;
    $pass = md5($pass) ;

    return $pass ;
}

function returnAdminUser( $username )
{
    $user = new Users() ;
    $result = $user
        ->where('username' , $username)
        ->value('roles') ;

    return $result ;
}









