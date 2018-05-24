<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10
 * Time: 18:56
 */
namespace app\user\controller;
use app\user\model\UserModel;
use app\user\model\UserPayModel;
use app\user\model\PortalPostModel;
use think\Db;
use cmf\controller\AdminBaseController;

class UserOrderController extends AdminBaseController{
    public function index(){
		$order=input('param.type');
		$where   = [];
        $request = input('param.');
		//dump($request);
		$address='';
		$address_ids='';
		$user = new UserModel();
        if (!empty($request['uid'])) {
            $where['cmf_pay.son'] = $request['uid'];	//这是商品编号
        }
		if(!empty(input('param.pid'))){
			$where['u.pid'] = input('param.pid/d');
		}
		$idWhere = [];
		if(!empty(input('param.id')) ){
			//只要是其他页面跳过来,这个id肯定有
			if(!empty(input('param.ids')) ){
				//echo '我有下级';
				@$ids = explode(',',input('param.ids'));
				array_push($ids,$request['id']);
				$idWhere['u.id'] = ['in',$ids];
			}else{
				//echo '我没有下级';
				$idWhere['u.id'] = ['=',input('param.id')];
			}
			$sNum = count(array_filter(explode(',',input('param.ids'))));
			//echo '人数is'.$sNum;
		}
		$keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['u.mobile|u.user_nickname|u.user_email'] = ['like', "%$keyword%"];
        }
		$keywords='';
		if (!empty($request['name'])) {
            $keyword = $request['name'];
            $keywordComplex['u.id'] = $keyword;
			$useraaa = Db::name('user')->where(['id'=>$keyword])->field('user_nickname')->find();
			$keywords = $useraaa['user_nickname'];
			//die;
        }
		$fWhere=[];
		if(!empty($request['pay_type'])){
			$fWhere['f.fspan']=$request['pay_type'];
		}
		if(!empty($request['address'])){			//AB侧
			if(!empty($request['address_ids'])){
				//echo '有address_ids';
				$idWhere['u.id'] = ['in',$request['address_ids']];
				$sNum = count(array_filter(explode(',',$request['address_ids'])));
				$address_ids = $request['address_ids'];
			}else{
				//echo '无address_ids';
				$xiajiid = $user->getAB($request['id'],$request['pid'],$request['address']);
				$user->banArr();
				$idWhere['u.id'] = ['in',$xiajiid];
				//dump($xiajiid);
				$sNum = count(array_filter($xiajiid));
				//echo '哈哈哈'.$ids=implode(',',$xiajiid);
				$address_ids = implode(',',$xiajiid);
			}
			$address=$request['address'];
		}
		$pWhere=[];
		$time = date('Y-m-d',time());
		$date = explode('-',$time);
		$now_year = $date[0].'-01-01';
		$last_year = ($date[0]+1).'-01-01';
		$pWhere['cmf_pay.time']=[
			['>=',strtotime($now_year)],
			['<=',strtotime($last_year)],
			'and'
		];
		if(!empty($request['stime'])){
			$pWhere['cmf_pay.time']=['>=',strtotime($request['stime'])];
			$now_year='';$last_year='';
		}
		if(!empty($request['etime'])){
			$pWhere['cmf_pay.time']=['<=',strtotime($request['etime'])+60*60*24];
			$now_year='';$last_year='';
		}
		if(!empty($request['stime'])&&!empty($request['etime'])){
			$pWhere['cmf_pay.time']=[
				['>=',strtotime($request['stime'])],
				['<=',strtotime($request['etime'])+60*60*24],
				'and'
			];
			$now_year='';$last_year='';
		}
		//echo $now_year;
		//echo $last_year;
		$a = Db::name('pay')
			->field('cmf_pay.*,u.user_nickname,u.mobile,u.user_email,p.post_source,p.post_title,a.name,a.tel,a.address as addresss,f.fs')
			->join('cmf_portal_post p','p.id=cmf_pay.pid','left')
			->join('cmf_user u','cmf_pay.uid=u.id','left')
			->join('cmf_address a','a.id=cmf_pay.address_id','left')
			->join('cmf_user_money_detail f','f.id=cmf_pay.float_id','left')
			->whereOr($keywordComplex)
			->where($idWhere)->where($where)->where($fWhere)->where($pWhere)->select();
		$money = 0;
		$zong_money = 0;
		foreach($a as $v){
			$zong_money += $v['money'];
			$money=$money+$v["post_source"];
		}
        $userPay = new UserPayModel();
        $userOrder = Db::name('pay')
            //->with('getPorPost')
            //->with('getUser')
			->field('cmf_pay.*,u.user_nickname,u.mobile,u.user_email,p.post_source,p.post_title,a.name,a.tel,a.address as addresss,f.fs,f.type')
			->join('cmf_portal_post p','p.id=cmf_pay.pid','left')
			->join('cmf_user u','cmf_pay.uid=u.id','left')
			->join('cmf_address a','a.id=cmf_pay.address_id','left')
			->join('cmf_user_money_detail f','f.id=cmf_pay.float_id','left')
			->whereOr($keywordComplex)
			->where($idWhere)
            ->where($where)
			->where($fWhere)
			->where($pWhere)
			;
			if($order=='ASC'){
				$userOrder = $userOrder->order('cmf_pay.time ASC')
				->paginate(30,false,['query'=>request()->param()]);
				$this->assign('order','倒序排列');
			}else{
				$userOrder = $userOrder->order('cmf_pay.time DESC')
				->paginate(30,false,['query'=>request()->param()]);
				$this->assign('order','正序排列');
			}
		//echo Db::getLastSql();
		$page = $userOrder->render();
		$province = Db::name('province')->select()->toArray();
		$data = [
			'userOrder'=>$userOrder,'page'=>$page,'province'=>$province,
			'money_sum'=>'','sNum'=>'','shi'=>$zong_money,
			'now'=>$now_year,'last'=>$last_year,'keywords'=>$keywords,'keywords'=>$keywords
			//'page_money_sum'=>''
		];
		if(!empty($request['user_type'])){
			$user_type=$request['user_type'];
			if($user_type=='代理'){
			$data = [
				'userOrder'=>$userOrder,'page'=>$page,'province'=>$province,
				'money_sums'=>$money,'sNum'=>$sNum,
				'address'=>$address,
				'address_ids'=>$address_ids,'shi'=>$zong_money,
				'now'=>$now_year,'last'=>$last_year,'keywords'=>$keywords
				//'page_money_sums'=>$page_money_sum
			];
			}else if($user_type=='省代'){
				$data = [
					'userOrder'=>$userOrder,'page'=>$page,'province'=>$province,
					'money_sum'=>$money,'sNum'=>$sNum,
					'address'=>$address,'shi'=>$zong_money,
					'address_ids'=>$address_ids,
					'now'=>$now_year,'last'=>$last_year,'keywords'=>$keywords
					//'page_money_sum'=>$page_money_sum
				];
			}else if($user_type=='会员'){
				$data = [
					'userOrder'=>$userOrder,'page'=>$page,'province'=>$province,
					'money_sumss'=>$money,'sNum'=>$sNum,
					'address'=>$address,'shi'=>$zong_money,
					'address_ids'=>$address_ids,
					'now'=>$now_year,'last'=>$last_year,'keywords'=>$keywords
				];
			}
			if(!empty($request['address'])){
				//echo '哈哈哈'.$sNum;
				$data = [
					'userOrder'=>$userOrder,'page'=>$page,'province'=>$province,
					'money_sumsss'=>$money,'sNum'=>$sNum,
					'address'=>$address,'shi'=>$zong_money,
					//'address_ids'=>implode(',',$address_ids),
					'address_ids'=>$address_ids,
						'now'=>$now_year,'last'=>$last_year,'keywords'=>$keywords
						//'aqx'=>'1'
					//'id'=>$id,'ids'=>$ids
				];
			}
		}
		
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();
		$this->assign('aqx',$ars['role_id']);
        return $this->fetch('',$data);
    }
	public function editAddress(){
		$request = input('request.');
		$oldSon = $request['hson'];
		$newSon = $request['hstr'];
		$oldSon = array_filter(explode(',',$oldSon));
		$newSon = array_filter(explode(',',$newSon));
		//先转为数组->去空白;
		$order = new UserPayModel();
		for($i=1;$i<=count($newSon);$i++){
			$order->where(['son'=>$oldSon[$i]])->update(['fhd'=>$newSon[$i]]);
		}
		if($i==count($newSon)+1){
			$this->success('修改成功');
			//echo '修改成功';
		}else if($i>1 && $i<=count($newSon)){
			$this->success('由于某些未知原因,部分发货单号修改成功');
			//echo '由于某些未知原因,部分发货单号修改成功';
		}else{
			$this->error('修改失败');
			//echo '修改失败';
		}
	}
}