<?php
namespace app\user\model;
use think\Db;
use think\Model;
use app\user\model\PortalPostModel;
use app\user\model\UserModel;
class UserPayModel extends Model{
	protected $table = 'cmf_pay';
	
    /*
     * ����Ʒ�����
     * portal_post
     * */
    public function getPorPost(){
        return $this->hasOne('PortalPostModel','id','pid',[],'left');
    }

    /*
     * ���û������
     * user
     * */
    public function getUser(){
        return $this->hasOne('UserModel','id','uid',[],'left');
    }
}
?>