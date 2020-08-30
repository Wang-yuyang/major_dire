<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\facade\View;
use app\index\controller\Base;

use app\index\controller\Login;
class Index extends Base
{
    public function index()
    {      
        // 验证专业大方向类型  -- 暂时被写死了
        $majordire = 'computer.json';
        switch (session('students.major'))
        {
            case 1 : $majordire = 'communication.json';break;
            case 2 : $majordire = 'computer.json';break;
        }
        // 这里注意 向模板的js直接传变量时不可行的
        return view('index',[
            'majordire' => $majordire,
        ]);
    }
    // 用户退出 清空session
    public function signOut()
    {
        session(null);// 清理session
        $this->redirect('/index/login/index');
        return "注销会话" ;
    }

}
