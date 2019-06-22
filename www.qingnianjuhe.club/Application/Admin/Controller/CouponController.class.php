<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ============================================================================
 * Date: 2015-12-11
 */
namespace Admin\Controller;
use Think\AjaxPage;

class CouponController extends BaseController {
    /**----------------------------------------------*/
     /*                优惠券控制器                  */
    /**----------------------------------------------*/
    /*
     * 优惠券类型列表
     */
    public function index(){
        //获取优惠券列表
        //echo time();
    	$count =  M('coupon')->count();
    	$Page = new \Think\Page($count,10);        
        $show = $Page->show();
        if(empty(I('type'))) $where ='`type` = 0';
        else {$type = I('type'); $where ="`type` = {$type} ";}
        $time = time();
        if(empty(I('time'))){
            $where.=" and {$time} <= `use_end_time`";
        }else{
            $where.=" and {$time} >= `use_end_time`";
        }
        if(empty(I('order'))){
            $order = 'add_time desc';
        }else{
            $order = 'add_time asc';
        }
        
        $lists = M('coupon')->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($lists as $key=>$val){
            $lists[$key]['usage'] = $val['use_num'] / $val['createnum'] * 100;
        }
        $store_id = get_arr_column($lists,'store_id');
        /*$store = M('store')->where("store_id in (".implode(',', $store_id).")")->getField('store_id,store_name');
        $this->assign('store',$store);*/
        $this->assign('lists',$lists);
        $this->assign('page',$show);// 赋值分页输出   
        $this->assign('coupons',C('COUPON_TYPE'));
        $this->display();
    }

    /*
     * 添加编辑一个优惠券类型
     */
    public function coupon_info(){
        if(IS_POST){
        	$data = I('post.');
//        	var_dump($data);die;
            $data['send_start_time'] = strtotime($data['send_start_time']);
            $data['send_end_time'] = strtotime($data['send_end_time']);
            $data['use_end_time'] = strtotime($data['use_end_time']);
            $data['use_start_time'] = strtotime($data['use_start_time']);
            $data['module_id'] = json_encode($data['module']);
            if($data['send_start_time'] > $data['send_end_time']){
                $this->error('发放日期填写有误');
            }
            if(empty($data['createnum'])) return $this->error('请填写发放数量');
            if(empty($data['id'])){
                if($data['type']==0){       //普通优惠券
                    $data['add_time'] = time();
                    $row = M('coupon')->add($data);
                }else{
                    //添加逻辑
                    //如果为分销商专属优惠券 应循环添加   分销商ID为 store_id
                    //查出所有的分销商
                    $AllSell = M('users') -> field('user_id') -> where(['is_distribut' => 1]) -> select();
                    //判断 $data['stors_id'] 是否为空
                    if($data['store_id'] == null){
                        foreach ($AllSell as $k => $v){
                            $data['store_id'] = $v['user_id'];
                            $data['add_time'] = time();
                            $row = M('coupon')->add($data);
                        }
                    }else{
                        $NowSwll = explode(',',$data['store_id']);
                        foreach ($NowSwll as $k => $v){
                            $data['store_id'] = $v;
                            $data['add_time'] = time();
                            $row = M('coupon')->add($data);
                        }
                    }
                }
            }else{
            	$row =  M('coupon')->where(array('id'=>$data['id']))->save($data);
            }
            if(!$row)
                $this->error('编辑代金券失败');
            $this->success('编辑代金券成功',U('Admin/Coupon/index'));
            exit;
        }
        $cid = I('get.id');
        $module = M('module')->select();
        $this->assign('module',$module);
        if($cid){
        	$coupon = M('coupon')->where(array('id'=>$cid))->find();
            $module_id = json_decode($coupon['module_id']);
            $this->assign('module_id',$module_id);
        	$this->assign('coupon',$coupon);
        }else{
        	$def['send_start_time'] = strtotime("+1 day");
        	$def['send_end_time'] = strtotime("+1 month");
        	$def['use_start_time'] = strtotime("+1 day");
        	$def['use_end_time'] = strtotime("+2 month");
        	$this->assign('coupon',$def);
        }     
        $this->display();
    }

    /*
    * 优惠券发放
    */
    public function make_coupon(){
        //获取优惠券ID
        $cid = I('get.id');
        $type = I('get.type');
        //查询是否存在优惠券
        $data = M('coupon')->where(array('id'=>$cid))->find();
        $remain = $data['createnum'] - $data['send_num'];//剩余派发量
    	if($remain<=0) $this->error($data['name'].'已经发放完了');
        if(!$data) $this->error("优惠券类型不存在");
        if($type != 4) $this->error("该优惠券类型不支持发放");
        if(IS_POST){
            $num  = I('post.num');
            if($num>$remain) $this->error($data['name'].'发放量不够了');
            if(!$num > 0) $this->error("发放数量不能小于0");
            $add['cid'] = $cid;
            $add['type'] = $type;
            $add['send_time'] = time();
            for($i=0;$i<$num; $i++){
                do{
                    $code = get_rand_str(8,0,1);//获取随机8位字符串
                    $check_exist = M('coupon_list')->where(array('code'=>$code))->find();
                }while($check_exist);
                $add['code'] = $code;
                M('coupon_list')->add($add);
            }
            M('coupon')->where("id=$cid")->setInc('send_num',$num);
            adminLog("发放".$num.'张'.$data['name']);
            $this->success("发放成功",U('Admin/Coupon/index'));
            exit;
        }
        $this->assign('coupon',$data);
        $this->display();
    }
    
    public function ajax_get_user(){
    	//搜索条件
    	$condition = array();
    	I('mobile') ? $condition['mobile'] = I('mobile') : false;
    	I('email') ? $condition['email'] = I('email') : false;
    	$nickname = I('nickname');
    	if(!empty($nickname)){
    		$condition['nickname'] = array('like',"%$nickname%");
    	}
    	$model = M('users');
    	$count = $model->where($condition)->count();
    	$Page  = new AjaxPage($count,10);
    	foreach($condition as $key=>$val) {
    		$Page->parameter[$key] = urlencode($val);
    	}
    	$show = $Page->show();
    	$userList = $model->where($condition)->order("user_id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        
        $user_level = M('user_level')->getField('level_id,level_name',true);       
        $this->assign('user_level',$user_level);
    	$this->assign('userList',$userList);
    	$this->assign('page',$show);
    	$this->display();
    }
    
    public function send_coupon(){
    	$cid = I('cid');    	
    	if(IS_POST){
    		$level_id = I('level_id');
    		$user_id = I('user_id');
    		$insert = '';
    		$coupon = M('coupon')->where("id=$cid")->find();
    		if($coupon['createnum']>0){
    			$remain = $coupon['createnum'] - $coupon['send_num'];//剩余派发量
    			if($remain<=0) $this->error($coupon['name'].'已经发放完了');
    		}
    		
    		if(empty($user_id) && $level_id>=0){
    			if($level_id==0){
    				$user = M('users')->where("is_lock=0")->select();
    			}else{
    				$user = M('users')->where("is_lock=0 and level_id=$level_id")->select();
    			}
    			if($user){
    				$able = count($user);//本次发送量
    				if($coupon['createnum']>0 && $remain<$able){
    					$this->error($coupon['name'].'派发量只剩'.$remain.'张');
    				}
    				foreach ($user as $k=>$val){
    					$user_id = $val['user_id'];
    					$time = time();
    					$gap = ($k+1) == $able ? '' : ',';
    					$insert .= "($cid,1,$user_id,$time)$gap";
    				}
    			}
    		}else{
    			$able = count($user_id);//本次发送量
    			if($coupon['createnum']>0 && $remain<$able){
    				$this->error($coupon['name'].'派发量只剩'.$remain.'张');
    			}
    			foreach ($user_id as $k=>$v){
    				$time = time();
    				$gap = ($k+1) == $able ? '' : ',';
    				$insert .= "($cid,1,$v,$time)$gap";
    			}
    		}
			$sql = "insert into __PREFIX__coupon_list (`cid`,`type`,`uid`,`send_time`) VALUES $insert";
			M()->execute($sql);
			M('coupon')->where("id=$cid")->setInc('send_num',$able);
			adminLog("发放".$able.'张'.$coupon['name']);
			$this->success("发放成功");
			exit;
    	}
    	$level = M('user_level')->select();
    	$this->assign('level',$level);
    	$this->assign('cid',$cid);
    	$this->display();
    }
    
    public function send_cancel(){
    	
    }

    /*
     * 删除优惠券类型
     */
    public function del_coupon(){
        //获取优惠券ID
        $cid = I('get.id');
        //查询是否存在优惠券
        $row = M('coupon')->where(array('id'=>$cid))->delete();
        if($row){
            //删除此类型下的优惠券
            M('coupon_list')->where(array('cid'=>$cid))->delete();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }


    /*
     * 优惠券详细查看
     */
    public function coupon_list(){


//        if(IS_POST){
//            $data = I('post');
//            var_dump($data);die;
//
//        }





//        $count = $count[0]['c'];
    	$Page = new \Think\Page($count,10);
    	$show = $Page->show();
        $m = M('coupon_list');
        $data = $m
            ->alias('a')
            ->field('a.*,ty_users.nickname')
            ->join('ty_users')
            ->where('a.store_id = ty_users.user_id')
            -> limit($Page->firstRow.','.$Page->listRows) -> select();
//        //查询该优惠券的列表
//        $sql = "SELECT l.*,c.name,o.order_sn,u.nickname FROM __PREFIX__coupon_list  l ".
//                "LEFT JOIN __PREFIX__coupon c ON c.id = l.cid ". //联合优惠券表查询名称
//                "LEFT JOIN __PREFIX__order o ON o.order_id = l.order_id ".     //联合订单表查询订单编号
//                "LEFT JOIN __PREFIX__users u ON u.user_id = l.uid WHERE l.cid = ".$cid.    //联合用户表去查询用户名
//                " limit {$Page->firstRow} , {$Page->listRows}";
//        $coupon_list = M()->query($sql);
        $this->assign('coupon_type',C('COUPON_TYPE'));
        $this->assign('type',$check_coupon['type']);       
        $this->assign('lists',$data);
    	$this->assign('page',$show);        
        $this->display();
    }
    
    /*
     * 删除一张优惠券
     */
    public function coupon_list_del(){
        //获取优惠券ID
        $cid = I('get.id');
        if(!$cid)
            $this->error("缺少参数值");
        //查询是否存在优惠券
         $row = M('coupon_list')->where(array('id'=>$cid))->delete();
        if(!$row)
            $this->error('删除失败');
        $this->success('删除成功');
    }
}
