<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/8
 * Time: 2:06
 */

namespace app\admin\controller;


use app\admin\model\Users;
use think\Collection;
use think\Request;

class Login extends Collection
{
    public function index()
    {
        return view() ;
    }
    public function check()
    {
        $request = Request::instance() ;
//        if (!$request->isPost()) {
//            return show('0' , '拒绝操作!') ;
//        }
        $username=$request->param("username","","trim");
        $password=$request->param("password","","trim");
        $captcha=$request->param("captcha","","trim");
        if(empty($username) || empty($password)||empty($captcha)){
            return show(config("stataus.error"),"参数不能为空.");
        }
        if(!captcha_check($captcha)){
            return show(config("stataus.error"),"验证码不正确.");
        }
        try{
            // 模型 学生表
            $user=new Users();
            $Users=$user->verifyUsername($username);
            //判断当前的用户是否存在
            if(empty($Users)){ // 模型处理后返回查询结果
                return show('0',"角色不存在.");
            }
            $Users=$Users->toArray(); // 数组
            //判断密码是否正确
//            echo md5Password($password) ;
            if ( md5Password($password) != strtolower($Users["password"])) {
                return show(config("stataus.error"),"密码不正确.");
            }
        } catch(\Exception $e){
            return show(config("stataus.error"),"内部异常,登录失败.");
        }

        $updateData=[
            "loginip"       =>  $_SERVER['REMOTE_ADDR'],
            "logintime"     =>  date('Y-m-d H:i:s',time()),
            'token'         =>  tokenKey( 8 )
        ];
        session('login_time' , $updateData['logintime'] ) ;
//     	存放ip和登录时间
        $res=$user->updataUser($username , $updateData);
        session('userInfo',$Users);
        if( !empty($res) ){
            return show("1",config("stataus.success"),"登录成功<br>请稍等,正在进入系统...");
        }
        return  show(config("stataus.error"),"登录失败.");

    }
}