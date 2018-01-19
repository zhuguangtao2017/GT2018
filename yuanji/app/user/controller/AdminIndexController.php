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
/** 
* 
*----------Dragon be here!----------/ 
* 　　 ┏┓　 ┏┓ 
* 　　┏┛┻━━━┛┻┓
* 　　┃　　　 ┃ 
* 　　┃ ━  ━  ┃ 
* 　　┃┳┛　┗┳ ┃ 
* 　　┃　　　 ┃ 
* 　　┃  ┻    ┃ 
* 　　┃　　   ┃ 
* 　　┗━┓　　┏┛ 
* 　　　┃　　┃神兽保佑 
* 　　　┃　　┃代码无BUG！ 
* 　　　┃　　┗━━━┓ 
* 　　　┃　　　　┣┓ 
* 　　　┃　　　 ┏┛ 
* 　　　┗┓┓┏━┳┓┏┛ 
* 　　　 ┃┫┫ ┃┫┫ 
* 　　　 ┗┻┛ ┗┻┛ 
* ━━━━━━神兽出没━━━━━━by:ZJH
*/  
namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;
use app\user\model\UserModel;
/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class AdminIndexController extends AdminBaseController
{

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $where   = [];
        $request = input('param.');
		//dump($request);
        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
		$type=[];
		if (!empty($request['type'])){
			$type['user_type']=['=',input('type/d')];
		}
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['user_login|user_nickname|user_email']    = ['like', "%$keyword%"];
        }
		if(input('time_type')==2){
			if(!empty($request['stime'])){
				$keywordComplex['create_time']=['>=',strtotime($request['stime'])];
			}
			if(!empty($request['etime'])){
				$keywordComplex['create_time']=['<=',strtotime($request['etime'])+60*60*24];
			}
			if(!empty($request['stime'])&&!empty($request['etime'])){
				$keywordComplex['create_time']=[
					['>=',strtotime($request['stime'])],
					['<=',strtotime($request['etime'])+60*60*24],
					'and'
				];
			}
		}
        $usersQuery = new UserModel();
		//dump($where);
        $list = $usersQuery
            ->whereOr($keywordComplex)
            ->where($where)
			->where($type)
            ->where('user_type','>',2)  //查询user_type>2
            //->join('cmf_province','cmf_user.pid = cmf_province.id','left')
            ->with('province')
			//->with('getMoneyDetail')
			//->join('cmf_user_money_detail','cmf_user_money_detail.uid=cmf_user.id')
            ->order("create_time DESC")
            ->paginate(30,false,['query'=>request()->param()]);
        // 获取分页显示
		//echo Db::getLastSql();
        $page = $list->render();
		$lists = [];
        foreach ($list as $v){
            $f = Db::name('user')->field('user_nickname')->where([
                'id' => $v['fid'],
                'pid'=> $v['pid']
            ])->find();
			$money = Db::name('user_money_detail')->field('money,type,pan,allow')->where([
                'uid' => $v['id'],
            ])->select();
            $v['fName'] = $f['user_nickname']?$f['user_nickname']:"";
			$v['money'] = $money?$money:'';
			$v['daili'] = 0;
			$v = $v->toArray();
			$money_shouyi= 0;
			$money_sum   = 0;
			$money_fenHong = 0;
			$jf = 0;
			$sev = 0;
			if(intval($v['user_type'])==3){		//代理
				//求代理收益
				$user = new UserModel();
				$money='';
				$arr = [$v['id']];
				$sonsId = $user->getSons($arr,$v['pid']);
				$user->banArr();
				//dump($sonsId);
				if($sonsId){
					$sonsMoneySum = $user->getThreeMoney($sonsId);
					//dump($sonsMoneySum);
					$user->banArr();
					//下级消费总和*利润;
					$a = Db::name('dali')->field('daili')->find();
					$money = $sonsMoneySum*$a['daili']/100;
					//echo '这是所有下级的消费总合'.$money;
				}
				$money?$v['daili']=$money:$v['daili']=0;
				
			}
			if($v['money']){  //自身收益(代理或会员)
				foreach($v['money'] as $k=>$vs){
					//1收益2提现3充值4消费		
					if($vs['type']==1){		//我是用户收益
						$money_shouyi = $money_shouyi+$vs['money'];
					}
					if($vs['pan']!=1){		//我是余额(不算消费券)
						if($vs['type']!=2){	//不是提现金额
							if($vs['type']!=5){ //不算消费券
								$money_sum = $money_sum+$vs['money'];
							}
						}else{				//提现金额,此时只算allow 1类型的
							if($vs['allow']==1){	//1为通过,只有通过才算
								$money_sum = $money_sum+$vs['money'];
							}
						}
					}
					if($vs['type']==5){		//我是用户分红
						$money_fenHong +=$vs['money'];
					}
					if($vs['type']==6){		//我是积分返现
						$jf +=$vs['money'];
					}
					if($vs['type']==7){		//我是手续费
						$sev +=$vs['money'];
					}
					$vs['jf'] = $jf;
					$vs['sev'] = $sev;
					$vs['money_shouyi'] = $money_shouyi;
					$vs['money_sum'] = $money_sum;
					$vs['money_fenHong'] = $money_fenHong;
					$v['moneys'] = $vs;
				}
			}else{
				$v['money']='';
			}
			$lists[]=$v;
        }
		//查询权限
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();
		
		//dump($lists);
		//die;	
        $this->assign('list', $lists);
        $this->assign('page', $page);
		$this->assign("aqx",$ars["role_id"]);
		
       // 渲染模板输出
        return $this->fetch();
    }

     //后台用户充值
	public function MoneyCharge()
	{
		 $aaid=cmf_get_current_admin_id();
		 $ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();
		 if($ars["role_id"]==6)
		{
			 die("错误");
		 }

		if ($this->request->isPost()) {
			$data = $this->request->post();
			$money=$data['money'];
			$uid=$data['id'];
	 
	 
			$fspan=5;//管理员充值
			$user = ['uid'=>$uid,'money'=>$money,'from'=>'','time'=>time(),'type'=>'3','fs'=>'管理员充值','fspan'=>3];
			$aa=Db::name('user_money_detail')->insert($user);
			if($aa){$this->success('充值'.$money,'');}
			}else{
				$id = input('id/d');
				$rs = Db::name('user')->where('id','=',$id)->find();
				return $this->fetch('moneycharge',['rs'=>$rs]);
			}
	}
    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = input('param.id', 0, 'intval');
		$res = Db::name('user')->where('id',$id)->find();
        if($res['user_type']<4){
            if($res['user_type']!=2){
				$this->error('请先取消其身份再处理');
			}
        }
        if ($id) {
            $result = Db::name("user")->where('id',$id)->where('user_type','>=',2)->setField('user_status', 0);
            if ($result) {
                $this->success("会员拉黑成功！",'');
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = intval(input('param.id'));
        if ($id) {
            Db::name("user")->where('id',$id)->update(['user_status'=>1]);
            $this->success("会员启用成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }

	
    /*
     * 设为代理
     * */
    public function offDaiLi(){
        $id = input('id/d');
        if($id){
            $user = new UserModel();
            $user->save(
                ['user_type'=>3],
                ['id'=>$id]
            );
            $this->success('成功！');
        }
    }

    /*
     * 取消代理
     * */
    public function noDaiLi(){
        $id = input('id/d');
        if($id){
            $user = new UserModel();
            $user->save(
                ['user_type'=>5],
                ['id'=>$id]
            );
            $this->success('成功！');
        }
    }

	/*
	用户信息修改
	*/
	public function exits(){
		$param = input('param.');
		if(Request::instance()->isPost()){
			$user = new UserModel();
			$validate = new Validate([
				"user_nickname"  => 'require|min:6',
				'sex' => 'require',
				'user_pass' => 'require|min:6|max:16',
				'user_passs' => 'require|min:6|max:16',
			]);
			$validate->message([
				"user_nickname.require" => '用户名不能为空',
				"user_nickname.min" => '用户名不能小于6位',
				'sex.require' => '性别不能为空',
				'user_pass.require' => '原密码不能为空',
				'user_passs.require' => '新密码不能为空',
				'user_passs.max'     => '新密码不能超过32个字符',
				'user_passs.min'     => '新密码不能小于6个字符',
			]);
			$data = $this->request->post();
			if (!$validate->check($data)) {
				$this->error($validate->getError());
			}else{
				$userModel = new UserModel();
				$arr=array(
					'user_nickname' => $param['user_nickname'],
					'sex' => $param['sex'],
					'user_pass' => cmf_password($param['user_passs'])
				);
				if($user->save($arr,['id' => $param['hid']])){
					echo $this->success('修改成功');
				}else{
					echo $this->error('修改失败或未作修改');
				}
				/*if(!empty($param['post']['mobile'])&&!empty($param['post']['user_email'])){
					$arr['mobile'] = $param['post']['mobile'];
					$arr['user_email'] = $param['post']['user_email'];
				}
				else if(!empty($param['post']['user_email'])){
					$arr['user_email'] = $param['post']['user_email'];
				}
				else if(!empty($param['post']['mobile'])){
					$arr['mobile'] = $param['post']['mobile'];
				}*/
			}
		}else{
			$id = $param['id'];
			$data = Db::name('user')->where(['id'=>$id])->find();
			return $this->fetch('admin_index/exits',['data'=>$data]);
		}
	}

	/*
	验证用户密码
	*/
	public function validatePass(){
		$id = input('param.hid');
		$pass = input('param.pass');
		$data = Db::name('user')->where(['id'=>$id])->find();
		if(cmf_compare_password($pass, $data['user_pass'])){
			echo '密码一样';
		}else{
			echo '密码不一样';
		}
	}
	public function spend(){
		$user = new UserModel();
		$request = input('param.');
		$user_type='';
		if(!empty($request['user_type'])){
			switch($request['user_type']){
				case '3': 
					$user_type='代理';
					$ids = $user->getSons([$request['id']],$request['pid']);
				break;
				case '4': 
					$user_type='会员';
					$ids = $user->getSons([$request['id']],$request['pid'],$request['user_type']);
				break;
			}
		}
		
		$user->banArr();
		//dump($ids);
		$idss = $ids;
		array_push($ids,$request['id']);
		$userOrder = [];
		$province = Db::name('province')->select()->toArray();
		$page = '';
		
		
		//echo $user_type;
		if(!empty($ids)){
			$where=[];
			if(!empty($request['stime'])){
				$where['p.time']=['>=',strtotime($request['stime'])];
			}
			if(!empty($request['etime'])){
				$where['p.time']=['<=',strtotime($request['etime'])+3600*24];
			}
			if(!empty($request['stime'])&&!empty($request['etime'])){
				$where['p.time']=[
					['>=',strtotime($request['stime'])],
					['<=',strtotime($request['etime'])+3600*24],
					'and'
				];
			}
			//求每个下级的购物,跳转到订单页面
			$a = Db::name('pay')->alias('p')
				->field('p.*,u.user_nickname,u.user_email,u.mobile,post.post_source,a.name,a.address as addresss,a.tel as atel,f.fs')
				->join('cmf_user u','u.id=p.uid','left')
				->join('cmf_portal_post post','post.id=p.pid','left')
				->join('cmf_address a','a.id=cmf_pay.address_id','left')
				->join('cmf_user_money_detail f','f.id=cmf_pay.float_id','left')
				->where('p.uid','in',$idss)
				->where($where)
				->order('p.time DESC')
				->select();
			$money = 0;
			foreach($a as $v){
				$money=$money+$v["post_source"];
			}
			$userOrder = Db::name('pay')->alias('p')
				->field('p.*,u.user_nickname,u.user_email,u.mobile,post.post_source,a.name,a.address as addresss,a.tel as atel,f.fs')
				->join('cmf_user u','u.id=p.uid','left')
				->join('cmf_portal_post post','post.id=p.pid','left')
				->join('cmf_address a','a.id=cmf_pay.address_id','left')
				->join('cmf_user_money_detail f','f.id=cmf_pay.float_id','left')
				->where('p.uid','in',$ids)
				->where($where)
				->order('p.time DESC')
				->paginate(30,false,['query'=>request()->param()]);
			//echo Db::getLastSql();
			$province = Db::name('province')->select()->toArray();
			$zong_money = 0;
			$page_money_sum=0;
			foreach($userOrder as $vs){
				$zong_money += $vs['money'];
				$page_money_sum=$page_money_sum+$vs['post_source'];
			}
			//dump($userOrder);
			$page = $userOrder->render();
			//array_push($ids,$request['id']);
			//查询权限
			$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();

			if($user_type=='代理'){
				$data=[
				'userOrder'=>$userOrder,'province'=>$province,'order'=>'正序排列','page'=>$page,'ids'=>implode(',',$idss),'money_sums'=>$money,
				'user_type'=>$user_type,'sNum'=>count($idss),'gouq'=>$zong_money,
				'uname'=>$request['uname'],'aqx'=>$ars['role_id']
				];
			}else if($user_type=='会员'){
				$data=[
				'userOrder'=>$userOrder,'province'=>$province,'order'=>'正序排列','page'=>$page,'ids'=>implode(',',$idss),'money_sumss'=>$money,
				'user_type'=>$user_type,'sNum'=>count($idss),'gouq'=>$zong_money,
				'uname'=>$request['uname'],'aqx'=>$ars['role_id']
				//'page_money_sums'=>$page_money_sum
				];
			}
			return $this->fetch('user_order/index',$data);
		}else{
			//查询权限
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();

			return $this->fetch('user_order/index',[
				'userOrder'=>$userOrder,'province'=>$province,
				'order'=>'正序排列','page'=>$page,'ids'=>'',
				'money_sums'=>0,'sNum'=>0,'user_type'=>'',
				'uname'=>$request['uname'],'aqx'=>$ars['role_id']
			//'page_money_sums'=>$page_money_sum
			]);
		}
	}
	public function profit(){
		$request = input('param.');
		$id  = $request['id'];
		$pid = $request['pid'];
		//dump($request);
		$user = new UserModel();
		if($request['user_type']==4){	//会员,只查9级
			$ids = $user->getSons([$id],$pid,$request['user_type']);
		}else if($request['user_type']<4){	//代理,省代
			$ids = $user->getSons([$id],$pid);
		}
		$user->banArr();
		$where=[];
		if(!empty($request['stime'])){
			$where['d.time']=['>=',strtotime($request['stime'])];
		}
		if(!empty($request['etime'])){
			$where['d.time']=['<=',strtotime($request['etime'])+3600*24];
		}
		if(!empty($request['stime'])&&!empty($request['etime'])){
			$where['d.time']=[
				['>=',strtotime($request['stime'])],
				['<=',strtotime($request['etime'])+3600*24],
				'and'
			];
		}
		$idss = $ids;
		//dump($idss);
		array_push($ids,$id);
		$detail = Db::name('user_money_detail')
			->alias('d')
			->field('d.*,u.id as user_id,u.user_nickname,u.mobile,u.user_email,u.user_type')
			->join('cmf_user u','u.id=d.uid')
			->where($where)
			->where('d.uid','in',$ids)
            ->order("d.time DESC")
			->paginate(30,false,['query'=>request()->param()]);
		//echo Db::getLastSql();
		$page = $detail->render();
		$details = [];
		foreach($detail as $k=>$v){	
			if($v['uid']==$id&&$v['type']==1 || in_array($v['uid'],$idss)&&$v['type']==4 ){
				$info = Db::name('user')
				->where('id',$v['from'])
				->field('user_nickname,user_email,mobile')
				->find();
				$details[$k]=$v;
				$details[$k]['froms']=$info;
			}
		}
		$type_arr=[];
		foreach($details as $v){
			//1收益  2提现  3充值，4 消费 5 分红
			//echo '<pre>';
			
			switch($v['type']){
				case '1':
					@$type_arr['sy'][]+=$v['money'];
					break;
				case '2':
					@$type_arr['tx'][]+=$v['money'];
					break;
				case '3':
					@$type_arr['cz'][]+=$v['money'];
					break;
				case '4':
					if($v['pan']==0){	//余额支付
						@$type_arr['xf_nei'][]+=$v['money'];
					}else{				//外部资金支付
						@$type_arr['xf_wai'][]+=$v['money'];
					}
					break;
				case '5':
					@$type_arr['fh'][]+=$v['money'];
					break;
			}
		}
		//dump($details);
		//die;
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();
		return $this->fetch('user_money_float/index',[
			'detail'=>$details,'page'=>$page,
			'ids'=>implode(',',$idss),'this_id'=>$id,
			'type_arr'=>$type_arr,'aqx'=>$ars['role_id']
			]);
	}
}
