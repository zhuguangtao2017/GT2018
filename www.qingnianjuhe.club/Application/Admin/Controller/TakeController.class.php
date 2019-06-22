<?php
/**
 * Created by PhpStorm.
 * User: 'zhuGuangTao'
 * Date: 2018/5/23
 * Time: 20:54
 */

namespace Admin\Controller;

class TakeController extends BaseController
{
    public function index(){
        $arr = M('take')->select();
        $this->assign('arr',$arr);
        $this->display();
    }
    public function add_shouhuo(){
        if(I('get.id')){
            $vo = M('take')->find(I('get.id'));
            $this->assign('vo',$vo);
        }
        if(I('take_name')){
            if(I('hid')){
                M('take')->where(['id'=>I('hid')])->save(['take_name'=>I('take_name'),'create_time'=>time()]);
            }else{
                M('take')->add(['take_name'=>I('take_name'),'create_time'=>time()]);
            }
            exit($this->success('操作成功',U('take/index')));
        }
        $this->display();
    }
    public function timIndex(){
        $arr = M('shoutime')->select();
        $this->assign('arr',$arr);
        $this->display();
    }
    public function add_time(){
        if(I('get.id')){
            $vo = M('shoutime')->find(I('get.id'));
            $this->assign('vo',$vo);
        }
        if(I('start_time')){
            if(I('hid')){
                M('shoutime')->where(['id'=>I('hid')])->save(['start_time'=>I('start_time'),'end_time'=>I('end_time'),'create_time'=>time()]);
            }else{
                M('shoutime')->add(['start_time'=>I('start_time'),'end_time'=>I('end_time'),'create_time'=>time()]);
            }
            exit($this->success('操作成功',U('take/timIndex')));
        }
        $this->display();
    }
    public function timeDel(){
        M('shoutime')->delete(I('id'));
        exit($this->success('操作成功',U('take/timIndex')));
    }
    public function shouDel(){
        M('take')->delete(I('id'));
        exit($this->success('操作成功',U('take/Index')));
    }
    public function panduan()
    {
        $date = date("H:i");
        if ($date >= '9:00:' && $date <= '10:00') {
            echo "零点零距离";
        } elseif ($date >= '21:30:' && $date <= '22:00') {
            echo "长夜的牵引";
        } elseif ($date >= '23:00:' && $date <= '23:59') {
            echo "灵命日粮";
        }
    }
}