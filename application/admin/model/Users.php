<?php


namespace app\admin\model;


use think\Model;

class Users extends Model
{
//    判断是否存在该角色 （用在判断存在与否）
    public function usersQuery ($username){
        $result = $this->where('username' , $username)->count() ;
        return $result ;
    }
//     验证用户的密码信息
    public function checkUserPass( $user , $pass)
    {
        $result = $this->where([
            ['username' , $user],
            ['password' , $pass]
        ])->find();
    }
//  返回所有角色
    public function usersViewAll(){
        $result = $this->select();

        return $result ;
    }
//  判断用户是否存在并返回用户的个人表信息 （用在登录验证）
    public function verifyUsername( $username )
    {
        $result = $this
            ->where('username' , $username)
            ->find();

        return $result ;
    }
//
    public function updataUser($usernaem , $data)
    {
        $result = $this
            ->where('username' , $usernaem)
            ->update($data) ;

        return $result ;
    }
//  核验token
    public function catToken($username)
    {
        $result = $this
            ->where('username' , $username)
            ->value('token') ;
        return $result ;
    }
}