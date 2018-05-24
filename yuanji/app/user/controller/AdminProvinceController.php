<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9
 * Time: 18:50
 */
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
use app\user\model\UserModel;			//用户模型
use app\user\models\ProvinceModel;		//引用地区模型
use app\user\model\UserPayModel;		//用户...
use think\Db;
use think\Request;
use think\Validate;
use cmf\controller\AdminBaseController;
class AdminProvinceController extends AdminBaseController{
    public function index()
    {
        $where   = [];
        $request = input('request.');

        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $keywordComplex['u.mobile|u.user_nickname|u.user_email']    = ['like', "%$keyword%"];
        }
        $usersQuery = Db::name('user');
        //$province = Db::name('province')->select()->toArray();                               //所有省份
        $tregion = Db::name('province')->where('ptype',0)->select();            //未添加省代的省份
        $list = $usersQuery->whereOr($keywordComplex)->where($where)->where('user_type',2)
			->alias('u')
			->field('u.*,p.province,p.id as province_id')
			->join('cmf_province p','p.id=u.pid')
			->order("create_time DESC")
			->paginate(30,false,['query'=>request()->param()]);
		// 获取分页显示
        $page = $list->render();
		$lists = [];
		foreach($list as $v){
			//1收益2提现3充值4消费
			$info = Db::name('user_money_detail')
				->where(['uid'=>$v['id'],'type'=>1])
				->SUM('money');
			$v['money'] = $info;
			//普通收益
			$fenHong = Db::name('user_money_detail')
				->where(['uid'=>$v['id'],'type'=>5])
				->SUM('money');
			//分红
			$v['money_fenHong'] = $fenHong;
			$user = new UserModel();
			//$sonIds = $user->where(['user_type'=>['>',2],'id'=>['<>',$v['id']],'pid'=>$v['pid']])->field('cmf_user.id as user_id')->select()->toArray();
			$sonIds = $user->where(['id'=>['<>',$v['id']],'pid'=>$v['pid']])->field('cmf_user.id as user_id')->select()->toArray();
			//都是省代,直接查省代下面所有用户
			$provinceSonsSpend = $user->getSsonSpend($sonIds,$v['pid']);
			//echo '该省消费总和'.$provinceSonsSpend;
			//$provinceSonsSpend 每个省代的下级所有消费SUM();
			$a = Db::name('dali')->field('sdai')->find();
			$sMoney = ($provinceSonsSpend*-1)*(intval($a['sdai'])/100);	//省代收益
			$v['s_money'] = $sMoney;
			$y_money = Db::name('user_money_detail')
				->where(['uid'=>$v['id'],'type'=>['in',[3,4]],'pan'=>['<>',1]])
			->SUM('money');		
			$tx_money = Db::name('user_money_detail')		//只求提现,提现为内部资金pan<>1并且为通过的allow=1
				->where(['uid'=>$v['id'],'type'=>['in',[2]],'pan'=>['<>',1],'allow'=>['=',1]])
			->SUM('money');
			$jf = Db::name('user_money_detail')		//只求积分
				->where(['uid'=>$v['id'],'type'=>['=',6]])
			->SUM('money');
			$sev = Db::name('user_money_detail')		//只求手续费
				->where(['uid'=>$v['id'],'type'=>['=',7]])
			->SUM('money');
			$v['jf'] = $jf;
			$v['sev'] = $sev;
			$v['y_money'] = $y_money+$tx_money+$sMoney+$info+$sev;
			//echo '省代自身收益'.$info;
			//echo '省代收益'.$sMoney;
			$lists[] = $v;
		}
		//dump($lists);
		//die;
        //$this->assign('province',$province);
		//查询权限
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();
		
        $this->assign('tregion',$tregion);
        $this->assign('list', $lists);
        $this->assign('page', $page);
		$this->assign("aqx",$ars["role_id"]);
		
        // 渲染模板输出
        return $this->fetch('/admin_province/index');
    }

    /*
     * 添加省代理
     * */
    public function create(){
        if(Request::instance()->isPost()){
            $rules = [
                'user'     => 'require',
                'password' => 'require|min:6|max:16',
                'passwords'=> 'require|min:6|max:32',
                'nc'       => 'require|min:2|max:20',
                'sex'      => 'require',
               // 'birthday' => 'require',
                'pid'      => 'require',
            ];
            $field = [
                'user'      => '手机或邮箱',
                'password'  => '密码',
                'passwords' => '重复密码',
                'nc'        => '昵称',
                'sex'       => '性别',
                //'birthday'  => '生日',
                'pid'       => '省份'
            ];
            $validate = new Validate($rules,[],$field);
            $validate->message([
                'user.require'     => '手机或邮箱不能为空',
                'password.require' => '密码不能为空',
                'password.max'     => '密码不能超过20个字符',
                'password.min'     => '密码不能小于6个字符',
                'passwords.require'=> '请再次输入密码',
                'nc.require'  => '昵称不能为空',
                'sex.require'  => '性别不能为空',
                'pid.require'  => '请选择省份',
            ]);
            $post = Request::instance()->post();
            if(!$validate->check($post)){
                $this->error($validate->getError());
            }
            $user = new UserModel();
			//dump($post);
			//die;
            if(preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $post['user'])){
                $result = $user->createProvince($post,1);
            }else if (preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i',$post['user'])){
                $result = $user->createProvince($post,2);
            }else{
                $result = 6;
            }
            switch ($result){
                case 1:
                    $this->success('添加成功');
                    break;
                case 2:
                    $this->error('手机号或邮箱已存在');
                    break;
                //case 3:
                    //$this->error('该省已存在代理,请重新添加');
                    //break;
                case 5:
                    $this->error('两次密码不一致');
                    break;
                case 6:
                    $this->error('不是正确的手机号(邮箱)');
                    break;
                default:
                    //此情况一般设置为4
                    $this->error('添加失败,请重新添加');
                    break;
            }
        }else{
            //$sheng = Db::name('province')->where('ptype','<>',1)->select();
			$sheng = Db::name('province')->select();
            return $this->fetch('create',['sheng'=>$sheng]);
        }
    }
    /*
     * ajax 用户设为省代
     */
    public function ajax()
    {
        if(input('pid')&&input('id'))             //将用户设置为省代
        {
            $ar = Db::name('user')
                ->where('id',input('id'))
                ->update(['pid' => input('pid'),
                    'user_type' => 2,
                    'fid' => 0
                ]);
            Db::name('province')
                ->where('id',input('pid'))
                ->update(['ptype' => 1]);
        }
        if(input('qpid')&&input('qid'))           //修改省代时先取消省代身份 再重新赋予
        {
            $ar = Db::name('user')
                ->where('id',input('qid'))
                ->update(['pid' => null,
                    'user_type' => 2,
                    'fid' => 0
                ]);
            Db::name('province')
                ->where('id',input('qpid'))
                ->update(['ptype' => 0]);
        }
    }

	/*
	修改省代信息
	*/
	public function exits(){
		$id = input('param.id');
		$user = new UserModel();
		if(Request::instance()->isPost()){
			print_r($this->request->post());
		}else{
			$data = $user->get($id);
			return $this->fetch('/admin_index/exits',['data'=>$data]);
		}
	}
	/*
	省代消费明细
	*/
	public function spend($pid){
		//$request = input('param.');
		//dump($request);
		$pid = input('pid/d');
		$param = input('param.');
		$res = Db::name('user')->alias('u')->where(['user_type'=>['>',2],'pid'=>$pid])->field('id')->select()
			->toArray();
		$ids = [];
		foreach($res as $v){
			$ids[] = $v['id'];
		}
		//dump($ids);
		$idss = $ids;
		$province = Db::name('province')->select()->toArray();
		$where=[];
		if(!empty($param['stime'])){
			$where['cmf_pay.time']=['>=',strtotime($param['stime'])];
		}
		if(!empty($param['etime'])){
			$where['cmf_pay.time']=['<=',strtotime($param['etime'])+3600*24];
		}
		if(!empty($param['stime'])&&!empty($param['etime'])){
			$where['cmf_pay.time']=[
				['>=',strtotime($param['stime'])],
				['<=',strtotime($param['etime'])+3600*24],
				'and'
			];
		}
        $userPay = new UserPayModel();
       /*
	    $userOrder = Db::name('pay')
			->field('cmf_pay.*,u.user_nickname,u.mobile,u.user_email,p.post_source,p.post_title,a.name,a.tel,a.address as addresss,f.fs')
			->join('cmf_portal_post p','p.id=cmf_pay.pid','left')
			->join('cmf_user u','cmf_pay.uid=u.id','left')
			->join('cmf_address a','a.uid=cmf_pay.uid','left')
			->join('cmf_user_money_detail f','f.id=cmf_pay.float_id','left')
			->where('u.pid',$pid)
			->where($where)
			->order('cmf_pay.time DESC');
	   */
		$a = Db::name('pay')
			->field('cmf_pay.*,u.user_nickname,u.mobile,u.user_email,p.post_source,p.post_title,a.name,a.tel,a.address as addresss,f.fs')
			->join('cmf_portal_post p','p.id=cmf_pay.pid','left')
			->join('cmf_user u','cmf_pay.uid=u.id','left')
			->join('cmf_address a','a.id=cmf_pay.address_id','left')
			->join('cmf_user_money_detail f','f.id=cmf_pay.float_id','left')
			->where('u.pid',$pid)
			//->where('cmf_pay.uid','in',$ids)
			->where($where)
			->order('cmf_pay.time DESC')
			->select();
		$b = Db::name('pay')
			->field('cmf_pay.*,u.user_nickname,u.mobile,u.user_email,p.post_source,p.post_title,a.name,a.tel,a.address as addresss,f.fs')
			->join('cmf_portal_post p','p.id=cmf_pay.pid','left')
			->join('cmf_user u','cmf_pay.uid=u.id','left')
			->join('cmf_address a','a.id=cmf_pay.address_id','left')
			->join('cmf_user_money_detail f','f.id=cmf_pay.float_id','left')
			->where('u.pid',$pid)
			//->where('cmf_pay.uid','in',$ids)
			->where($where)
			->order('cmf_pay.time DESC')
			->paginate(30,false,['query'=>request()->param()]);
		$money=0;
		$zong_money = 0;
        foreach($a as $vs){
			$zong_money += $vs['money'];
			$money=$vs['post_source']+$money;
		}
		/*
		$page_money = 0;
		foreach($b as $vss){
			$page_money = $page_money + $vss['post_source'];
		}
		*/
		//echo Db::getLastSql();
        //dump($userOrder);
		$this->assign('order','正序排列');
		$page = $b->render();
			//查询权限
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();

		return $this->fetch('/user_order/index',[
			'userOrder'=>$b,'page'=>$page,'province'=>$province,
			'money_sum'=>$money,'gouq'=>$zong_money,
			'ids'=>implode(',',$ids),'sNum'=>count($ids),'user_type'=>'省代','aqx'=>$ars["role_id"]
			]);
	}

	/*
	省代收益明细
	*/
	public function profit(){
		$param = input('param.');
		$id = input('param.id/d');
		$pid = input('param.pid/d');
		$pids = Db::name('user')->where(['pid'=>['=',$pid],'user_type'=>['>',1],'id'=>['<>',$id]])
			->select();
		$aaa=[];
		//dump($pids);
		foreach($pids as $o){
			$aaa[]=$o['id'];
		}
		$xxx = $aaa;
		array_push($aaa,$id);
		$px = 'desc';
        if(input('px'))
        {
        	$px = input('px');
        }
		$where=[];
		if(!empty($param['stime'])){
			$where['d.time']=['>=',strtotime($param['stime'])];
		}
		if(!empty($param['etime'])){
			$where['d.time']=['<=',strtotime($param['etime'])+3600*24];
		}
		if(!empty($param['stime'])&&!empty($param['etime'])){
			$where['d.time']=[
				['>=',strtotime($param['stime'])],
				['<=',strtotime($param['etime'])+3600*24],
				'and'
			];
		}
		$a = $detail = Db::name('user_money_detail')->alias('d')
			->where([
				'uid'=>$id,'type'=>1
			])
			->join('cmf_user u','u.id=d.uid','left')
			->where($where)
			->order("time $px")
			->select();
		$detail = Db::name('user_money_detail')->alias('d')->where(['uid'=>['in',$aaa]])
			->field('d.*,u.id as user_id,u.user_nickname,u.mobile,u.user_email,u.user_type')
			->join('cmf_user u','u.id=d.uid','left')
			->where($where)
			->order("time $px")
			->paginate(30,false,['query'=>request()->param()]);
		//echo Db::getLastSql();
		$page = $detail->render();
		$province = Db::name('province')->select()->toArray();
		$money = 0;
		foreach($a as $vs){
			$money = $money+$vs['money'];
		}
		$details = [];
		$page_money_sum=0;
		foreach($detail as $k=>$v){
			if($v['uid']==$id&&$v['type']==1 || in_array($v['uid'],$xxx)&&$v['type']==4){
				$info = Db::name('user')
				->where('id',$v['from'])
				->field('user_nickname,user_email,mobile')
				->find();
				$details[$k]=$v;
				$details[$k]['froms']=$info;
				//$page_money_sum=$page_money_sum+$v['money'];
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
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();
		$this->assign('aqx',$ars['role_id']);
		return $this->fetch('user_money_float/index',[
			'detail'=>$details,'page'=>$page,
			'money_sum'=>$money,'pid'=>$pid,
			'ids'=>implode(',',$xxx),'this_id'=>$id,
			'type_arr'=>$type_arr,
			]);
	}
}
