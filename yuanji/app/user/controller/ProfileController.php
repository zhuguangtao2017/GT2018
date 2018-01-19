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
use think\Request;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use think\Db;
use app\portal\model\PortalCategoryModel;
use think\Session;
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
	    $my=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->sum('money');//总金钱

	   $weiti=Db::name('user_money_detail')->where(['uid'=>$uid])->where(['type'=>'2'])->where("allow","neq","1")->sum('money');//未通过提现的金额

	   $my=$my-$weiti;
//die();
		$chongzhi=Db::name('user_money_detail')->where(['uid'=>$uid])->where(['type'=>'3'])->sum('money');//充值金钱
		$xiaofei=Db::name('user_money_detail')->where(['uid'=>$uid])->where(['type'=>'4'])->where('pan','neq','1')->sum('money');//消费
		$yue=$chongzhi+$xiaofei;//充值 - 消费 = 充值余额
		 $shou=Db::name('user_money_detail')->where(['uid'=>$uid])->where("type",['=',1],['=',5],'or')->where('pan','neq','1')->sum('money');//收益
		 $tixian=Db::name('user_money_detail')->where(['uid'=>$uid])->where(['type'=>'2'])->where("allow",["=",1],["=",0],"or")->sum('money');//提现
		  
		   $arrr=$this->dailimoney($uid);
			$shengdaijine=$arrr["moneys"];
		 //die();
		 $shouyi=$shou+$tixian+$shengdaijine;//收益 - 提现 = 可提余额
        
		  $jffanxain=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>6])->sum('money');//充值返现
		//echo 'money';
        return $this->fetch('',['my'=>$my,'yue'=>$yue,'shouyi'=>$shouyi,"jffanxain"=>$jffanxain]);
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
		$arr=array();
		 $timeqi=input('timeqi');
		 $arr['timeqi']=input('timeqi');
		$arr['timezhi']=input('timezhi');
		$hidd=input("hidd");
		$query= Db::name('user_money_detail')
			->join('cmf_user','cmf_user.id=cmf_user_money_detail.from','left')
			->where(['uid'=>$id]);
		 if(!empty(input('timeqi'))||!empty(input('timezhi'))){
			 if(!empty($timeqi)&&empty(input('timezhi'))){
				$query=$query->where('time','>=',strtotime($arr['timeqi'])+3600*24);
			
				//die();
			}else if(empty($timeqi)&&!empty(input('timezhi')))
			{
				$query=$query->where('time','<=',strtotime($arr['timezhi'])+3600*24);
				
			}
			else if(!empty($timeqi)&&!empty(input('timezhi')))
			{
				$query=$query->where('time','>=',strtotime($arr['timeqi'])+3600*24)->where('time','<=',strtotime($arr['timezhi'])+3600*24);
				
			}
			 }
        if($role > 4){
            //echo '等级至少为5,则是普通会员,未入网,没有下级收入,无奖励,只有自己充值的';
        }
        $res =$query->order('time desc')->paginate('10');
        return $this->fetch('',['money'=>$res,'page'=>$res->render(),'arr'=>$arr]);
    }

    /*
     * 余额提现
     * 只能提现奖励的金额,而不能提现自己充值的金额
     * 提现的钱分为自己充值|下级贡献
     * */
    public function MoneyTiXian(){
        $user = cmf_get_current_user();
		$uid=cmf_get_current_user_id();
        $this->assign($user);
		$shou=Db::name('user_money_detail')->where(['uid'=>$uid])->where("type",['=',1],['=',5],'or')->where('pan','neq','1')->sum('money');//收益
		$tixian=Db::name('user_money_detail')->where(['uid'=>$uid])->where(['type'=>'2'])->where("allow",["=",1],["=",0],"or")->sum('money');//提现
		
			$arrr=$this->dailimoney($uid);//省代收益或代理收益
			$shengdaijine=$arrr["moneys"];

$shouxufei=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>7])->sum('money');//手续费

$czfanxain=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>6])->sum('money');//充值返现
		$shouyi=$shou+$tixian+$shengdaijine+$shouxufei-$czfanxain;//收益 - 提现 = 可提余额
		//$shouyi=$shou+$tixian;//收益 - 提现 = 可提余额
		$zhifubao='';
		$zhanghao=Db::name('payment')->where(['uid'=>$uid])->find();
				if(!empty($zhanghao)){
				$zhifubao=$zhanghao['zhifubao'];
				}
        return $this->fetch('',['shouyi'=>$shouyi,'zhifubao'=>$zhifubao]);
    }
	public function MoneyTX()
	{
		//$money=-100;
		$uid=cmf_get_current_user_id();

		$shou=Db::name('user_money_detail')->where(['uid'=>$uid])->where("type",['=',1],['=',5],'or')->where('pan','neq','1')->sum('money');//收益
		$tixian=Db::name('user_money_detail')->where(['uid'=>$uid])->where(['type'=>'2'])->where("allow",["=",1],["=",0],"or")->sum('money');//提现
		
		$arrr=$this->dailimoney($uid);
			$shengdaijine=$arrr["moneys"];

		$shouxufei=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>7])->sum('money');//手续费
		$czfanxain=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>6])->sum('money');//充值返现

		$shouyi=$shou+$tixian+$shengdaijine+$shouxufei-$czfanxain;//收益 - 提现 = 可提余额
	
		
		if ($this->request->isPost()) {
		$data = $this->request->post();
		$money='-'.$data['money'];
		if($data["money"]>$shouyi)	
		{
			die("错误");
		}
		if($money%200!=0)
		{
		    die("提现金额必须为200的倍数");
		}
		$xixi='';
				$fspan='';
				$fangshi=input('interface');//支付方式
				if(empty($fangshi))
				{
					die("错误");
				}
				//die("yibi");
				$zhanghao=Db::name('payment')->where(['uid'=>$uid])->find();
				if($fangshi==1)
				{
					$zhanghaos=input('hao');//支付账号
					 $xixi='支付宝('.$zhanghaos.')';
					 $fspan=1;
				}else if($fangshi==2)
				{
					$zhanghaos=input('hao');//支付账号
					 $xixi='微信('.$zhanghaos.')';
					  $fspan=2;
				}				
				else if($fangshi==4)
				{
					$yhk=input('yhk');
					$aa='';
					if($yhk==1)
					{
						$aa=$zhanghao['yhk1'];
					}elseif($yhk==2)
					{
						$aa=$zhanghao['yhk2'];
					}
					elseif($yhk==3)
					{
						$aa=$zhanghao['yhk3'];
					}
					$xixi='银行卡('.$aa.')';
					$fspan=4;
				}
		
		$user = ['uid'=>$uid,'money'=>$money,'from'=>'','time'=>time(),'type'=>'2','fs'=>$xixi,'fspan'=>$fspan];
		$aa=Db::name('user_money_detail')->insert($user);
			if($aa){

				$this->success('发起提现成功','profile/moneydetail','',1);
			}
		}else
		{
		return $this->fetch();
		}
	}

 
	public function chong(){
		$MerNo="43578";
		//MD5私钥
		$MD5key = "yuanjitituan";
		//订单号
		$BillNo = $this->request->param('BillNo'); 
		//一麻袋支付订单号
		$OrderNo= $this->request->param('OrderNo'); 
		//金额
		$Amount = $this->request->param('Amount');  
		//支付状态
		$Succeed = $this->request->param('Succeed');   
		//支付结果
		$Result = $this->request->param('Result');   
		//取得的MD5校验信息
		$SignInfo = $this->request->param('SignInfo');  
		//备注
		$Remark = $this->request->param('Remark'); 
    	//校验源字符串
         $md5src = "MerNo=".$MerNo."&BillNo=".$BillNo."&OrderNo=".$OrderNo."&Amount=".$Amount."&Succeed=".$Succeed."&".$MD5key;
       //MD5检验结果
	     $md5sign = strtoupper(md5($md5src));

		  if ($SignInfo!=$md5sign){                
				 die();
		  }

		  if($Succeed=='88' && $Result=='SUCCESS'){
		  //  $uid=cmf_get_current_user_id();
	       // $data = $this->request->param;
			//$money=$data['money'];
			//$fangshi=input('interface');
			//$uid = $this->request->param('uid'); 
			$uid= $this->request->param('uid', 0, 'intval');

			$fangshi=4;
			if($fangshi==1)
				{
					 $zhanghaos=input('hao');//支付账号
					 $xixi='支付宝';
					 $fspan=1;
				}else if($fangshi==2)
				{
					$zhanghaos=input('hao');//支付账号
					 $xixi='微信';
					  $fspan=2;
				}
				else if($fangshi==4)
				{					
					$xixi='银行卡';
					$fspan=4;
				}
		$user = ['uid'=>$uid,'money'=>$Amount,'from'=>'','time'=>time(),'type'=>'3','fs'=>$xixi,'fspan'=>$fspan];
		$aa=Db::name('user_money_detail')->insert($user);
		 if($aa){echo 'ok';}else{echo 'fail'; }
		}
	
	}
	public function MoneyCharge()
	{
		//$money=100;
		$uid=cmf_get_current_user_id();
		$user = cmf_get_current_user();
        $this->assign($user);
		if ($this->request->isPost()) {
			 //支付接口
			$MD5key = "yuanjitituan";		//MD5私钥
			$MerNo = "43578";	
			$data = $this->request->post();
		    $money=$data['money'];
		   // $money=0.01;
					  
			$BillNo ="SNO".date('Ymd',time()).substr(time(),'-6');		//[必填]订单号(商户自己产生：要求不重复)
			$Amount = $money;// $money;//$money;				//[必填]订单金额
					   
			$ReturnURL = "http://".$_SERVER['SERVER_NAME']."/yuanji/public/index.php/user/profile/money"; 			//[必填]返回数据给商户的地址(商户自己填写):::注意请在测试前将该地址告诉我方人员;否则测试通不过
						
		   $AdviceURL  = "http://".$_SERVER['SERVER_NAME']."/yuanji/public/index.php/portal/index/chong/uid/$uid"; 	 //[必填]支付完成后，后台接收支付结果，可用来更新数据库值
			$Remark = "";  //[选填]升级。
						 
			$OrderTime =date('YmdHis');   //[必填]交易时间YYYYMMDDHHMMSS

		    $md5src = "MerNo=".$MerNo. "&BillNo=".$BillNo."&Amount=".$Amount."&OrderTime=".$OrderTime. "&ReturnURL=".$ReturnURL ."&AdviceURL=".$AdviceURL."&".$MD5key ;		//校验源字符串
			 $SignInfo = strtoupper(md5($md5src));		//MD5检验结果
			 //送货信息(方便维护，请尽量收集！如果没有以下信息提供，请传空值:'')
			 //因为关系到风险问题和以后商户升级的需要，如果有相应或相似的内容的一定要收集，实在没有的才赋空值,
			$products='充值';// '------------------物品信息
		    return $this->fetch('/cardpay',['money'=>$money,'MerNo'=>$MerNo,'BillNo'=>$BillNo,'Amount'=>$Amount,'ReturnURL'=>$ReturnURL,'AdviceURL'=>$AdviceURL,'OrderTime'=>$OrderTime,'SignInfo'=>$SignInfo,'Remark'=>$Remark,'products'=>$products]);
		}else
		{
			return $this->fetch();
		}
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
		//$pan=input('pan');
		//if($pan=='2'){
		//$iid=input('iid');
		//}
		 $iid=input('iid');
        $user = cmf_get_current_user();
        $uid=$user['id'];
        $id=input('id');
        $type=input('type');
        $dat=Db::name('address')->where('id',$id)->find();
		 $pan=input('pan');
        if(Request::instance()->isPost()){
			 $pan=input('pan');
			 $iid=input('iid');
            $request = input('param.');
            if(array_key_exists('sub', $request)){
                    $validate = new Validate([
                    'aname'    => 'require',

                    'aaddress' => 'require',
                    'atel'     =>'require|number',
                ]);
                $validate->message([
                    'aname.require'          => '收货人不能为空',
                    'aaddress.require'       => '收货地址不能为空',
                    'atel.require'           => '联系方式不能为空',
                    'atel.number'            => '联系方式只能为数字'
                ]);

                $data = $this->request->post();
                if (!$validate->check($data)) {
                    $this->error($validate->getError());
                }
                $uid=$user['id'];
                $name=input('aname');
                $address=input('aaddress');
                $tel=input('atel');
                $dat = ['uid' => $uid, 'name' => $name,'address'=>$address,'tel'=>$tel];    
                $add=Db::name('address')->insert($dat);
                if($add==1){
					//echo '----------';
					//var_dump($pan);
					if($pan==2){
						//$this->redirect('portal/article/buy',['id'=>$iid]);
						$this->success('添加成功','/yuanji/public/index.php/portal/article/buy/id/'.$iid,'',1);
						//die();
					}
					else
					{
						//die;
						//echo 1321;
						$this->success('添加成功','address','',1);
					}
                   

					
                }
                else{
                    $this->error();
                }
            }else if(array_key_exists('submit', $request)){
                $validate = new Validate([
                    'name'    => 'require',
                    'address' => 'require',
                    'tel'     =>'require|number',
                ]);
                $validate->message([
                    'name.require'          => '收货人不能为空',
                    'address.require'       => '收货地址不能为空',
                    'tel.require'           => '联系方式不能为空',
                    'tel.number'            =>'联系方式只能为数字',
                ]);

                $data = $this->request->post();
                if (!$validate->check($data)) {
                    $this->error($validate->getError());
                }

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
					if($pan=='2'){
						$this->success('添加成功','/yuanji/public/index.php/portal/article/buy/id/'.$iid,'',1);
						//die();
					}
					else
					{
                   $this->success('修改成功','address','',1);
					}
					//}
					//else{
					//	$this->redirect('portal/article/buy',['id'=>input('iid')]);
					//}
                }
                else{
                    
                    $this->error();
                }
            }
        }
        else{
			$this->assign('pan',@$pan);
			$this->assign('iid',@$iid);
				$this->assign('type',$type);
				$this->assign('data',$dat);
			    return $this->fetch();
	        }
    }

public function del()
{
    $id=input('id');
	$pan=input('pan');
	$iid=input('iid');
    $del=Db::name('address')->where('id',$id)->delete();
	if($del==1)
	{
		if($pan==2){
			//$this->error('删除成功','portal/article/buy','',0);	
			$this->redirect('portal/article/buy',['id'=>$iid]);
        }
        else $this->error('删除成功','address','',2);
	}
    else{
            $this->error('删除失败');
    }
        
}
public function gouwu()
{
     $user = cmf_get_current_user();
        $uid=$user['id'];
        $pay     = Db::name('pay')->where('uid',$uid)
            ->order('id DESC')->paginate('10');
        $this->assign($user);
        $this->assign("page", $pay->render());
        $this->assign("pay", $pay);
        return $this->fetch();
}
    public function shengdai()
	{
        
        $admin = cmf_get_current_user();
		$uid=cmf_get_current_user_id();
		$moneys=0;
        $arrr=$this->dailimoney($uid);
		 $moneys=$arrr["moneys"];
        //$my=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->sum('money');//总余额      //代理或省代明细表收益

		 $my=Db::name('user_money_detail')->where(['uid'=>$uid])->where("type",['=',1],['=',5],'or')->where('pan','neq','1')->sum('money');//收益
		
$xiaofei=Db::name('user_money_detail')->where(['uid'=>$uid])->where(['type'=>'4'])->sum('money');//消费
      
		$chongzhi=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>3])->sum('money');//充值

		$tixian=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>2])->where("allow",["=",1],["=",0],"or")->sum('money');//提现

	    $shouxufei=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>7])->sum('money');//手续费

		$jffanxain=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>6])->sum('money');//充值返现 / 券

		  $summoney = $my+$moneys+$chongzhi+$tixian+$shouxufei+$xiaofei-$jffanxain;

	$tixian1=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>2])->where("allow",1)->sum('money');//已提现


		  $shengqing=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>2])->where(["allow"=>0])->sum('money');//申请提现中金额

		  $jffanxain=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->where(["type"=>6])->sum('money');//充值返现
        return $this->fetch('',['my'=>$my,'moneys'=>$moneys,'summoney'=>$summoney,'type'=>$admin['user_type'],'chongzhi'=>$chongzhi,'tixian'=>$tixian1,'shenqing'=>$shengqing,"jffanxain"=>$jffanxain]);
	}
    public function xiaxian()
    {
        $uid=cmf_get_current_user_id();
		$arr;
		$arr['id']=input('id');
		$arrr=$this->dailimoney($uid);
		$strs=$arrr["str"];
        if(!empty($strs)||$arr['id'])
        {
			$zongshu=count(explode(',',$strs));//当前下线总人数
			$timeqi=strtotime(input('timeqi'));
			$timezhi=strtotime(input('timezhi'))+(60*60*24);
			//;
			$arr['timeqi']=input('timeqi');
			$arr['timezhi']=input('timezhi');
			
             $money = Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$strs);
			 $sum=Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$strs)->count('distinct(uid)');
			 $may=Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$strs);
			 if(!empty(input('timeqi'))||!empty(input('timezhi'))){
			 if(!empty($timeqi)&&empty(input('timezhi'))){
				$money=$money->where('time','>=',$timeqi);
				//die();
				$may=$may->where('time','>=',$timeqi);
			}else if(empty($timeqi)&&!empty(input('timezhi')))
			{
				$money=$money->where('time','<=',$timezhi);
				$may=$may->where('time','<=',$timezhi);
			}
			else if(!empty($timeqi)&&!empty(input('timezhi')))
			{
				$money=$money->where('time','>=',$timeqi)->where('time','<=',$timezhi);
				$may=$may->where('time','>=',$timeqi)->where('time','<=',$timezhi);
			}
			 }
			 if(!empty($arr["id"]))
			{
				$money=Db::name('pay')->join('user','user.id=uid')->where('uid',$arr["id"]);
				$may=Db::name('pay')
				->join('user','user.id=uid')->where('uid',$arr["id"]);
				$sum=Db::name('pay')
				->join('user','user.id=uid')->where('uid',$arr["id"])->count('distinct(uid)');;
             
			 }
			$money=$money->order('time DESC')->paginate();
			$may=$may->sum('money');
			$kong=!empty($money)?"1":"";
			
             return $this->fetch('',['pay'=>$money,'page'=>$money->render(),'arr'=>$arr,'sum'=>$sum,'may'=>$may,'zongshu'=>$zongshu,'ppp'=>1,"kong"=>$kong]);
        }
        else
        {
        	$this->error('暂未分享');
        }
    }
    public function zhifu()
    {
        $uid=cmf_get_current_user_id();
        $arr = Db::name('payment')->where('uid',$uid)->find();
        $type = Db::name('yhtype')->select();
       return $this->fetch('',['arr'=>$arr,'type'=>$type]);
    }
    public function ajax()
    {
        $uid=cmf_get_current_user_id();
        if(input('zfs'))
        {
            $ar = Db::name('payment')->where('uid',$uid)->count();
           // dump($ar);
            if($ar!=0)
            {
                $success = Db::name('payment')->where('uid',$uid)->update([
                        input('name') => input('val')
                    ]);
            }
            else
            {
               $success = Db::name('payment')->insert([
                        'uid' => $uid,
                        input('name') => input('val')
                    ]);
            }
            if($success)
            {
                echo '添加成功';
            }
        }
        if(input('zfy'))
        {
            $ar = Db::name('payment')->where('uid',$uid)->find();
            if($ar)
            {
            	if(input('tel')){
            		$success = Db::name('payment')->where('uid',$uid)->update([
                        input('name') => input('lval').'-'.input('sel').'-'.input('nval').'-'.input('sheng').'-'.input('shi').'-'.input('zh').'-'.input('tel')
                    ]);
            	}else{
            		$success = Db::name('payment')->where('uid',$uid)->update([
                        input('name') => input('lval').'-'.input('sel').'-'.input('nval').'-'.input('sheng').'-'.input('shi').'-'.input('zh')
                    ]);
            	}
                
            }
            else
            {
            	if(input('tel')){
            		$success = Db::name('payment')->insert([
                        'uid' => $uid,
                        input('name') => input('lval').'-'.input('sel').'-'.input('nval').'-'.input('sheng').'-'.input('shi').'-'.input('zh').'-'.input('tel')
                    ]);
            	}else{
            		$success = Db::name('payment')->insert([
                        'uid' => $uid,
                        input('name') => input('lval').'-'.input('sel').'-'.input('nval').'-'.input('sheng').'-'.input('shi').'-'.input('zh')
                    ]);
            	}
            }
            
        }
    }
	 public function axian()
    {
        $uid=cmf_get_current_user_id();
		$users = new UserModel();
		$user = Db::name('user')->where('id',$uid)->find();
		
		$arrr=$users->getAB($user['id'],$user['pid'],'a');
		$zongshu=count($arrr);//a线总人数
		/*$zz=$users->where(['fid'=>78,'pid'=>'5'])->field('id')->select()->toArray();
		foreach($zz as $key=>$v)
		{
			$arrr[$key]=$v['id'];
		}
		print_r($zz);
		echo $uid;
		print_r($arrr);
		die();*/
		$arr;
        if(!empty($arrr))
        {
			$timeqi=strtotime(input('timeqi'));
			$timezhi=strtotime(input('timezhi'))+(60*60*24);
			//;
			$arr['timeqi']=input('timeqi');
			$arr['timezhi']=input('timezhi');

             $money = Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$arrr);

			 $sum=Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$arrr)->count('distinct(uid)');
			 $may=Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$arrr);
			 if(!empty(input('timeqi'))||!empty(input('timezhi'))){
			 if(!empty($timeqi)&&empty(input('timezhi'))){
				$money=$money->where('time','>=',$timeqi);
				$may=$may->where('time','>=',$timeqi);
				//die();
			}else if(empty($timeqi)&&!empty(input('timezhi')))
			{
				$money=$money->where('time','<=',$timezhi);
				$may=$may->where('time','<=',$timezhi);
			}
			else if(!empty($timeqi)&&!empty(input('timezhi')))
			{
				$money=$money->where('time','>=',$timeqi)->where('time','<=',$timezhi);
				$may=$may->where('time','>=',$timeqi)->where('time','<=',$timezhi);
			}
			 }
			$money=$money->order('time DESC')->paginate();
			$may=$may->sum('money');
             return $this->fetch('',['pay'=>$money,'page'=>$money->render(),'arr'=>$arr,'sum'=>$sum,'may'=>$may,'zongshu'=>$zongshu,'ppp'=>2]);
        }
        else
        {
        	$this->error('A侧暂无分享');
        }
    }
	 public function bxian()
    {
        $uid=cmf_get_current_user_id();
		$users = new UserModel();
		$user = Db::name('user')->where('id',$uid)->find();
		$arrr=$users->getAB($user['id'],$user['pid'],'b');
		$zongshu=count($arrr);//b线总人数
		/*$zz=$users->where(['fid'=>78,'pid'=>'5'])->field('id')->select()->toArray();
		foreach($zz as $key=>$v)
		{
			$arrr[$key]=$v['id'];
		}
		print_r($zz);
		echo $uid;
		print_r($arrr);
		die();*/
		$arr;
        if(!empty($arrr))
        {
			$timeqi=strtotime(input('timeqi'));
			$timezhi=strtotime(input('timezhi'))+(60*60*24);
			//;
			$arr['timeqi']=input('timeqi');
			$arr['timezhi']=input('timezhi');

             $money = Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$arrr);

			 $sum=Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$arrr)->count('distinct(uid)');
			 $may=Db::name('pay')
             ->join('user','user.id=uid')
             ->where('uid','in',$arrr);
			 if(!empty(input('timeqi'))||!empty(input('timezhi'))){
			 if(!empty($timeqi)&&empty(input('timezhi'))){
				$money=$money->where('time','>=',$timeqi);
				$may=$may->where('time','>=',$timeqi);
				//die();
			}else if(empty($timeqi)&&!empty(input('timezhi')))
			{
				$money=$money->where('time','<=',$timezhi);
				$may=$may->where('time','<=',$timezhi);
			}
			else if(!empty($timeqi)&&!empty(input('timezhi')))
			{
				$money=$money->where('time','>=',$timeqi)->where('time','<=',$timezhi);
				$may=$may->where('time','>=',$timeqi)->where('time','<=',$timezhi);
			}
			 }
			$money=$money->order('time DESC')->paginate();
			$may=$may->sum('money');
             return $this->fetch('',['pay'=>$money,'page'=>$money->render(),'arr'=>$arr,'sum'=>$sum,'may'=>$may,'zongshu'=>$zongshu,'ppp'=>3]);
        }
        else
        {
        	$this->error('B侧暂无分享');
        }
    }

	//下线图
	public function myxiaxian()
	{
		$uid=cmf_get_current_user_id();
		$pid=Db::name("user")->field("pid")->where("id",$uid)->find();
		$userpid=$pid["pid"];
		$portalCategoryModel = new PortalCategoryModel();
		//print_r($portalCategoryModel);
		
        $categoryTree        = $portalCategoryModel->adminCategoryTable1Tree($uid,$userpid);

        $this->assign('arr', $categoryTree);
       
		return $this->fetch("");
	}


		//求其代理或省代收益
	public function dailimoney($uid)
	{
		$dali = Db::name('dali')->find();
		$user = Db::name('user')->where('id',$uid)->find();
        if($user['user_type']==2){
        $pids = Db::name('user')->where('pid',$user['pid'])->where('fid','neq','0')->field('id')->select();
		$str=array();
            foreach($pids as $val)
            {
                $arr[] = $val['id'];
            }
            @$str = implode(',',$arr);
		
            $money = Db::name('user_money_detail')->where('uid','in',$str)->where('type','4')->sum('money');
            $moneys = $dali['sdai']/100*-$money;                                    //省代下线收益
           
        }
        else if($user['user_type']==3||$user['user_type']==4)
        {
            $fid = array($user['id']);
            $pid = $user['pid'];
            $users = new UserModel();
            $arr = $users->getSons($fid,$pid);
			$users->banArr();
            $moneys = $users->getThreeMoney($arr)*$dali['daili']/100;               //代理下线收益
            $str = implode(',',$arr);
        }
	
		return $arrs=["str"=>@$str,"moneys"=>@$moneys];
	}
}