<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ============================================================================
 * Author: IT宇宙人      
 * 
 * Date: 2016-03-09
 */

namespace Admin\Controller;
use Think\Page;
use Admin\Logic\GoodsLogic;

class DistributController extends BaseController {
    
    /*
     * 初始化操作
     */
    public function _initialize() {
       parent::_initialize();
    }    
    
    /**
     * 分成记录
     */
    public function rebate_log()
    { 
        $model = M("rebate_log"); 
        $status = I('status');
        $user_id = I('user_id');
        $order_sn = I('order_sn');        
        $create_time = I('create_time');
        $create_time = $create_time  ? $create_time  : date('Y-m-d',strtotime('-1 year')).' - '.date('Y-m-d',strtotime('+1 day'));
                       
        $create_time2 = explode(' - ',$create_time);
        $where = " store_id = ".STORE_ID." and create_time >= '".strtotime($create_time2[0])."' and create_time <= '".strtotime($create_time2[1])."' ";
        
        if($status === '0' || $status > 0)
            $where .= " and status = $status ";        
        $user_id && $where .= " and user_id = $user_id ";
        $order_sn && $where .= " and order_sn like '%{$order_sn}%' ";
                        
        $count = $model->where($where)->count();
        $Page  = new Page($count,16);        
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        
        //nickname
        $get_user_id = get_arr_column($list, 'user_id'); // 获佣用户
        $buy_user_id = get_arr_column($list, 'user_id'); // 购买用户
        $user_id_arr = array_merge($get_user_id,$buy_user_id);      //合并数组
        if(!empty($user_id_arr))
        $user_arr = M('users')->where("user_id in (".  implode(',', $user_id_arr).")")->select();
        $this->assign('user_arr',$user_arr);
        $this->assign('create_time',$create_time);        
        $show  = $Page->show();                 
        $this->assign('show',$show);
        $this->assign('list',$list);
        C('TOKEN_ON',false);
        $this->display();    
    }
   
    public function goods_list(){
    	$GoodsLogic = new GoodsLogic();
    	$brandList = $GoodsLogic->getSortBrands();
    	$categoryList = $GoodsLogic->getSortCategory();
    	$this->assign('categoryList',$categoryList);
    	$this->assign('brandList',$brandList);
    	$where = ' distribut > 0 and store_id='.STORE_ID;
    	$cat_id = I('cat_id');
    	if($cat_id > 0)
    	{
    		$grandson_ids = getCatGrandson($cat_id);
    		$where .= " and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
    	}
    	$key_word = I('key_word') ? trim(I('key_word')) : '';
    	if($key_word)
    	{
    		$where = "$where and (goods_name like '%$key_word%' or goods_sn like '%$key_word%')" ;
    	}
    	I('brand_id') && $where = "$where and brand_id = ".I('brand_id') ;
    	$model = M('Goods');
    	$count = $model->where($where)->count();
    	$Page  = new Page($count,10);
    	$show = $Page->show();
    	$goodsList = $model->where($where)->order('sales_sum desc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('goodsList',$goodsList);
    	$this->assign('page',$show);
    	$this->display();
    }
    
    /**
     * 修改编辑 分成 
     */
    public  function editRebate(){        
        $id = I('id',0);
        $model = M("rebate_log");
        $rebate_log = $model->find($id);
        if(empty($rebate_log))
            $this->error("参数错误!!!");
               
        if(IS_POST)
        {
                $model->create();
                
                // 如果是确定分成 将金额打入分佣用户余额
                if($model->status == 3 && $rebate_log['status'] != 3)
                {
                    accountLog($model->user_id, $rebate_log['money'], 0,"订单:{$rebate_log['order_sn']}分佣",$rebate_log['money']);
                }                
                $model->save();                               
                $this->success("操作成功!!!",U('Distribut/rebate_log')); 
                exit;
        }                      
       
       $user = M('users')->where("user_id = {$rebate_log[user_id]}")->find();       
            
       if($user['nickname'])        
           $rebate_log['user_name'] = $user['nickname'];
       elseif($user['email'])        
           $rebate_log['user_name'] = $user['email'];
       elseif($user['mobile'])        
           $rebate_log['user_name'] = $user['mobile'];            
       
       $this->assign('user',$user);
       $this->assign('rebate_log',$rebate_log);
       $this->display();           
    }        
            

    /*
     * 分销设置
     */
    public function setting()
    {

        if(IS_POST){
            $data = I('post.');
//            $data['proportion_id'] = I('id');
            $data['proportion_bl'] = I('proportion_bl');
            $data['module_id'] = json_encode($data['module']);
            $data['module2_id'] = json_encode($data['module2']);
            $data['title'] = I('title');
            $data['selltitle'] = I('selltitle');
            if(is_array($data) || empty($data)){
                $m = M('proportion');
                $res = $m->where(['proportion_id'=>1])->save($data);
                if($res){
                    $this ->success('操作成功',U('admin/Distribut/setting'));
                }else{
                    $this ->error('参数!!!');

                }
            }else{
                $this ->error('参数错误!!!');
            }
        }else{
            $module = M('module')->select();
            $this->assign('module',$module);
            $model = M('proportion');
            $data = $model -> find();
            $module_id = json_decode($data['module_id']);
            $module_id2 = json_decode($data['module2_id']);
            $this->assign('module_id',$module_id);
            $this->assign('module_id2',$module_id2);
//            var_dump($data);die;
            $this ->assign('data',$data);
            $this ->display();
        }

    }

    /**
     * 提现申请
     *
     */
    public function withdrawals()
    {
        if(IS_POST){
            $status = I('status');
            $model = M('store_withdrawals');
            $data = $model
                    ->alias('a')
                    ->field('a.*,ty_users.nickname,ty_users.wxname')
                    ->join('ty_users')
                    ->order('create_time desc')
                    ->where("a.user_id = ty_users.user_id and a.status =' $status '")
                    -> select();
        }else{
            $model = M('store_withdrawals');
            $data = $model
                    ->alias('a')
                    ->field('a.*,ty_users.nickname,ty_users.wxname')
                    ->join('ty_users')
                    ->order('create_time desc')
                    ->where('a.user_id = ty_users.user_id')
                    -> select();
        }
            $this -> assign('list',$data);
            $this -> display();
    }

   /*
     * list
     */
    public function or_distributor_list()
    {
        $this -> display();
    }
    /**
     * 提现审核
     */
    public function editWithdrawals()
    {
        if(IS_POST){
        $data = I('post.');

                $NewData = M('users') -> where(['user_id' => $data['user_id']]) -> find();
                if($data['remark'] == '寄卖金额提现'){
                    if($data['money'] > $NewData['total_amount']){
                        $this -> error('提现金额过大!');
                    }
                    $NewData['total_amount'] =  $NewData['total_amount'] - $data['money'];
                    $user_id = $data['user_id'];
                    $retmoney = $data['money'];
                    logSell($order_id,$user_id,$pay = 2,$retmoney);
                }elseif($data['remark'] == '分销金额提现'){
                    if($data['money'] > $NewData['user_money']){
                        $this -> error('提现金额过大!');
                    }
                    $NewData['user_money'] =  $NewData['user_name'] - $data['money'];
                }
                $res = M('users') -> where(['user_id' => $data['user_id']]) -> save($NewData);
                $data['status'] = 1;
                $result = M('store_withdrawals') -> where(['id' => $data['id']]) -> save($data);
                if($res && $result){
                    $this -> success('操作成功',U('Distribut/editWithdrawals'));
                }

        }else{
            $id = I('get.id');
            $m = M('store_withdrawals');
            $data = $m
                ->alias('a')
                ->field('a.*,ty_users.nickname,ty_users.wxname')
                ->join('ty_users')
                ->order('create_time desc')
                ->where(" a.id = $id  and a.user_id = ty_users.user_id")
                ->find();
            $this -> assign('data',$data);
            $this -> display();
        }
    }


}
