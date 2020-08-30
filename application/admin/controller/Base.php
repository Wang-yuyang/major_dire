<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/8
 * Time: 2:05
 */

namespace app\admin\controller;


use app\admin\model\Users;
use think\Collection;
use think\Controller;

class Base extends Controller
{

    public function __construct()
    {
//        $session_key = time() - strtotime(session('login_time'));

        $redirect_url='/admin/login';

        $User = new Users() ;
        // session会话事件
        if (session('userInfo.username') != null ) {
            $result = $User -> catToken(session('userInfo.username'));
            if (session('TOKEN_login') == $result && session('TOKEN_login') != null){
                return true;
            } else {
                //session(null);// 清理session
                $this->redirect($redirect_url);
            }
        } else {
            session(null);// 清理session
            $this->redirect($redirect_url);
        }
    }
}