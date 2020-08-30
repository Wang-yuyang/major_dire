<?php


namespace app\admin\model;


use think\Model;

class Majordire extends Model
{
    public function counAll()
    {
        $count = $this->select()->count('pid') ;
        return $count ;
    }
}