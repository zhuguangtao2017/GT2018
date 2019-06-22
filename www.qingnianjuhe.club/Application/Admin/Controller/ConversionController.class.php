<?php
namespace Admin\Controller;
use Think\AjaxPage;
class ConversionController extends BaseController{
    public function list(){
        return $this->display();
    }
    public function ajaxList(){
        $model = M('conversion');
        $where = [];
        if(!empty(I('goods_name'))){
            $where['goods_name'] = ['like','%'.I('goods_name').'%'];
        }
        if(!empty(I('status'))){
            $where['status'] = I('status');
        }
        $count = $model->where($where)->count();
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();
        $list = M('conversion')->where($where)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$show);// 赋值分页输出
        return $this->display();
    }
    public function addEditGoods(){
        $rules  = array(
            array('goods_name','require','商品名必须！'), //默认情况下用正则进行验证
            array('price','require','市场价必须！'),
            array('integral','require','兑换所需积分不能为空！'),
            array('count','require','库存不能为空！'),
            array('imgs','require','请选择图片'), // 当值不为空的时候判断是否在一个范围内
        );
        $model = D('conversion');
        $_POST['imgs'] = implode(',',$_POST['thum']);
        if(IS_POST){
            if ((!$model->validate($rules)->create())){     // 如果创建失败 表示验证没有通过 输出错误提示信息
                $return_arr = array(
                    'status' => -1,
                    'msg'   => '操作失败',
                    'data'  => $model->getError(),
                );
                $this->ajaxReturn(json_encode($return_arr));
            }else{     // 验证通过 可以进行其他数据操作
                $_POST['create_time'] = time();
                if(empty(I('id'))) $result = $model->add($_POST);
                else $result = $model->save($_POST);
                if($result){
                    $return_arr = array(
                        'status' => 1,
                        'msg'   => '操作成功',
                        'data'  => array('url'=>U('Admin/Conversion/list')),
                    );
                    $this->ajaxReturn(json_encode($return_arr));
                }else{
                    $return_arr = array(
                        'status' => -1,
                        'msg'   => '操作失败',
                        'data'  => array('url'=>U('Admin/Conversion/addEditGoods')),
                    );
                    $this->ajaxReturn(json_encode($return_arr));
                }
            }
        }else{
            if(I('id')){
                $info = M('conversion')->find(I('id'));
                $imgs = explode(',',$info['imgs']);
                $this->assign('imgs',$imgs);
                $this->assign('goodsInfo',$info);
            }
            $this->initEditor(); // 编辑器
            return $this->display();
        }
    }
    public function conver_order(){            //兑换订单列表
        $arr = M('take')->select();
        $begin = date('Y/m/d',strtotime('-1 days'));//30天前
        $end = date('Y/m/d',time());
        $this->assign('timegap',$begin.'-'.$end);
        $this->assign('arr',$arr);
        return $this->display();
    }
    public function ajaxconver(){
    	$where = [];
        $timegap = I('timegap');
        if($timegap){
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1])+24 * 60 * 60;
        }else{
            $begin = strtotime(date('Y/m/d',strtotime('-1 days')));
            $end = strtotime(date('Y/m/d',time()))+24 * 60 * 60;
        }
        if($begin && $end){
            $where['create_time'] = array('between',"$begin,$end");
        }
        if(!empty(I('address'))) $where['user_address'] = I('address');
        if(!empty(I('user_name'))){
            $where['user_name'] = ['like','%'.trim(I('user_name')).'%'];
        }
        if(!empty(I('goods_name'))) $where['goods_name'] = ['like','%'.I('goods_name').'%'];
        $count = M('conver_order')->join('ty_users on ty_users.user_id = ty_conver_order.user_id')->where($where)->count();
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();
        $list = M('conver_order')->join('ty_users on ty_users.user_id = ty_conver_order.user_id')->where($where)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$show);// 赋值分页输出
        return $this->display();
    }
    /*
     * 批量操作
     * */
    public function act(){
        $act = I('post.act', '');
        $goods_ids = I('post.goods_ids');
        $goods_state = I('post.goods_state');
        $return_success = array('status' => 1, 'msg' => '操作成功', 'data' => '');
        if ($act == 'putaway') {
            $hot_condition['id'] = array('in', $goods_ids);
            M('conversion')->where($hot_condition)->save(array('status' => 1));
            $this->ajaxReturn($return_success);
        }
        if ($act == 'sold') {
            $hot_condition['id'] = array('in', $goods_ids);
            M('conversion')->where($hot_condition)->save(array('status' => 2));
            $this->ajaxReturn($return_success);
        }
    }
    /**
     * 初始化编辑器链接
     * 本编辑器参考 地址 http://fex.baidu.com/ueditor/
     */
    private function initEditor()
    {
        $this->assign("URL_upload", U('Admin/Ueditor/imageUp',array('savepath'=>'goods'))); // 图片上传目录
        $this->assign("URL_imageUp", U('Admin/Ueditor/imageUp',array('savepath'=>'article'))); //  不知道啥图片
        $this->assign("URL_fileUp", U('Admin/Ueditor/fileUp',array('savepath'=>'article'))); // 文件上传s
        $this->assign("URL_scrawlUp", U('Admin/Ueditor/scrawlUp',array('savepath'=>'article')));  //  图片流
        $this->assign("URL_getRemoteImage", U('Admin/Ueditor/getRemoteImage',array('savepath'=>'article'))); // 远程图片管理
        $this->assign("URL_imageManager", U('Admin/Ueditor/imageManager',array('savepath'=>'article'))); // 图片管理
        $this->assign("URL_getMovie", U('Admin/Ueditor/getMovie',array('savepath'=>'article'))); // 视频上传
        $this->assign("URL_Home", "");
    }
}
