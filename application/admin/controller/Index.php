<?php


namespace app\admin\controller;


use think\Controller;

class Index extends Base
{
    public function index()
    {
        // 验证专业大方向类型
        $majordire = 'common.json';
        if (session('userInfo.roles') == '0') {
            $majordire = 'admin.json' ;
        }
        // 这里注意 向模板的js直接传变量时不可行的
        return view('index',[
            'majordire' => $majordire,
        ]);


        return view('index');
    }

    // 用户退出 清空session
    public function signOut()
    {
        session(null);// 清理session
        $this->redirect('/admin/login/index');
        return "注销会话" ;
    }

    public function ex(){
        return view('ex') ;
    }
}