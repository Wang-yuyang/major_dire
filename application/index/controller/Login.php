<?php
namespace app\index\controller;
use think\request;
use think\Controller;
use think\Db;
use think\facade\View;
use app\index\model\Student;
use think\Session;

class Login extends Controller
{
	public function index(){
         return view();
	}
     public function check()
     {
		$request=Request::instance();
     	if(!$request->isPost()){
     		return show(config("stataus.error"),"请求方式错误");
     	}
     	//参数校验 从post请求中取值
     	$username=$request->param("username","","trim");
     	$password=$request->param("password","","trim");
     	$captcha=$request->param("captcha","","trim");
     	// 校验参数是否为空
     	if(empty($username) || empty($password)||empty($captcha)){
     		return show(config("stataus.error"),"参数不能为空.");
     	}
     	// 检测验证码是否正确
     	if(!captcha_check($captcha)){
			return show(config("stataus.error"),"验证码不正确.");
     	}
     	try{
     	    // 模型 学生表
            $StuInfo=new Student();
            $stuUser=$StuInfo->getAdminUserByUsername($username);
            //判断当前的用户是否存在
            if(empty($stuUser)){ // 模型处理后返回查询结果
                return show(config("stataus.error"),"学号不存在.");
            }
            $stuUser=$stuUser->toArray(); // 数组
            //判断密码是否正确
            if(strtolower($stuUser["aftersix"])!=strtolower($password)){
                return show(config("stataus.error"),"密码不正确.");
            }
     	} catch(\Exception $e){
     		return show(config("stataus.error"),"内部异常,登录失败.");
     	}
     	// 模型端返回的 $stuUser 包含该学生的学生表所有字段
     	$updateData=[
     	    "last_login_ip"=>$StuInfo->get_client_ip(),
            "visittime"=>date('Y-m-d H:i:s',time()),
            'token' => tokenKey( 8 )
        ];
     	session('login_time' , $updateData['visittime'] ) ;
//     	存放ip和登录时间
     	$res=$StuInfo->updateById($stuUser["student"],$updateData);
     	session('students',$stuUser);  //保存session记录数据
         // 设置一个防登陆后破坏性的数据请求导致安全问题
        session('request_count' , '0') ;

     	if( !empty($res) ){
//     	    $this->redirect('/index/index/index') ;
            return show("1",config("stataus.success"),"登录成功<br>请稍等,正在进入系统...");
     	}
         return  show(config("stataus.error"),"登录失败.");

     
     }
   
}
