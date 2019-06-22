<?php
namespace Admin\Controller;


class IndexController extends BaseController {

    public function index(){
        $this->pushVersion();
        $act_list = session('act_list');
        $menu_list = getMenuList($act_list);
        $json =  json_encode($menu_list);
        $this->assign('menu_list',$menu_list);
        $admin_info = getAdminInfo(session('admin_id'));
        $order_amount = M('order')->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();
        $this->assign('order_amount',$order_amount);
        $this->assign('admin_info',$admin_info);
        $this->display();
    }
   
    public function welcome(){
        $this->assign('sys_info',$this->get_sys_info());
        $today = strtotime("-1 day");
        $count['handle_order'] = M('order')->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();//待处理订单
        $count['new_order'] = M('order')->where("add_time>$today")->count();//今天新增订单
        $xxx = 10;
        $count['goods']['kucun'] =  M('spec_goods_price')->where("store_count<$xxx")->count();//库存不足总数


        $count['article'] =  M('article')->where("1=1")->count();//文章总数
        $count['users'] = M('users')->where("1=1")->count();//会员总数
        $count['today_login'] = M('users')->where("last_login>$today")->count();//今日访问
    $count['wGoods'] = COUNT(M('Goods') -> where(['goods_state' => 0]) -> select());//待审商品
        $count['new_users'] = M('users')->where("reg_time>$today")->count();//新增会员
        $count['comment'] = M('comment')->where("is_show=0")->count();//最新评论
        $count['store'] = M('store_apply')->where("apply_state=0")->count();//店铺审核
        $count['bind_class'] = M('store_bind_class')->where("state=0")->count();//申请经营类目
        $count['brand'] = M('brand')->where("status=0 and store_id>0")->count();//申请品牌
        $this->assign('count',$count);
        $this->display();
    }
    
    public function get_sys_info(){
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
        $sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'YES';//safe_mode = Off
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : 'NO';  
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']           = phpversion();
        $sys_info['ip']             = GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
        $sys_info['max_ex_time']    = @ini_get("max_execution_time").'s'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain']         = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit']   = ini_get('memory_limit');      
        $sys_info['version']        = file_get_contents('./Application/Admin/Conf/version.txt');
        $mysqlinfo = M()->query("SELECT VERSION() as version");
        $sys_info['mysql_version']  = $mysqlinfo['version'];
        if(function_exists("gd_info")){
            $gd = gd_info();
            $sys_info['gdinfo']     = $gd['GD Version'];
        }else {
            $sys_info['gdinfo']     = "未知";
        }
        return $sys_info;
    }
    
    
    public function pushVersion()
    {            
        if(!empty($_SESSION['isset_push']))
            return false;    
        $_SESSION['isset_push'] = 1;    
        error_reporting(0);//关闭所有错误报告
        $app_path = dirname($_SERVER['SCRIPT_FILENAME']).'/';
        $version_txt_path = $app_path.'/Application/Admin/Conf/version.txt';
        $curent_version = file_get_contents($version_txt_path);

        $vaules = array(            
                'domain'=>$_SERVER['SERVER_NAME'], 
                'last_domain'=>$_SERVER['SERVER_NAME'], 
                'key_num'=>$curent_version, 
                'install_time'=>INSTALL_DATE,
                'serial_number'=>SERIALNUMBER,
         );     
         $url = "http://service.tp-shop.cn/index.php?m=Home&c=Index&a=user_push&".http_build_query($vaules);
         stream_context_set_default(array('http' => array('timeout' => 3)));
         file_get_contents($url);         
    }
    
    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
    public function changeTableVal(){  
            $table = I('table'); // 表名
            $id_name = I('id_name'); // 表主键id名
            $id_value = I('id_value'); // 表主键id值
            $field  = I('field'); // 修改哪个字段
            $value  = I('value'); // 修改字段值                        
            M($table)->where("$id_name = $id_value")->save(array($field=>$value)); // 根据条件保存修改的数据
    }     
        /*
     * 获取商品分类
     */
    public function get_category(){
        $parent_id = I('get.parent_id',0); // 商品分类 父id  
        empty($parent_id) && exit('');
        $list = M('goods_category')->where(array('parent_id'=>$parent_id))->select();
        // 店铺id
         $store_id = session('store_id');

        //如果店铺登录了
        if($store_id)
        {
            $store = M('store')->where("store_id = $store_id")->find();
               
            if($store['bind_all_gc'] == 0)
            {                            
                $class_id1 = M('store_bind_class')->where("store_id = $store_id and state = 1")->getField('class_1',true);
                $class_id2 = M('store_bind_class')->where("store_id = $store_id and state = 1")->getField('class_2',true);
                $class_id3 = M('store_bind_class')->where("store_id = $store_id and state = 1")->getField('class_3',true);
                $class_id = array_merge($class_id1,$class_id2,$class_id3);
                $class_id = array_unique($class_id);          
            }
        }
        foreach ($list as $k=>$v){
            if($v['level']==3 && count(explode('_',$v['parent_id_path']))==3)
                $list[$k]['name'] = $v['name'].'&nbsp;&nbsp;<b>(直属品项)</b>';
        }
        foreach($list as $k => $v)
        {
            // 如果是某个店铺登录的, 那么这个店铺只能看到自己申请的分类,其余的看不到
            if($class_id && !in_array($v['id'],$class_id))
                continue;
            $html .= "<option value='{$v['id']}' rel='{$v['commission']}'>{$v['mobile_name']}</option>";
        }

        exit($html);
    }    
    public function insterMenu(){
        $list = getAllMenu();
        foreach ($list as $k => $v){
            M('menu_list')->add([
                'name' => $v['name'],
                'icon' => $v['icon'],
                'identify' => $k
            ]);
            $id = M('menu_list')->getLastInsID();
            foreach ($v['sub_menu'] as $k1 => $v1){
                M('menu_list')->add([
                    'sub_menu_name' => $v1['name'],
                    'sub_menu_act'  => $v1['act'],
                    'sub_menu_control' => $v1['control'],
                    'parent_id' => $id
                ]);
            }
        }
    }
}
