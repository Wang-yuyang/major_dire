<?php
namespace app\index\model;
use think\Db;
use think\Model;
class Majordire extends Model
{
// 获取某一个方向类的所有子方向专业名称 $data是pid方向类
	public function getMajor( $date )
	{
		$result = $this
            ->where('pid',$date)
            ->select();

		return $result;
	}

// 通过专业名称来获取该方向的总招人数
	public function getNum( $data )
    {
        $result = $this
            ->where( 'majorname' , $data)
            ->value('majornumber');

        return $result;
    }
//  获取某方向的所有子方向名和介绍以及数量
    public function getMajordireAll( $pid )
    {
        $result = $this
            ->where('pid' , $pid )
            ->select();

        return $result ;
    }
}