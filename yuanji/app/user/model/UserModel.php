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
namespace app\user\model;

use think\Db;
use think\Model;
use app\user\model\UserMoneyDetailModel;
class UserModel extends Model
{
	public static $num = 1;
	public static $sonsIds = [];
	public static $sonsInfo = [];
    public function doMobile($user)
    {
        $userQuery = Db::name("user");

        $result = $userQuery->where('mobile', $user['mobile'])->find();


        if (!empty($result)) {
            $comparePasswordResult = cmf_compare_password($user['user_pass'], $result['user_pass']);
            $hookParam =[
                'user'=>$user,
                'compare_password_result'=>$comparePasswordResult
            ];
            hook_one("user_login_start",$hookParam);
            if ($comparePasswordResult) {
                //拉黑判断。
                if($result['user_status']==0){
                    return 3;
                }
                session('user', $result);
                $data = [
                    'last_login_time' => time(),
                    'last_login_ip'   => get_client_ip(0, true),
                ];
                $userQuery->where('id', $result["id"])->update($data);
                return 0;
            }
            return 1;
        }
        $hookParam =[
            'user'=>$user,
            'compare_password_result'=>false
        ];
        hook_one("user_login_start",$hookParam);
        return 2;
    }

    public function doName($user)
    {
        $userQuery = Db::name("user");

        $result = $userQuery->where('user_nickname', $user['user_nickname'])->find();
        if (!empty($result)) {
            $comparePasswordResult = cmf_compare_password($user['user_pass'], $result['user_pass']);
            $hookParam =[
                'user'=>$user,
                'compare_password_result'=>$comparePasswordResult
            ];
            hook_one("user_login_start",$hookParam);
            if ($comparePasswordResult) {
                //拉黑判断。
                if($result['user_status']==0){
                    return 3;
                }
                session('user', $result);
                $data = [
                    'last_login_time' => time(),
                    'last_login_ip'   => get_client_ip(0, true),
                ];
                $userQuery->where('id', $result["id"])->update($data);
                return 0;
            }
            return 1;
        }
        $hookParam =[
            'user'=>$user,
            'compare_password_result'=>false
        ];
        hook_one("user_login_start",$hookParam);
        return 2;
    }

    public function doEmail($user)
    {

        $userQuery = Db::name("user");

        $result = $userQuery->where('user_email', $user['user_email'])->find();


        if (!empty($result)) {
            $comparePasswordResult = cmf_compare_password($user['user_pass'], $result['user_pass']);
            $hookParam =[
                'user'=>$user,
                'compare_password_result'=>$comparePasswordResult
            ];
            hook_one("user_login_start",$hookParam);
            if ($comparePasswordResult) {

                //拉黑判断。
                if($result['user_status']==0){
                    return 3;
                }
                session('user', $result);
                $data = [
                    'last_login_time' => time(),
                    'last_login_ip'   => get_client_ip(0, true),
                ];
                $userQuery->where('id', $result["id"])->update($data);
                return 0;
            }
            return 1;
        }
        $hookParam =[
            'user'=>$user,
            'compare_password_result'=>false
        ];
        hook_one("user_login_start",$hookParam);
        return 2;
    }

    /*
    * 省代理商添加,1为手机号,2为邮箱
    * */
    public function createProvince($user,$type){
        if($user['password']!=$user['passwords']){
            return 5;
        }
        $userQuery = Db::name("user");
        //$province  = Db::name('province')->where(['ptype'=>1,'id'=>$user['pid']])->count();
        //查本省是否存在代理商
        $email    = $userQuery->where('user_email', $user['user'])->find();//邮箱唯一
        $mobile   = $userQuery->where('mobile',$user['user'])->find();     //手机号唯一
        //if($province!=0){
        //    return 3;
        //}
        if($email!=0 || $mobile!=0){
            return 2;//手机|邮箱已被注册,保证用户唯一
        }
            $type==1?
            $data   = [
               // 'user_login'      => $user['nc'],
                'mobile'=>$user['user'],
                'user_nickname'   => $user['nc'],
                'user_pass'       => cmf_password($user['password']),
                'last_login_ip'   => get_client_ip(0, true),
                'create_time'     => time(),
					//strtotime($user['birthday']),
                "user_type"       => 2,//省代理商,
                'pid'             => $user['pid'],
				'sex'			  => intval($user['sex']),
            ]:
            $data   = [
               // 'user_login'      => $user['nc'],
                'user_email'=>$user['user'],
                'user_nickname'   => $user['nc'],
                'user_pass'       => cmf_password($user['password']),
                'last_login_ip'   => get_client_ip(0, true),
                'create_time'     => time(),
					//strtotime($user['birthday']),
                "user_type"       => 2,//省代理商,
				'sex'			  => intval($user['sex']),
                'pid'             => $user['pid'],
            ];
        $int  = $this->save($data);
        //$intS = Db::name('province')->where('id',$user['pid'])->update(['ptype'=>1]);//修改本省为存在代理商状态&& $intS
        if($int ){
            return 1;
        }else{
            return 4;
        }
    }

    public function registerEmail($user)
    {
        $userQuery = Db::name("user");
        $result    = $userQuery->where('user_email', $user['user_email'])->find();

        $userStatus = 1;

        if (cmf_is_open_registration()) {
            $userStatus = 2;
        }

        if (empty($result)) {
            $num = $this->getNum($user['pid']);
            switch($num){
                case 0:
                    $lev = 1;
                    $user_num = 1;
                    $res = $this->where([
                        'pid'=>$user['pid'],
                        'user_type'=>2,
                    ])->find();
                    @$fid = $res['id'];
                    if(!$res){
                        //$fid=0;    //若未设置省代理,则走此步,但是后期设置上省代理后,又要更新本省这些没有fid的人
						return 3;
                    }
                    break;
                case 1:
                    $lev = 1;
                    $user_num = 2;
                    $res = $this->where([
                        'pid'=>$user['pid'],
                        'user_type'=>2,
                    ])->find();
                    @$fid = $res['id'];
                    if(!$res){
                        //$fid=0;    //若未设置省代理,则走此步,但是后期设置上省代理后,又要更新本省这些没有fid的人
						return 3;
                    }
                    break;
                default:
                    $num = $num+2;
                    $lev = $this->getLevel($num);
                    $order = $this->getOrder($num,$lev);
                    $user_num = $order[1];
                    /*if($order[0]=='A'){
                        $fOrder = UserModel::A($order);
                        $fLev = $fOrder[3];
                        $fUser_num = $fOrder[1];
                    }else{
                        $fOrder = UserModel::B($order);
                        $fLev = $fOrder[2];
                        $fUser_num = $fOrder[1];
                    }
                    $res = $this->where([
                        'user_level'=>$fLev,
                        'user_num'=>$fUser_num,
                        'pid'=>$user['pid'],
                    ])->find();
                    $fid = $res['id'];*/
                    $fid = $this->getFid($order,$user['pid']);
                    break;
            }
            $data   = [
                'user_login'      => '',
                'user_email'      => $user['user_email'],
                'mobile'          => '',
                'user_nickname'   => '',
                'user_pass'       => cmf_password($user['user_pass']),
                'last_login_ip'   => get_client_ip(0, true),
                'create_time'     => time(),
                'last_login_time' => time(),
                'user_status'     => $userStatus,
                "user_type"       => 5,//普通会员,
                'user_level'      => $lev,
                'user_num'        => $user_num,
                'pid'             => $user['pid'],
                'fid'             => $fid,
            ];
            $userId = $userQuery->insertGetId($data);
            $date   = $userQuery->where('id', $userId)->find();
            cmf_update_current_user($date);
            return 0;
        }
        return 1;
    }

    public function registerMobile($user)
    {
        $result = Db::name("user")->where('mobile', $user['mobile'])->find();

        $userStatus = 1;

        if (cmf_is_open_registration()) {
            $userStatus = 2;
        }
        
		if (empty($result)) {
			/* 注册
            $num = $this->getNum($user['pid']);
            switch($num){
                case 0:
                    $lev = 1;
                    $user_num = 1;
                    $res = $this->where([
                        'pid'=>$user['pid'],
                        'user_type'=>2,
                    ])->find();
                    @$fid = $res['id'];
                    if(!$res){
                     return 3;
                    }
                    break;
                case 1:
                    $lev = 1;
                    $user_num = 2;
                    $res = $this->where([
                        'pid'=>$user['pid'],
                        'user_type'=>2,
                    ])->find();
                    @$fid = $res['id'];
                    if(!$res){
                        return 3;
                    }
                    break;
                default:
                    $num = $num+2;
                    $lev = $this->getLevel($num);
                    $order = $this->getOrder($num,$lev);
                    $user_num = $order[1];
                    $fid = $this->getFid($order,$user['pid']);

                    break; */  //这边是注册
                    /*$res = $this->where([
                        'user_level'=>$fOrder[2],
                        'user_num'=>$fOrder[1],
                        //'user_pid'=>$省份
                    ])->find();*/
                    //$fid = '我的上层是第'.$fOrder[2].'层,第'.$fOrder[1].'个';
            //   注册结束}
            /*$num = $this->getNum();
            $lev = $this->getLevel($num);
            $order = $this->getOrder($num,$lev);
            if($order[0]=='A'){
                //echo '<br />我是得到上层A侧的关系:';
                $fOrder = $this->A($order);
                //echo '我的上层是第'.$fOrder[3].'层,第'.$fOrder[1].'个';
                $res = $this->where([
                    'user_level'=>$fOrder[3],
                    'user_num'=>$fOrder[1],
                    //'user_pid'=>$省份
                ])->find();
                $fid = $res['id'];
            }else{
                $fOrder = $this->B($order);
                //echo '我的上层是第'.$fOrder[2].'层,第'.$fOrder[1].'个';

                $fid = $res['id'];
            }*/
            $data   = [
               // 'user_login'      => '',
                'user_email'      => '',
                'mobile'          => $user['mobile'],
                'sex'             => $user['sex'],
                'user_nickname'   => $user['user_nickname'],
                //'birthday'        => $user['birthday'],
                'user_pass'       => cmf_password($user['user_pass']),
                'last_login_ip'   => get_client_ip(0, true),
                'create_time'     => time(),
                'last_login_time' => time(),
                'user_status'     => $userStatus,
                "user_type"       => 5,//普通会员,
                //'user_level'      => $lev,
                //'user_num'        => $user_num,
                "pid"			  =>$user['pid'], //地区id
                "fid"			  => $fid=$user['fid'],//用户的父id
            ];
            $userId = Db::name("user")->insertGetId($data);
            $data   = Db::name("user")->where('id', $userId)->find();
            cmf_update_current_user($data);
            return 0;
        }
		
        return 1;
    }

    /**
     * 通过邮箱重置密码
     * @param $email
     * @param $password
     * @return int
     */
    public function emailPasswordReset($email, $password)
    {
        $result = $this->where('user_email', $email)->find();
        if (!empty($result)) {
            $data = [
                'user_pass' => cmf_password($password),
            ];
            $this->where('user_email', $email)->update($data);
            return 0;
        }
        return 1;
    }

    /**
     * 通过手机重置密码
     * @param $mobile
     * @param $password
     * @return int
     */
    public function mobilePasswordReset($mobile, $password)
    {
        $userQuery = Db::name("user");
        $result    = $userQuery->where('mobile', $mobile)->find();
        if (!empty($result)) {
            $data = [
                'user_pass' => cmf_password($password),
            ];
            $userQuery->where('mobile', $mobile)->update($data);
            return 0;
        }
        return 1;
    }

    public function editData($user)
    {
        $userId           = cmf_get_current_user_id();
        $data['user_nickname'] = $user['user_nickname'];
        $data['sex'] = $user['sex'];
        //$data['birthday'] = strtotime($user['birthday']);
        $data['user_url'] = $user['user_url'];
        $data['signature'] = $user['signature'];
        $userQuery        = Db::name("user");
        if ($userQuery->where('id', $userId)->update($data)) {
            $userInfo = $userQuery->where('id', $userId)->find();
            cmf_update_current_user($userInfo);
            return 1;
        }
        return 0;
    }

    /**
     * 用户密码修改
     * @param $user
     * @return int
     */
    public function editPassword($user)
    {
        $userId    = cmf_get_current_user_id();
        $userQuery = Db::name("user");
        if ($user['password'] != $user['repassword']) {
            return 1;
        }
        $pass = $userQuery->where('id', $userId)->find();
        if (!cmf_compare_password($user['old_password'], $pass['user_pass'])) {
            return 2;
        }
        $data['user_pass'] = cmf_password($user['password']);
        $userQuery->where('id', $userId)->update($data);
        return 0;
    }

    public function comments()
    {
        $userId               = cmf_get_current_user_id();
        $userQuery            = Db::name("Comment");
        $where['user_id']     = $userId;
        $where['delete_time'] = 0;
        $favorites            = $userQuery->where($where)->order('id desc')->paginate(10);
        $data['page']         = $favorites->render();
        $data['lists']        = $favorites->items();
        return $data;
    }

    public function deleteComment($id)
    {
        $userId              = cmf_get_current_user_id();
        $userQuery           = Db::name("Comment");
        $where['id']         = $id;
        $where['user_id']    = $userId;
        $data['delete_time'] = time();
        $userQuery->where($where)->update($data);
        return $data;
    }

    /**
     * 绑定用户手机号
     */
    public function bindingMobile($user)
    {
        $userId      = cmf_get_current_user_id();
        $data ['mobile'] = $user['username'];
        Db::name("user")->where('id', $userId)->update($data);
        $userInfo = Db::name("user")->where('id', $userId)->find();
        cmf_update_current_user($userInfo);
        return 0;
    }

    /**
     * 绑定用户邮箱
     */
    public function bindingEmail($user)
    {
        $userId     = cmf_get_current_user_id();
        $data ['user_email'] = $user['username'];
        Db::name("user")->where('id', $userId)->update($data);
        $userInfo = Db::name("user")->where('id', $userId)->find();
        cmf_update_current_user($userInfo);
        return 0;
    }

    /*
     * 根据当前选择省份,查询当前省份下共有多少人
     * */
    public function getNum($pid){
        $num = $this->where([
            'user_type'=>['in',[3,4,5]],
            'pid'=>$pid
        ])->count();
        return $num;
    }

    /*
     * 根据传入顺序,判断该用户是属于第几层
     * $num 用户顺序
     * $lev 用户层数
     * */
    public function getLevel($num){
        $lev = 1;
        while($lev){
            if(pow(2,$lev)<=$num && $num<pow(2,$lev+1)){
                return $lev;
            }
            $lev++;
        }
    }

    /*
     * 根据用户顺序与层数,判断该用户属于本层第几个
     * $num 用户顺序
     * $lev 用户层数
     * $ord 用户位于本层中第几个
     * 返回array为本用户位于本层中AB,AB侧第几个,本层中第几个
     * */
    public function getOrder($num,$lev){
        $ord = $num - pow(2,$lev)+1;
        if($ord <= pow(2,$lev)/2){
            return array('A',$ord,$num,$lev);
        }else{
            return array('B',$ord,$ord-pow(2,$lev)/2,$lev,$num);
        }
    }

    /*
     * A侧
     * $fNum  上层数字
     * $fHome 上层数字位于上层第几个
     * $fLev  上层数字的层数
     * */
    public function A($order){
        if($order[1]%2!=0){
            //echo '我位于本层中的奇数侧';
            //A侧奇数情况
            $fNum = $order[2]/2;            //得到上层的数字
            $lev = $this->getLevel($fNum);         //得到上层数字的层数
            $order = $this->getOrder($fNum,$lev);  //得到上层数字位于AB侧,AB侧位置,上层数字
            return $order;
        }else{
            //echo '我位于本层中的偶数侧';
            $j=2;
            $fHome='';
            for($i=pow(2,$order[3]-1)/2-1;$i>=0;$i--){
                if($j==$order[1]){
                    $fHome = $order[1]+$i;
                }
                $j+=2;
                continue;
            }
            $fLev = $order[3]-1;
            return array('A',$fHome,'',$fLev);
            //A侧偶数情况
        }
    }

    /*
     * B侧
     * */

    public function B($order){
        if ($order[2]%2!=0) {
            //echo '我是奇数列';
            //1,3,5,7,9
            $j=0;
            for ($i=1;$i<=$order[1]-pow(2,$order[3])/2;$i+=2) {
                if ($i==$order[1]-pow(2,$order[3])/2){
                   // echo '父亲位置' .
                    $fHome = $order[1]-pow(2,$order[3])/2-$j;
                    //本层位置((本层位置-本层2的n次方/2))-$j=上层位置;
                    return array('B', $fHome, $order[3]-1);
                }
                $j++;
            }
        } else {
            $num = $order[4];
            //本层数字
            $fNum = ceil($num / 2) - 1;
            //父级数字
            $fLev = $order[3] - 1;
            //父级层数
            $fOrder = $this->getOrder($fNum, $fLev);
            $fOrder = array($fOrder[0], $fOrder[1], $fOrder[3], $fOrder[4]);
            //AB侧,第几个,第几层,数字是几
            return $fOrder;
        }
    }
    public function getFid($order,$pid){
        if($order[0]=='A'){
            $fOrder = UserModel::A($order);
            $fLev = $fOrder[3];
            $fUser_num = $fOrder[1];
        }else{
            $fOrder = UserModel::B($order);
            $fLev = $fOrder[2];
            $fUser_num = $fOrder[1];
        }
        $res = $this->where([
            'user_level'=>$fLev,
            'user_num'=>$fUser_num,
            'pid'=>$pid,
        ])->find();
        $fid = $res['id'];
        return $fid;
    }
    public function province(){
        return $this->hasOne('ProvinceModel','id','pid',[],'left');
    }
	public function getMoneyDetail(){
		return $this->hasMany('UserMoneyDetailModel','uid','id',[],'left');
	}
	/*
	得到上级下的ab一侧用户
	*/
	public function getAB($fid,$pid,$address){
		//echo 'id is '.$fid;
		$res = $this->where([
			'fid'=>$fid,'pid'=>$pid	
		])->field('id,user_level,user_num')->select()->toArray();	
		if(!empty($res)){
			$sons=[];
			foreach($res as $vs){
				if($address=='a'){
					if($vs['user_num']<=pow(2,$vs['user_level'])/2){
						//echo '我是a侧';
						//echo $vs['id'];
						$sons[]=$vs['id'];
					}
				}else if($address=='b'){
					if($vs['user_num']>pow(2,$vs['user_level'])/2){
						//echo '我是b侧';
						//echo $vs['id'];
						$sons[]=$vs['id'];
					}
				}
			}
			//dump($sons);
			$sonsIds = $this->getSons($sons,$pid);
			$this->banArr();
			RETURN $sonsIds;
			//dump($sonsIds);
		}else{
			//echo '哈哈哈,无下级';
			return [];
		}
	}
	/*
	代理收益
	*/
	public function getSons($fid,$pid,$user_type=''){
		if($fid){
			$sonsId=[];
			//echo "<br />我是求下级用户,我的上级为".$fid[0].'我的省份是'.$pid."<br />";
			$sons = [];
			foreach($fid as $k){
				$sons[] = $this->where(['fid'=>$k,'pid'=>$pid])->field('id,user_level,user_num')->select()->toArray();
				//dump($sons);
			}
			if(!empty($sons)){
				//echo '发展了下级';
				$ids = [];
				foreach($sons as $k){
					if(array_key_exists('id', $k)){
						$ids[] = $k['id'];
						array_push(STATIC::$sonsIds,$k['id']);
					}else{
						foreach($k as $ks){
						$ids[] = $ks['id'];
						array_push(STATIC::$sonsIds,$ks['id']);
					}
					}
				}
				//dump($ids);
				//$num++;
				//dump(STATIC::$sonsIds);
				//dump($ids);
				//unset($sons);
				$user_type==4?STATIC::$num++:'';
				if(STATIC::$num==8){
					return STATIC::$sonsIds;
				}
				return $this->getSons($ids,$pid);
			}else{
				return STATIC::$sonsIds;
			}
		}else{
			
			return STATIC::$sonsIds;	
		}
		
	}
	
	/*
	递归后再次调用上个函数,数组值仍存留,需调用此方法清空静态数组
	*/
	public function banArr(){
		STATIC::$num=1;
		STATIC::$sonsIds=[];
	}
	
	/*
	根据传来的下级id,查每个下级的消费总和,将每个下级的消费总和拼合为数组
	代利润=所有下级的消费总和的数组*代理利润
	*/
	public function getThreeMoney($ids){
		//echo '得到代理利润';
		//dump($ids);
		$moneys = [];
		foreach($ids as $v){
			//1收益2提现3充值4消费
			$sonMoneySum = Db::name('user_money_detail')->where(['uid'=>$v,'type'=>4])->SUM('money');
			$moneys[]= $sonMoneySum;
		}
		//dump(array_sum($moneys)*-1);
		return array_sum($moneys)*-1;
	}

	/*
	计算省代理每个下级的消费
	*/
	public function getSsonSpend($ids,$pid){
		if(!empty($ids)){
			$moneys = [];
			foreach($ids as $k=>$v){
				$moneys[] = Db::name('user_money_detail')
					->where(['uid'=>$v['user_id'],'type'=>4])
					->SUM('money');		
			}
			$a = array_sum($moneys);
			//dump($moneys);
			//dump(array_sum($moneys));
			if($a!=0){
				return $a;
			}
		}
	}
}
