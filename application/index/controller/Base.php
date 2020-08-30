<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Student;

class Base extends Controller
{

    public function __construct()
    {
//       每一个继承这个页面的都会被验证session和token的正确有效性
        /**
         * session 核心的token验证
         * 1 学号:student
         * 2 Token:token_login
         *
         * 3 这里不单单只验证学号，同时也验证token,最大程度的防止……
         * 4 验证session的request_count , 防止登陆后发出大量的数据请求
         *
         * 其他控制器若是想调用这个来验证用户,可以使用类似如下语法:
         *      pareent::__construct() [推荐]
         *      $this -> __construct()
         */
        // session 有效期 利用时间差的长短来定义session的过期时间

        $session_key = time() - strtotime(session('login_time'));
//        $session_key = '12';

        $redirect_url='/index/login';

        $StuInfo = new Student() ;
                                // session会话事件
        if (session('students.student') != null
            && $session_key <= '600'
            && session('request_count' ) <= '50'
            ) {
            // 优先判断session中的学号信息是否存在,然后在判断token是否匹配
            $result = $StuInfo -> catToken(session('students.student'));
            if (session('TOKEN_login') == $result && session('TOKEN_login') != null){
                return true;
            } else {
                session(null);// 清理session
                $this->redirect($redirect_url);
            }
        } else {
            session(null);// 清理session
            $this->redirect($redirect_url);
        }
    }
}