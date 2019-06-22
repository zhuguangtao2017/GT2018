<?php
/**
 * Created by PhpStorm.
 * User: 'zhuGuangTao'
 * Date: 2018/11/14
 * Time: 16:05
 */

namespace Admin\Controller;


class SellController extends BaseController
{
    public function index(){
        $this->display();
    }
    public function ajaxIndex(){
        $where['store_id'] = ['neq',0];
        I('sellType')=='' ? false : $where['sellType'] = I('sellType');
        I('store_id')=='' ? false : $where['store_id'] = ['in',get_arr_column(M('users')->where(['nickname'=>['like','%'.I('store_id').'%']])->field('user_id')->select(),'user_id')];
        $goodsList = M('goods')->join('ty_users on ty_users.user_id = ty_goods.store_id')->where($where)->select();
        $this->assign('goodsList',$goodsList);
        $this->display();
    }
    public function act(){
        $where['sellType'] = I('act');
        $goods_array = explode(',', I('goods_ids'));
        $where['reason'] = I('reason');
        $goods_state_cg = C('sell_state');
        $return_success = array('status' => 1, 'msg' => '操作成功', 'data' => '');
        if (!array_key_exists(I('act'), $goods_state_cg)) {
            $return_success = array('status' => -1, 'msg' => '操作失败，商品没有这种属性', 'data' => '');
            $this->ajaxReturn($return_success);
        }
        foreach ($goods_array as $key => $val) {
            $update_goods_state = M('goods')->where("goods_id = $val")->save($where);
            if ($update_goods_state) {
                $update_goods = M('goods')->where(array('goods_id' => $val))->find();
                // 给商家发站内消息 告诉商家商品被批量操作
                $store_msg = array(
                    'store_id' => $update_goods['store_id'],
                    'content' => "您的商品\"{$update_goods[goods_name]}\"被{$goods_state_cg[I('act')]},原因:{$where['reason']}",
                    'addtime' => time(),
                );
                M('store_msg')->add($store_msg);
            }
        }
        $this->ajaxReturn($return_success);
    }
}