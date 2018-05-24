<?php
namespace app\user\model;
use think\Db;
use think\Model;
use app\user\model\PortalPostModel;
use app\user\model\UserModel;
class UserPayModel extends Model{
	protected $table = 'cmf_pay';
	
    /*
     * 与商品表关联
     * portal_post
     * */
    public function getPorPost(){
        return $this->hasOne('PortalPostModel','id','pid',[],'left');
    }

    /*
     * 与用户表关联
     * user
     * */
    public function getUser(){
        return $this->hasOne('UserModel','id','uid',[],'left');
    }
}
?>