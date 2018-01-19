<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;
use cmf\lib\Storage;
use think\Validate;
use think\Image;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use think\Db;

class ProfileController extends UserBaseController
{

    function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心首页
     */
    public function center()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 编辑用户资料
     */
    public function edit()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch('edit');
    }

    /**
     * 编辑用户资料提交
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'user_nickname' => 'chsDash|max:32',
                'sex'     => 'number|between:0,2',
                'birthday'   => 'dateFormat:Y-m-d|after:-88 year|before:-1 day',
                'user_url'   => 'url|max:64',
                'signature'   => 'chsDash|max:128',
            ]);
            $validate->message([
                'user_nickname.chsDash' => '昵称只能是汉字、字母、数字和下划线_及破折号-',
                'user_nickname.max' => '昵称最大长度为32个字符',
                'sex.number' => '请选择性别',
                'sex.between' => '无效的性别选项',
                'birthday.dateFormat' => '生日格式不正确',
                'birthday.after' => '出生日期也太早了吧？',
                'birthday.before' => '出生日期也太晚了吧？',
                'user_url.url' => '个人网址错误',
                'user_url.max' => '个人网址长度不得超过64个字符',
                'signature.chsDash' => '个性签名只能是汉字、字母、数字和下划线_及破折号-',
                'signature.max' => '个性签名长度不得超过128个字符',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $editData = new UserModel();
            if ($editData->editData($data)) {
                $this->success("保存成功！", "user/profile/center");
            } else {
                $this->error("没有新的修改信息！");
            }
        } else {
            $this->error("请求错误");
        }
    }

    /**
     * 个人中心修改密码
     */
    public function password()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 个人中心修改密码提交
     */
    public function passwordPost()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'old_password' => 'require|min:6|max:32',
                'password'     => 'require|min:6|max:32',
                'repassword'   => 'require|min:6|max:32',
            ]);
            $validate->message([
                'old_password.require' => '旧密码不能为空',
                'old_password.max'     => '旧密码不能超过32个字符',
                'old_password.min'     => '旧密码不能小于6个字符',
                'password.require'     => '新密码不能为空',
                'password.max'         => '新密码不能超过32个字符',
                'password.min'         => '新密码不能小于6个字符',
                'repassword.require'   => '重复密码不能为空',
                'repassword.max'       => '重复密码不能超过32个字符',
                'repassword.min'       => '重复密码不能小于6个字符',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $login = new UserModel();
            $log   = $login->editPassword($data);
            switch ($log) {
                case 0:
                    $this->success('修改成功');
                    break;
                case 1:
                    $this->error('密码输入不一致');
                    break;
                case 2:
                    $this->error('原始密码不正确');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }

    }

    // 用户头像编辑
    public function avatar()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    // 用户头像上传
    public function avatarUpload()
    {
        $file   = $this->request->file('file');
        $result = $file->validate([
            'ext'  => 'jpg,jpeg,png',
            'size' => 1024 * 1024
        ])->move('.' . DS . 'upload' . DS . 'avatar' . DS);

        if ($result) {
            $avatarSaveName = str_replace('//', '/', str_replace('\\', '/', $result->getSaveName()));
            $avatar         = 'avatar/' . $avatarSaveName;
            session('avatar', $avatar);

            return json_encode([
                'code' => 1,
                "msg"  => "上传成功",
                "data" => ['file' => $avatar],
                "url"  => ''
            ]);
        } else {
            return json_encode([
                'code' => 0,
                "msg"  => $file->getError(),
                "data" => "",
                "url"  => ''
            ]);
        }
    }

    // 用户头像裁剪
    public function avatarUpdate()
    {
        $avatar = session('avatar');
        if (!empty($avatar)) {
            $w = $this->request->param('w', 0, 'intval');
            $h = $this->request->param('h', 0, 'intval');
            $x = $this->request->param('x', 0, 'intval');
            $y = $this->request->param('y', 0, 'intval');

            $avatarPath = "./upload/" . $avatar;

            $avatarImg = Image::open($avatarPath);
            $avatarImg->crop($w, $h, $x, $y)->save($avatarPath);

            $result = true;
            if ($result === true) {
                $storage = new Storage();
                $result  = $storage->upload($avatar, $avatarPath, 'image');

                $userId = cmf_get_current_user_id();
                Db::name("user")->where(["id" => $userId])->update(["avatar" => $avatar]);
                session('user.avatar', $avatar);
                $this->success("头像更新成功！");
            } else {
                $this->error("头像保存失败！");
            }

        }
    }

    /**
     * 绑定手机号或邮箱
     */
    public function binding()
    {
        $user = cmf_get_current_user();
        $uid  =  cmf_get_current_user_id();
        $this->assign($user);
        $this->assign('uid',$uid);
        return $this->fetch();
    }

    /**
     * 绑定手机号
     */
    public function bindingMobile()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'username'          => 'require|number|unique:user,mobile',
                'verification_code' => 'require',
            ]);
            $validate->message([
                'username.require'          => '手机号不能为空',
                'username.number'          => '手机号只能为数字',
                'username.unique'          => '手机号已存在',
                'verification_code.require' => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }
            $userModel = new UserModel();
            $log       = $userModel->bindingMobile($data);
            switch ($log) {
                case 0:
                    $this->success('手机号绑定成功');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }
    }

    /**
     * 绑定邮箱
     */
    public function bindingEmail()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'username'          => 'require|email|unique:user,user_email',
                'verification_code' => 'require',
            ]);
            $validate->message([
                'username.require'          => '邮箱地址不能为空',
                'username.email'            => '邮箱地址不正确',
                'username.unique'           => '邮箱地址已存在',
                'verification_code.require' => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }
            $userModel = new UserModel();
            $log       = $userModel->bindingEmail($data);
            switch ($log) {
                case 0:
                    $this->success('邮箱绑定成功');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }
    }

	
    /*
     * 用户的钱包,关于奖励以及余额
     * 奖励来源为:
     * 下级消费
     * */
    public function Money(){
        $user = cmf_get_current_user();
        $this->assign($user);
		$uid=cmf_get_current_user_id();
		$my=Db::name('user_money_detail')->where(['uid'=>$uid])->sum('money');
        //echo 'money';
        return $this->fetch('',['my'=>$my]);
    }

    /*
     * 奖励明细
     * 循环明细表,受益人为该用户的
     * */
    public function MoneyDetail(){
        $user = cmf_get_current_user();
        $this->assign($user);
        $id = $user['id'];
        $role = $user['user_type'];
        if($role > 4){
            //echo '等级至少为5,则是普通会员,未入网,没有下级收入,无奖励,只有自己充值的';
        }
        $res = Db::name('user_money_detail')
			->join('cmf_user','cmf_user.id=cmf_user_money_detail.uid')
			->where(['uid'=>$id])->order('time desc')->select();
        return $this->fetch('',['money'=>$res]);
    }

    /*
     * 余额提现
     * 只能提现奖励的金额,而不能提现自己充值的金额
     * 提现的钱分为自己充值|下级贡献
     * */
    public function MoneyTiXian(){
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }
	public function MoneyTX()
	{
		$money=-100;
		$uid=cmf_get_current_user_id();
		$user = ['uid'=>$uid,'money'=>$money,'from'=>'','time'=>time(),'type'=>'2'];
		$aa=Db::name('user_money_detail')->insert($user);
			if($aa){$this->success('提现100','profile/money');}
	}
	public function MoneyCharge()
	{
		$money=100;
		$uid=cmf_get_current_user_id();
		$user = ['uid'=>$uid,'money'=>$money,'from'=>'','time'=>time(),'type'=>'3'];
		$aa=Db::name('user_money_detail')->insert($user);
		if($aa){$this->success('充值100','profile/money');}
	}
    public function address(){
        $user = cmf_get_current_user();
        $uid=$user['id'];
        $address     = Db::name('address')->where('uid',$uid)
            ->order('id DESC')->paginate();
        $this->assign($user);
        $this->assign("page", $address->render());
        $this->assign("address", $address);
        return $this->fetch();
    }
public function add(){
        $user = cmf_get_current_user();
        $uid=$user['id'];
        $id=input('id');
        $type=input('type');
        $data=Db::name('address')->where('id',$id)->find();
        if(input('sub')){
	$uid=$user['id'];
	$name=input('aname');
	$address=input('aaddress');
	$tel=input('atel');
	$data = ['uid' => $uid, 'name' => $name,'address'=>$address,'tel'=>$tel];	
              $add=Db::name('address')->insert($data);
	if($add==1){
		$this->success('添加成功','address','',1);
	}
	else{
		$this->error();
	}
        }
        else if(input('submit')){
	$id=input('hid');
	$uid=$user['id'];
	$name=input('name');
	$address=input('address');
	$tel=input('tel');
              $add=Db::name('address')->where('id',$id)->update([
						       'uid' => $uid,
						       'name' => $name,
						       'address'=>$address,
						       'tel'=>$tel
						      ]);
	if($add==1){
		$this->success('修改成功','address','',1);
	}
	else{
		
		$this->error();
	}
        }
        else{
	
	$this->assign('type',$type);
	$this->assign('data',$data);
              return $this->fetch();
        }
    }
public function del()
{
	$id=input('id');
	$del=Db::name('address')->where('id',$id)->delete();
	if($del)
	{
		$this->success('删除成功','address','',1);
	}
}
public function gouwu()
{
     $user = cmf_get_current_user();
        $uid=$user['id'];
        $pay     = Db::name('pay')->where('uid',$uid)
            ->order('id DESC')->paginate();
        $this->assign($user);
        $this->assign("page", $pay->render());
        $this->assign("pay", $pay);
        return $this->fetch();
}
public function shengdai()
	{
		$my=Db::name('user_money_detail')->where(['uid'=>$uid])->sum('money');
        //echo 'money';
        return $this->fetch('',['my'=>$my]);
	}
}