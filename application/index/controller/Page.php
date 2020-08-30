<?php


namespace app\index\controller;

use app\index\model\Fillin;
use app\index\model\Majordire;
use app\index\model\Student;
use think\Controller;
use think\Db;
use think\Request;
use app\index\controller\Base;

class Page extends Base
{
//    public function index()
//    {
//        return "";
//    }
//    首页 / 专业方向切换 切换至对应的所属专业群的方向介绍
    public function majordireIntroduce()
    {
        parent::__construct() ;

        $major = new Majordire() ;
        $major_all = $major->getMajordireAll(session('students.major'));
        $view_data = [
            'majordire' =>$major_all
        ];
//      交给页面进行模板渲染
		return view('majordireIntroduce' , $view_data );
    }
//    填报页设置安全审核 检查数据表中的state状态是否填报
    public function majordireFillin()
    {
        parent::__construct() ;
        $student = new Student() ;
        $fillin = new Fillin();
//        $result = ;
//      拒绝一切提交过填报表的访问Fillin页面，直接跳转值结果页
        if ($fillin->onlyQuery(session('students.student'))){
            $this->redirect('/index/page/majordireResult');
        }
        else if (!session('students.switch')){ // 查看0系统开关  /tpis这里也可以设置成从数据库中事实获取系统开关状态

//        else if ( !($student->where('student' , session('students.student'))->value('switch')) ){
            $this->redirect('/index/page/majordireNotOpen');
            // ps: 这里是使用了session作判断，如果在登录阶段开启则该用户依旧无法进入，需要重新登录
        }

		$major = new Majordire();
		$major_group = $major->getMajor(session('students.major'));
		// $major_name  = $major->getMajor()
		// 向下拉菜单中渲染专业种类
		return view('majordireFillin',['marjor_name_list' => $major_group]);
    }
//    结果页 查询数据库fillin向页面渲染结果
    public function majordireResult()
    {

        if (!session('students.switch')){ // 核验session中的系统开关状态
            $this->redirect('/index/page/majordireNotOpen');
        }

        $fillin = new Fillin();
        $fillinChecked = $fillin->getCheckedMajor(session('students.student'));
        if(empty($fillinChecked)){
            return view('majordireResult',['fillinChecked' => '抱歉!未查询到你选择的专业方向,请尝试重新登录!']);
        }
		return view('majordireResult',['fillinChecked' => $fillinChecked]);
    }
//    系统关闭提示页
    public function majordireNotOpen() {
        parent::__construct() ;

        return view('majordireNotOpen');
    }

//  查询当前子方向专业的剩余人数
    public function majorNumber()
    {
        $request = Request::instance();
        $majorNamescr = $request->param('majorname');
        if(!$request->isPost() || empty($majorNamescr)){
            return show( '0',"空数据,拒绝操作!");
        } else if( $majorNamescr == -1) {
            return show( '0' , "专业方向选择发生错误,请重新选择!" );
        } else if ( $majorNamescr < -1) { //防止学生提交负数给数据库
            return show( '0' , "小朋友,你很调皮!" );
        }
        /*
         * 设置一个安全机制,防止长期呆在这里干扰或对数据库进行大量的请求导致数据异常
         */

        requestCount(); //防止多次数据请求滥用

        $major = new Majordire();
        $majorNum = $major->getNum($majorNamescr);
        $fillin = new Fillin();
        $fillinNum = $fillin->getMajorConut($majorNamescr);
        $numNer = $majorNum - $fillinNum ;

        if ( $numNer > 0 ){
            return show( 1 , "获取数量" , $numNer );
        } else if ($numNer <= 0 ) {
            return show( 2 , "该方向人员已满,不支持选择" );
        } else {
            return show( 4 , "账户/系统发生意外错误,请尝试重新登录!") ;
        }
    }

//    提交填报表
    public function majorFillin(){
        $request = Request::instance();
        // 以防存在时差存储问题，在最终进行存储数据时进行一次余量检查
//        待进一步完善

        $majorNamescr = $request->param('majorname');

        if(!$request->isPost() || empty($majorNamescr)){
            return show(0 ,"空数据,拒绝操作!");
        } else if( $majorNamescr == -1) {
            return show( 0 , "专业方向选择发生错误,请重新选择!" );
        } else if ( $majorNamescr < -1) { //防止学生提交负数给数据库
            return show( 0 , "小朋友,你很调皮!" );
        }

        requestCount() ; // 防止多次数据请求滥用

        $major = new Majordire();
        $fillin = new Fillin();
		$student = new Student ();
        $majorNum = $major->getNum($majorNamescr);
        $fillinNum = $fillin->getMajorConut($majorNamescr);
        $numNer = $majorNum - $fillinNum ; //得出剩余的数量

        if( $numNer > 0 && session('students.state') == 0) {
//            if ( true ) {
                $data = [
                    'student' => session('students.student'),
                    'majorname' => $majorNamescr ,
                    'createtime' => date('Y-m-d H:i:s', time())
                ];
                $result = $fillin->insert($data);
                if (empty($result)){
                    return show(2, '提交失败请重新选择并提交' );
                } else {
                    $student->where('student', session('students.student'))->update(['state' => '1']);
                    return show(1, '提交成功');
                }
//            } else {
//                return show(2, '方向专业已经被锁定/人员已满,请重新选择!');
//            }
        } else {
            return show( 2 , '人数已满请重新选择');
        }
    }

}