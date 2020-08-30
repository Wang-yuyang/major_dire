<?php


namespace app\admin\controller;

use app\admin\model\Fillin;
use app\admin\model\Majordire;
use app\admin\model\Student;
use app\admin\model\Users;
use think\Controller;
use think\Request;
// 调试过程中建议暂时暂时关闭登陆检查
class Page extends Base
{
    public function index()
    {
        return view('index');
    }
    public function studentAdd()
    {
        // 学生添加
        return view('studentadd');
    }
    public function adminUserAdd()
    {
        //管理员的添加
        return view('adminuseradd');
    }
    public function delStudent()
    {
        if ( returnAdminUser(session('userInfo.username')) == '0' ) {
            // 删除用户
            return view('delStudent');
        }
    }
    public function delUsers()
    {
        if ( returnAdminUser(session('userInfo.username')) == '0' ) {
            // 删除权限角色
            return view('delUsers');
        }
    }
//    修改角色信息的单独页面，需要使用渲染技术把用户名渲染给页面 (作废)
    public function reviseUserView() {
        $request = Request::instance() ;
        $id = $request->param('id');

        $user = new Users() ;
        $result = $user->where('id' , $id)->find() ;
        $this -> assign('adminUsers' , $result) ;
        return view(' reviseUserView ' , ['adminUsers' => $result]) ;
    }
    public function majordireInformationImport() // 专业方向信息导入
    {

        return view('majordireInformationImport');
    }
    public function adddirection()
    {
        return view('addDirection');
    }
    public function deldirection()
    {
        return view('delDirection') ;
    }
    public function backgroundHomeChart() // 后台图表页面
    {
        return view('backgroundHomeChart');
    }
    public function studentInformationView() // 学生信息显示
    {
        return view('studentInformationView' );
    }
    public function systemSwitch() //系统平台开放功能
    {
        return view('systemSwitch');
    }
    public function adminInformationView() //显示权限角色信息
    {
        return view('adminInformationView');
    }
//  系统环境填报的启动/关闭
    public function systemSwitchSetup()
    {
        $requests = Request::instance();
        $result = $requests->param('statue');
        if (!empty($result) || $result == 0){
            $StuInfo = new Student() ;
            $data = $StuInfo->switchFieldUpdate($result);
            if (!empty($data)){
                return show( '1' , 'Good!');
            }
        }
    }
//  系统环境填报的查询
    public function systemSwitchState()
    {
        $requests = Request::instance();
        $StuInfo = new Student() ;
        $result = $StuInfo->switchState();
        if (!empty($result) || $result == 1){
            return show( '1' , '系统平台已经开放');
        } else if ((!empty($result) || $result == 0)) {
            return show( '0' , '暂未开放');
        }
        return false ;
    }
//  重置学生状态
    public function strdentStateReset()
    {
        /*
         * 学生状态需要将student表中的state字段重置为0；同时删除填报表中该学生的信息
         */
        $student = new Student() ;
        $fillin  = new  Fillin() ;

        $request = Request::instance() ;
        $result = $request->param('student') ;
        if (!empty($result)){
            if ( $student->stateReset($result) && $fillin->removeSingleFillin($result) ){
                 return show( '1' , '重置成功');
            } else {
                return show( '2' , '找不到该学生的填报信息') ;
            }
        }
    }
//  重置全部学生状态
    public function strdentStateResetAll()
    {
        $student = new Student() ;
        $fillin  = new  Fillin() ;

//        $request = Request::instance() ;
//        $result = $request->param('student') ;
        $stuUp = $student->where('state' , 1)->update(['state'=>'0']) ;
        $fillUp = $fillin->where( 'student' , 'like' , '%')->delete(true) ;
//        $fillUp = $fillin->where()->delete() ;
            if (  $stuUp && $fillUp){
                return show( '1' , '重置成功');
            } else {
                return show( '2' , 'error') ;
            }

    }

//  查询所有学生的填报、状态等信息 (join联表查询 学生表和填报表)
    public function studentAll () {
        $student = new Student() ;
        $page=input("get.page")?input("get.page"):1;
        $page=intval($page);
        $limit=input("get.limit")?input("get.limit"):1;
        $limit=intval($limit);
        $start=$limit*($page-1);
        $cate_list= $student->studentViewAll()->limit($start,$limit)->select();

        $list["msg"]="";
        $list["code"]=0;
        $list["count"] = count($student->studentViewAll()->select());
        $list["data"]=$cate_list;
        if(empty($cate_list)){
            $list["msg"]="暂无数据";
        }
        return json($list);
    }
//  查看所有角色信息
    public function usersAll() {
        $users = new Users() ;
        $page = input("get.page")?input("get.page"):1 ;
        $page = intval($page) ;
        $limit = input("get.limit")?input("get.limit"): 1 ;
        $limit = intval($limit);
        $start = $limit*($page-1) ;
        $cate_list = $users->limit($start , $limit )->select() ;

        $list["msg"] = "" ;
        $list["code"] = 0 ;
        $list["count"] = count($users->select()) ;
        $list["data"] = $cate_list ;
        if (empty($cate_list)){
            $list["msg"] = "暂无数据" ;
        }
        return json($list);
    }
//  查看所有专业方向
    public function majorAll() {
        $major = new Majordire() ;
        $page = input("get.page")?input("get.page"):1 ;
        $page = intval($page) ;
        $limit = input("get.limit")?input("get.limit"): 1 ;
        $limit = intval($limit);
        $start = $limit*($page-1) ;
        $cate_list = $major->limit($start , $limit )->select() ;

        $list["msg"] = "" ;
        $list["code"] = 0 ;
        $list["count"] = count($major->select()) ;
        $list["data"] = $cate_list ;
        if (empty($cate_list)){
            $list["msg"] = "暂无数据" ;
        }
        return json($list);
    }
//  数据图 1 全部的人 已选/未选
    public function count()
    {
        $student = new Student() ;
        $onm = $student->where('state' , '1')->count('state') ;
        $nom = $student->where('state' , '0')->count('state') ;
        $result = [
            'onmajor' => $onm ,
            'nomajor' => $nom
        ] ;
        return json($result) ;
    }
//  数据图 2 各个方向 已选择 / 未选择
    public function countChoice ()
    {
        $stu = new Student() ;
//        $stu1 = $stu->where('major' , '1') ; // 专业向 通信
        $stuCountSelected1 =  $stu->where('major' , '1')->where('state' , '1')->count('state') ; // 已填报人数
        $stuCountNoSelected1 = $stu->where('major' , '1')->where('state' , '0')->count('state') ; // 未填报人数

//        $stu2 = $stu->where('major' , '2') ; // 专业向的总数 计算机
        $stuCountSelected2 =  $stu->where('major' , '2')->where('state' , '1')->count('state') ; // 已填报人数
        $stuCountNoSelected2 = $stu->where('major' , '2')->where('state' , '0')->count('state') ; // 未填报人数

        return show('1' , '通信/计算机' , [
            'computerY'         => $stuCountSelected2 ,
            'computerN'         => $stuCountNoSelected2 ,
            'communicationY'    =>  $stuCountSelected1,
            'communicationN'    =>  $stuCountNoSelected1
            /*
             *  result [ 通信已填报 , 通信未填报 , 计算机已填报 , 计算机未填报 ]
             *  result.communicationY
             */
        ]);
    }
//  数据图 3 返回各个方向的人数和方向名字
    public function majorNameCountNum()
    {
        $major = new Majordire() ;
        $fillin = new Fillin() ;

        $major_Direname = $major -> field('majorname') ->select() ;
//        return json($major_Direname) ;
//        return $major_Direname[0]['majorname'] ;
        $major_name_count_num = [] ;
        for ( $i = 0 ; $i < count($major_Direname) ; $i++)
        {
            $major_name = $major_Direname[$i]['majorname'];
            $major_num = $fillin->where('majorname', $major_name)->count('majorname');
            $major_name_count_num[$i] = [
                'majorname' =>   $major_name ,
                'majornum'  =>   $major_num
            ] ;
        }

//        return json($major_name_count_num) ;

        return show('1' , 'test' , $major_name_count_num) ;


    }

//  添加学生信息
    public function addStudent()
    {
        $request = Request::instance() ;
        $data = $request->param();

        $student = $request->param('student');
        $stuclass = $request->param("stuclass");
        $stuname = $request->param("stuname") ;
        $aftersix = $request->param("aftersix") ;
        $major = $request->param("majorname") ;
        $gender  = $request->param("gender");
        $category = $request->param("category") ;
//        $majorname = $request->param("majorname");

//       if(empty($student) || empty($stuclass) || empty($stuname) || empty($aftersix) || empty($major) || empty($gender)){
       if(empty($student) || empty($stuclass) || empty($stuname) || empty($major) || empty($gender)){
            return show( '0' , '数据为空,拒绝操作') ;
        }

        $update = [
            'student' => $student ,
            'stuclass' => $stuclass ,
            'stuname' => $stuname ,
            'aftersix' => '123456' ,
            'major' => $major ,
            'groupid' => $major ,
            'state' => '0' ,
            'switch' => '0' ,
            'gender' => $gender ,
            'year' => '2019' ,
            'category' => $category ,
//            'majorname' => $majorname ,
//            ''
        ] ;

        $stuInfo = new  Student() ;
        $result = $stuInfo->where('student' , $student)->count() ;
        if ($result){
            return show('2' , '该学号已存在,不可重复添加') ;
        }

        $result = $stuInfo->insert($update) ;

        if ( $result == '1'){
            return show( '1' , '新用户添加成功') ;
        } else {
            return show ('0' , '未成功添加用户，请确保添加的用户学号是正确的或未录入的');
        }
    }
//  删除一个学生
    public function deleteStudent()
    {
        $request = Request::instance() ;
        $student = $request->param('student');

        $stu = new Student() ;
        $result = $stu -> deleteStu( $student ) ;
        if ($result) {
            return show( '1' , '该学生已经被成功删除') ;
        } else {
            return show( '0' , '没有删除成功或该学生不存在');
        }
    }
//  修改一个学生*
    public function reviseStudent()
    {

        $request = Request::instance() ;
        $data = $request->param();

        $id= $request->param('stuId');
        $student = $request->param('student');
        $stuclass = $request->param("stuclass");
        $stuname = $request->param("stuname") ;
        $aftersix = $request->param("aftersix") ;
        $major = $request->param("majorname") ;//ps:这里接收的时专业群
        $gender  = $request->param("gender");
        $category = $request->param("category") ;
//        $majorname = $request->param("majorname");

//       if(empty($student) || empty($stuclass) || empty($stuname) || empty($aftersix) || empty($major) || empty($gender)){
        if(empty($student) || empty($stuclass) || empty($stuname) || empty($major) || empty($gender)){
            return show( '0' , '数据为空,拒绝操作') ;
        }

        $update = [
            'student' => $student ,
            'stuclass' => $stuclass ,
            'stuname' => $stuname ,
            'aftersix' => '123456' ,
            'major' => $major ,
            'groupid' => $major ,
            'state' => '0' ,
            'switch' => '0' ,
            'gender' => $gender ,
            'year' => '2019' ,
            'category' => $category ,
//            'majorname' => $majorname ,
        ] ;

        $stuInfo = new  Student() ;

        $result = $stuInfo->where('id' , $id)->update($update) ;

//        if (!empty($result) || $result == 1){
        if ( $result == '1'){
            return show( '1' , '新用户添加成功') ;
        } else {
            return show ('0' , '未成功添加用户，请确保添加的用户学号是正确的或未录入的');
        }
    }

//  添加一个角色权限
    public function addAdmin() {
        $request = Request::instance();

        $username  = $request->param('username') ;
        $password  = $request->param('password') ;
        $roles     = $request->param('roles') ;

        if (empty($username) || empty($password) || empty($roles)){
            return show( ' 0' , '数据为空,拒绝操作') ;
        }
        $password = md5Password($password) ;
        $user = new Users() ;
        if($user->usersQuery($username)){
            return show( '2' , '该用户已存在不可以重复添加') ;
        } else if(session('userInfo.username') != 'admin' && $roles == '0'){
            return show ('0' , '你不配拥有设立管理的权限');
        }

        $update = [
            'username' => $username,
            'password' => $password,
            'roles' => $roles
        ];
        $result = $user->insert($update);

        if ( $result ){
            return show( '1' , '新用户添加成功') ;
        } else {
            return show ('0' , '未成功添加用户');
        }

    }
//  修改一个角色
    public function reviseAdmin()
    {
        $request = Request::instance();

        $id = $request->param('id');
        $username  = $request->param('username') ;
        $password  = $request->param('password') ;
        $roles     = $request->param('roles') ;
        if (empty($username) || empty($password) || empty($roles)){
            return show( ' 0' , '数据为空,拒绝操作') ;
        } else if ( $username == 'admin') {
            return show('2' , '超级管理员用户角色不可被操作');
        } else if ($username == session('userInfo.username')) {
            return show( '2' , '不能对自己的角色信息进行操作') ;
        }
        $password = md5Password($password) ;
        $update = [
            'username'  => $username ,
            'password'  => $password ,
            'roles'     => $roles
        ] ;

        $user = new Users() ;
        $result = $user->where('id' , $id)->update($update);

        if ( $result ){
            return show( '1' , '修改成功') ;
        } else {
            return show ('0' , '修改失败');
        }
    }
//  删除一个角色
    public function deleteAdmin()
    {
        $request = Request::instance() ;
        $username = $request->param('username');

        if (empty($username) ){
            return show( ' 0' , '请求数据为空,拒绝操作') ;
        } else if ( $username == 'admin') {
            return show('2' , '超级管理员用户角色不可删除');
        } else if ($username == session('userInfo.username')) {
            return show( '2' , '不能对自己的角色信息进行删除操作') ;
        }

//        $password = md5Password($password) ;
        $users = new Users() ;
        $result = $users -> where( 'username' , $username ) -> delete() ;
        if ($result) {
            return show( '1' , '该角色已经被成功删除') ;
        } else {
            return show( '0' , '没有删除成功或该角色不存在');
        }
    }

//  添加一个专业方向
    public function addMajor()
    {

        $request = Request::instance() ;

        $majorname = $request->param('majorname');
        $majorintroduce = $request->param("content");
        $majornumber = $request->param("majornumber") ;
        $pid = $request->param("pid") ;

        if (empty($majorname) || empty($pid) || empty($majornumber) ){
            return show( ' 0' , '数据为空,拒绝操作' , $pid) ;
        }

        $update = [
            'majorname' => $majorname ,
            'majorintroduce' => $majorintroduce ,
            'majornumber' => $majornumber ,
            'pid' => $pid
        ] ;

        $major = new  Majordire() ;

        $result = $major->insert($update) ;

//        if (!empty($result) || $result == 1){
        if ( $result == '1'){
            return show( '1' , '新专业方向成功添加') ;
        } else {
            return show ('0' , '未成功添加专业方向，请确保添加的专业方向的格式正确');
        }
    }
//  删除一个专业方向
    public function deleteMajor()
    {
        $request = Request::instance() ;
        $id = $request->param('id');

        if (empty($id) ){
            return show( ' 0' , '请求数据为空,拒绝操作') ;
        }

        $major = new Majordire() ;
        $result = $major -> where( 'id' , $id ) -> delete() ;
        if ($result) {
            return show( '1' , '该专业方向已经被成功删除') ;
        } else {
            return show( '0' , '没有删除成功或该专业方向不存在');
        }
    }
//  修改一个专业方向
    public function reviseMajor()
    {
        $request = Request::instance() ;
        $id = $result->param('majorId');
        $majorname = $request->param('majorname');
        $majorintroduce = $request->param("majorintroduce");
        $majornumber = $request->param("majornumber") ;
        $pid = $request->param("pid") ;

        if (empty($majorname) || empty($majorintroduce) || empty($majornumber) || empty($pid) || empty($id)){
            return show( ' 0' , '数据为空,拒绝操作') ;
        }

        $update = [
            'majorname' =>  $majorname ,
            'majorintroduce' => $majorintroduce ,
            'majornumber' => $majornumber ,
            'pid' => $pid
        ];

        $major = new Majordire() ;
        $result = $major -> where( 'id' , $id ) -> update($update) ;
        if ($result) {
            return show( '1' , '修改已成功');
        } else {
            return show( '0' , '修改未成功');
        }

    }
}