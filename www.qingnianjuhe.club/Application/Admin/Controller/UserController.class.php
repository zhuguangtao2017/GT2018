<?php
namespace Admin\Controller;

use Admin\Logic\UsersLogic;
use Think\AjaxPage;
use Think\Page;
use WXAPI\Controller\CartController;

class UserController extends BaseController {

    public function index(){
        $this->display();
    }
    //开发 会员列表显示微信用户   用户添加字段 id_sell 表示用户是否寄卖wu'pin

    /**
     * 搜索用户名
     */
    public function search_user()
    {
        $search_key = trim(I('search_key'));
        if(strstr($search_key,'@'))
        {
            $list = M('users')->where(" email like '%$search_key%' ")->select();
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['email']}</option>";
            }
        }
        else
        {
            $list = M('users')->where(" mobile like '%$search_key%' ")->select();
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['mobile']}</option>";
            }
        }
        exit;
    }

    /**
     * 会员列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        $condition['is_lock'] = 0;
        I('mobile') ? $condition['mobile'] = I('mobile') : false;
        I('email') ? $condition['email'] = I('email') : false;
        $sort_order = I('order_by').' '.I('sort');
//        $condition['is_distribut'] = 0;
        $model = M('users');
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('level',M('user_level')->getField('level_id,level_name'));
        $this->display();
    }
    /**
     * 会员列表
     */
    public function ajax_index(){
        // 搜索条件
        $condition = array();
        $condition['is_lock'] = 0;
        I('mobile') ? $condition['mobile'] = I('mobile') : false;
        I('email') ? $condition['email'] = I('email') : false;
        $sort_order = I('order_by').' '.I('sort');
        $condition['is_distribut'] = 1;
        $model = M('users');
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }

        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('level',M('user_level')->getField('level_id,level_name'));
        // var_dump($userList);die;
        $this->display();
    }
    public function payhy()
    {

        if(IS_POST){
//            $data['proportion_id'] = I('id');
            $data = I('post.');
            if(is_array($data) || empty($data)){
                $m = M('payhy');
                $res = $m ->where(['id' => 1])->save($data);
                if($res){
                    $this -> success('操作成功',U('admin/user/payhy'));
                }else{
                    $this -> error('参数!!!');

                }
            }else{
                $this -> error('参数错误!!!');
            }
        }else{
            $model = M('payhy');
            $data = $model -> find();
//            var_dump($data);die;
            $this -> assign('data',$data);
            $this -> display();
        }

    }
    public function lock(){     //黑名单列表
        $this->display();
    }
    public function ajaxlock(){
        $condition['is_lock'] = 1;
        I('mobile') ? $condition['mobile'] = I('mobile') : false;
        $count = M('users')->where($condition)->order('reg_time desc')->count();
        $Page = new AjaxPage($count,10);
        $list = M('users')->where($condition)->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('userList',$list);
        $show = $Page->show();
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    public function blacklist(){        //加入黑名单
        if(!empty(I('id'))){
            if(I('type')==1){       //取消黑名单
                M('users')->where(['user_id'=>I('id')])->save(['is_lock'=>0]);
                return $this->success('取消加入黑名单成功',U('User/index'));
            }else{                  //加入黑名单
                M('users')->where(['user_id'=>I('id')])->save(['is_lock'=>1]);
                return $this->success('已加入黑名单',U('User/lock'));
            }
        }return $this->error('缺少参数');
    }
    /**
     * 会员列表
     */
    public function or_ajax_index(){
        // 搜索条件
        $condition = array();
        I('mobile') ? $condition['mobile'] = I('mobile') : false;
        I('email') ? $condition['email'] = I('email') : false;
        $sort_order = I('order_by').' '.I('sort');
        $condition['distatus'] = 1;
        $model = M('users');
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }

        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('level',M('user_level')->getField('level_id,level_name'));
        // var_dump($userList);die;
        $this->display();
    }
    // /**
    //  * 会员详细信息查看
    //  */
    public function detail(){
        $uid = I('get.id');
        $user = D('users')->where(array('user_id'=>$uid))->find();
        if(!$user)
            exit($this->error('会员不存在'));
        if(IS_POST) {
//            var_dump($_POST);
            if ($_POST['distatus'] = 2) {
                $_POST['is_distribut'] = 1;
            } elseif ($_POST['distatus'] = 3) {
                $_POST['is_distribut'] = 0;
            }

            $row = M('users')->where(['user_id' => $uid])->save($_POST);
            if ($row)
                exit($this->success('修改成功'));
            exit($this->error('未作内容修改或修改失败'));

        }

        // var_dump($user);die;
        $this->assign('user',$user);
        $this->display();
    }


    // public function add_user(){
    // 	if(IS_POST){
    // 		$data = I('post.');
    // 		$user_obj = new UsersLogic();
    // 		$res = $user_obj->addUser($data);
    // 		if($res['status'] == 1){
    // 			$this->success('添加成功',U('User/index'));exit;
    // 		}else{
    // 			$this->error('添加失败,'.$res['msg'],U('User/index'));
    // 		}
    // 	}
    // 	$this->display();
    // }

    // /**
    //  * 用户收货地址查看
    //  */
    public function address(){
        $uid = I('get.id');
        $lists = D('user_address')->where(array('user_id'=>$uid))->select();
        $regionList = M('Region')->getField('id,name');
        $this->assign('regionList',$regionList);
        $this->assign('lists',$lists);
        $this->display();
    }

    // /**
    //  * 删除会员
    //  */
    public function delete(){
        $uid = I('get.id');
        $row = M('users')->where(array('user_id'=>$uid))->delete();
        if($row){
            $this->success('成功删除会员');
        }else{
            $this->error('操作失败');
        }
    }

    // /**
    //  * 账户资金记录
    //  */
    // public function account_log(){
    //     $user_id = I('get.id');
    //     //获取类型
    //     $type = I('get.type');
    //     //获取记录总数
    //     $count = M('account_log')->where(array('user_id'=>$user_id))->count();
    //     $page = new Page($count);
    //     $lists  = M('account_log')->where(array('user_id'=>$user_id))->order('change_time desc')->limit($page->firstRow.','.$page->listRows)->select();

    //     $this->assign('user_id',$user_id);
    //     $this->assign('page',$page->show());
    //     $this->assign('lists',$lists);
    //     $this->display();
    // }

    // /**
    //  * 账户资金调节
    //  */
    // public function account_edit(){
    //     $user_id = I('get.id');
    //     if(!$user_id > 0)
    //         $this->error("参数有误");
    //     if(IS_POST){
    //         //获取操作类型
    //         $m_op_type = I('post.money_act_type');
    //         $user_money = I('post.user_money');
    //         $user_money =  $m_op_type ? $user_money : 0-$user_money;

    //         $p_op_type = I('post.point_act_type');
    //         $pay_points = I('post.pay_points');
    //         $pay_points =  $p_op_type ? $pay_points : 0-$pay_points;

    //         $f_op_type = I('post.frozen_act_type');
    //         $frozen_money = I('post.frozen_money');
    //         $frozen_money =  $f_op_type ? $frozen_money : 0-$frozen_money;

    //         $desc = I('post.desc');
    //         if(!$desc)
    //             $this->error("请填写操作说明");
    //         if(accountLog($user_id,$user_money,$pay_points,$desc)){
    //             $this->success("操作成功",U("Admin/User/account_log",array('id'=>$user_id)));
    //         }else{
    //             $this->error("操作失败");
    //         }
    //         exit;
    //     }
    //     $this->assign('user_id',$user_id);
    //     $this->display();
    // }

    // public function recharge(){
    //     $timegap = I('timegap');
    // 	$nickname = I('nickname');
    // 	$map = array();
    // 	if($timegap){
    // 		$gap = explode(' - ', $timegap);
    // 		$begin = $gap[0];
    // 		$end = $gap[1];
    // 		$map['ctime'] = array('between',array(strtotime($begin),strtotime($end)));
    // 	}
    // 	if($nickname){
    // 		$map['nickname'] = array('like',"%$nickname%");
    // 	}
    // 	$count = M('recharge')->where()->count();
    // 	$page = new Page($count);
    // 	$lists  = M('recharge')->where()->order('ctime desc')->limit($page->firstRow.','.$page->listRows)->select();
    // 	$this->assign('page',$page->show());
    // 	$this->assign('lists',$lists);
    // 	$this->display();
    // }

    public function level(){
        $act = I('GET.act','add');
        $this->assign('act',$act);
        $level_id = I('GET.level_id');
        $level_info = array();
        if($level_id){
            $level_info = D('user_level')->where('level_id='.$level_id)->find();
            $this->assign('info',$level_info);
        }
        $this->display();
    }

    public function levelList(){
        $Ad =  M('user_level');
        $res = $Ad->where('1=1')->order('level_id')->page($_GET['p'].',10')->select();
        if($res){
            foreach ($res as $val){
                $list[] = $val;
            }
        }
        if(IS_POST){
            $_POST['module_id'] = json_encode($_POST['module_id']);
            $result = M('proportion')->where(['proportion_name'=>'会员设置'])->save($_POST);
        }
        $module = M('module')->select();
        $module_id = json_decode(M('proportion')->where(['proportion_name'=>'会员设置'])->getField('module_id'),true);
        $this->assign('module',$module);
        $this->assign('module_id',$module_id);
        $this->assign('list',$list);
        $count = $Ad->where('1=1')->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function levelHandle(){
        $data = I('post.');
        if($data['act'] == 'add'){
            $r = D('user_level')->add($data);
        }
        if($data['act'] == 'edit'){
            $r = D('user_level')->where('level_id='.$data['level_id'])->save($data);
        }

        if($data['act'] == 'del'){
            $r = D('user_level')->where('level_id='.$data['level_id'])->delete();
            if($r) exit(json_encode(1));
        }

        if($r){
            $this->success("操作成功",U('Admin/User/levelList'));
        }else{
            $this->error("操作失败",U('Admin/User/levelList'));
        }
    }
    /**
     * 账户资金调节
     */
    public function returnStatus(){
        $id = I('id');
        $info = M('return_goods')->find($id);
        if($info['status'] == 1){
            $out_trade_no = $info['order_sn'];
            $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
            $cart = new CartController();
            $data = [
                'appid' => APP_ID,
                'mch_id' => MCHID,
                'nonce_str' => $cart->getRandChar(32),
                'out_trade_no' => $out_trade_no
            ];
            $data['sign'] = $cart->getSign($data,false);
            $xml = $cart->arrayToXml($data);
            $result = $cart->xmlstr_to_array($cart->postXmlCurl($xml,$url));
            if($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS'){
                $return_goods_id = $out_trade_no;
                $return_goods = M('return_goods')->find($id);
//        var_dump($return_goods);


                $user_id = $return_goods['user_id'];
                $order_goods = M('order_goods')->where("order_id ={$return_goods['order_id']} and goods_id = {$return_goods['goods_id']} ")->find();

                $res = M('order')->where(['order_id' => $return_goods['order_id']])->find();
                $money = 0;
//        var_dump($res);die;
                if($res['pay_status']==1&&$res['order_status']==1){
                    //查询分成记录表中该订单状态为已付款的数据 计算money 并将数据状态修改为4
                    $arr = M('rebate_log')->where(['order_sn'=>$return_goods['order_sn'],'status'=>1])->select();
                    foreach ($arr as $key => $value) {
                        $money+=$value['money'];
                        M('rebate_log')->where(['id'=>$value['id']])->save(['status'=>4,'remark'=>'用户已取消了订单']);
                    }
//            logOrder($res['order_id'],'用户已取消订单','订单已退款',$res['user_id']);   //订单表生成记录
                    $result = M('users')->where(['user_id'=>$arr[0]['user_id']])->setDec('frozen_money',$money);  //减一下用户冻结金额
                    logSell($res['order_id'],$res['user_id'],3);   //往sell表中添加 订单退款的记录
                    M('order')->where(['master_order_sn'=>$return_goods['order_sn']])->save(['order_status'=>3,'refund'=>2]);//修改订单状态为3
                    $orderGoodsArr = M('OrderGoods')->where("order_id = $res[order_id]")->select();
                    foreach ($orderGoodsArr as $key => $val) {
                        M('SpecGoodsPrice')->where("goods_id = {$val['goods_id']} and `key` = '{$val['spec_key']}'")->setInc('store_count',$val['goods_num']);
                        refresh_stock($val['goods_id']);
                    }
                    //更新库存 查询订单中的购买的商品 向规格表中添加库存 更新库存refresh_stock
                    $result = M('return_goods')->where("id = $id")->save(['status'=>2]);
                    exit(json_encode(['status'=>1,'msg'=>"订单已退款：{$out_trade_no}"]));
                }else{
                    exit(json_encode(['status'=>2,'msg'=>"订单已退款：{$out_trade_no}"]));
                }
            }
        }else exit(json_encode(['status'=>'2','msg'=>'订单状态也不允许','result'=>$info]));

    }
    public function returnGs(){
        $result = M('return_goods')->where(['id'=>I('return_goods_id')])->save(['status'=>I('type')]);
            if($result) exit($this->success("操作成功",U("Order/return_list")));
            exit($this->success("网络出错",U("Order/return_list")));
    }
    public function return_goods(){
        $desc = I('post.desc');
        $return_goods_id = I('return_goods_id');
        $return_goods = M('return_goods')->where("id = $return_goods_id")->find();
//        var_dump($return_goods);
        empty($return_goods) && $this->error("参数有误");

        $user_id = $return_goods['user_id'];
        $order_goods = M('order_goods')->where("order_id ={$return_goods['order_id']} and goods_id = {$return_goods['goods_id']} ")->find();
        $res = M('order')->where(['order_id' => $return_goods['order_id']])->find();
        $money = 0;
//        var_dump($res);die;
        if($res['pay_status']==1&&$res['order_status']==1){
            //查询分成记录表中该订单状态为已付款的数据 计算money 并将数据状态修改为4
            $arr = M('rebate_log')->where(['order_sn'=>$return_goods['order_sn'],'status'=>1])->select();
            foreach ($arr as $key => $value) {
                $money+=$value['money'];
                M('rebate_log')->where(['id'=>$value['id']])->save(['status'=>4,'remark'=>'用户已取消了订单']);
            }
//            logOrder($res['order_id'],'用户已取消订单','订单已退款',$res['user_id']);   //订单表生成记录
            $result = M('users')->where(['user_id'=>$arr[0]['user_id']])->setDec('frozen_money',$money);  //减一下用户冻结金额
            logSell($res['order_id'],$res['user_id'],3);   //往sell表中添加 订单退款的记录
            M('order')->where(['master_order_sn'=>$return_goods['order_sn']])->save(['order_status'=>3,'refund'=>2]);//修改订单状态为3
            $orderGoodsArr = M('OrderGoods')->where("order_id = $res[order_id]")->select();
            foreach ($orderGoodsArr as $key => $val) {
                M('SpecGoodsPrice')->where("goods_id = {$val['goods_id']} and `key` = '{$val['spec_key']}'")->setInc('store_count',$val['goods_num']);
                refresh_stock($val['goods_id']);
            }
            //更新库存 查询订单中的购买的商品 向规格表中添加库存 更新库存refresh_stock
            $this->success("操作成功",U("Order/return_list"));
        }else{
            $this->error("操作有误");
        }

//        if($order_goods['is_send'] != 1)
//            $this->error("该订单商品状态不能退款操作");
        /*
                $order = M('order')->where("order_id = {$return_goods['order_id']}")->find();


                // 计算退回积分公式
                //  退款商品占总商品价比例 =  (退款商品价 * 退款商品数量)  / 订单商品总价      // 这里是算出 退款的商品价格占总订单的商品价格的比例 是多少
                //  退款积分 = 退款比例  * 订单使用积分

                // 退款价格的比例
                $return_price_ratio = ($order_goods['member_goods_price'] * $order_goods['goods_num']) / $order['goods_price'];
                // 退还积分 = 退款价格的比例 *
                $return_integral = ceil($return_price_ratio * $order['integral']);

                 // 退还金额 = (订单商品总价 - 优惠券 - 优惠活动) * 退款价格的比例 - (退还积分 + 当前商品送出去的积分) / 积分换算比例
                 // 因为积分已经退过了, 所以退金额时应该把积分对应金额推掉 其次购买当前商品时送出的积分也要退回来,以免被刷积分情况

                $return_goods_price = ($order['goods_price'] - $order['coupon_price'] - $order['order_prom_amount']) * $return_price_ratio - ($return_integral + $order_goods['give_integral']) /  tpCache('shopping.point_rate');
                $return_goods_price = round($return_goods_price,2); // 保留两位小数
         */

        $refund = order_settlement($return_goods['order_id'],$order_goods['rec_id']);  // 查看退款金额
        //  print_r($refund);
        $return_goods_price = $refund['refund_settlement'] ? $refund['refund_settlement'] : 0; // 这个商品的退款金额
        //$refund_integral = $refund['refund_integral'] ? ($refund['refund_integral'] * -1) : 0; // 这个商品的退积分
        $refund_integral = $refund['refund_integral'] - $refund['give_integral'];


        if(IS_POST)
        {
            if(!$desc)
                $this->error("请填写操作说明");
            if(!$user_id > 0)
                $this->error("参数有误");

            $pending_money = M('store')->where(" store_id = ".STORE_ID)->getField('pending_money'); // 商家在未结算资金
            if($pending_money < $return_goods_price)
                $this->error("你的未结算资金不足 ￥{$return_goods_price}");

            //     M('store')->where(" store_id = ".STORE_ID)->setDec('pending_money',$user_money); // 从商家的 未结算自己里面扣除金额
            $result = storeAccountLog(STORE_ID, 0,$return_goods_price * -1,$desc,$return_goods['order_id']);
            if($result)
            {
                accountLog($user_id,$return_goods_price,$refund_integral,'订单退货',0,$return_goods['order_id']);
            }  else {
                $this->error("操作失败");
            }
            M('order_goods')->where("rec_id = {$order_goods['rec_id']}")->save(array('is_send'=>3));//更改商品状态
            // 如果一笔订单中 有退货情况, 整个分销也取消
            M('rebate_log')->where("order_id = {$return_goods['order_id']}")->save(array('status'=>4,'remark'=>'订单有退货取消分成'));

            $this->success("操作成功",U("Order/return_list"));
            exit;
        }

        $this->assign('return_goods_price',$return_goods_price);
        $this->assign('return_integral',$refund_integral);
        $this->assign('order_goods',$order_goods);
        $this->assign('user_id',$user_id);
        $this->display();
    }


    // /**
    //  * 分销树状关系
    //  */
    // public function ajax_distribut_tree()
    // {
    //       $list = M('users')->where("first_leader = 1")->select();
    //       $this->display();
    // }

    // /**
    //  *
    //  * @time 2016/08/31
    //  * @author dyr
    //  * 发送站内信
    //  */
    // public function sendMessage()
    // {
    //     $user_id_array = I('get.user_id_array');
    //     $users = array();
    //     if (!empty($user_id_array)) {
    //         $users = M('users')->field('user_id,nickname')->where(array('user_id' => array('IN', $user_id_array)))->select();
    //     }
    //     $this->assign('users', $users);
    //     $this->display();
    // }

    // /**
    //  * 发送系统消息
    //  * @author dyr
    //  * @time  2016/09/01
    //  */
    // public function doSendMessage()
    // {
    //     $call_back = I('call_back');//回调方法
    //     $message = I('post.text');//内容
    //     $type = I('post.type', 0);//个体or全体
    //     $admin_id = session('admin_id');
    //     $users = I('post.user');//个体id
    //     $message = array(
    //         'admin_id' => $admin_id,
    //         'message' => $message,
    //         'category' => 0,
    //         'send_time' => time()
    //     );
    //     if ($type == 1) {
    //         //全体用户系统消息
    //         $message['type'] = 1;
    //         M('Message')->data($message)->add();
    //     } else {
    //         //个体消息
    //         $message['type'] = 0;
    //         if (!empty($users)) {
    //             $create_message_id = M('Message')->data($message)->add();
    //             foreach ($users as $key) {
    //                 M('user_message')->data(array('user_id' => $key, 'message_id' => $create_message_id, 'status' => 0, 'category' => 0))->add();
    //             }
    //         }
    //     }
    //     echo "<script>parent.{$call_back}(1);</script>";
    //     exit();
    // }

    // /**
    //  *
    //  * @time 2016/09/03
    //  * @author dyr
    //  * 发送邮件
    //  */
    // public function sendMail()
    // {
    //     $user_id_array = I('get.user_id_array');
    //     $users = array();
    //     if (!empty($user_id_array)) {
    //         $user_where = array(
    //             'user_id' => array('IN', $user_id_array),
    //             'email'=> array('neq','')
    //         );
    //         $users = M('users')->field('user_id,nickname,email')->where($user_where)->select();
    //     }
    //     $this->assign('smtp',tpCache('smtp'));
    //     $this->assign('users',$users);
    //     $this->display();
    // }

    // /**
    //  * 发送邮箱
    //  * @author dyr
    //  * @time  2016/09/03
    //  */
    // public function doSendMail()
    // {
    //     $call_back = I('call_back');//回调方法
    //     $message = I('post.text');//内容
    //     $title = I('post.title');//标题
    //     $users = I('post.user');
    //     if (!empty($users)) {
    //         $user_id_array = implode(',', $users);
    //         $users = M('users')->field('email')->where(array('user_id' => array('IN', $user_id_array)))->select();
    //         $to = array();
    //         foreach ($users as $user) {
    //             if (check_email($user['email'])) {
    //                 $to[] = $user['email'];
    //             }
    //         }
    //         $res = send_email($to, $title, $message);
    //         echo "<script>parent.{$call_back}({$res});</script>";
    //         exit();
    //     }
    // }
}
