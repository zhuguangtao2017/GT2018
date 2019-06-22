<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace Home\Controller;
use Home\Logic\ArticleLogic;

class ArticleController extends BaseController {
    public $a = '';
    public function index(){       
        $article_id = I('article_id',38);
    	$article = D('article')->where("article_id=$article_id")->find();
    	$this->assign('article',$article);
        $this->display();
    }
    public function ang(){
        $redis = new \Redis();
        $redis -> connect('127.0.0.1',6379);
        $name = I('goods_id').','.I('num');
        $redis -> set($name,'123');
        //$redis->delete('name');
        $redis->delete('*');
        $b = $redis->keys('*');
        $arr = '';
       foreach ($b as $val){
           $arr.= $redis->get($val);
       }
       echo $arr;
    }
    public static function ad($b){
        static $A = '';
        $A.=$b;
        return $A;
    }
    public function aa(){
       // $_SESSION['num'] .= I('str');
       // session('num',null);
        //echo $_SESSION['num'].'<br/>';
        $a = 'a';
         $_SESSION[$a] = 'b';
        dump($_SESSION['a']);

        $arr = array_filter(explode(',',$_SESSION['num']));
       echo serialize($arr);
        $wxPay = M('plugin')->where(array('type'=>'payment','code'=>'weixin'))->find();
       $config = array(
            'appid' => APP_ID,    /*微信小程序应用id*/
            'mch_id' => MCHID,   /*微信申请成功之后邮件中的商户id*/
            'key' => Key,    /*在微信商户平台上自己设定的api密钥 32位*/
            'appsecret' => APP_SECRET /*自定义的回调程序地址id*/
        );
        echo $ser = serialize($config);
        $wxPay = M('plugin')->where(array('type'=>'payment','code'=>'weixin'))->save(['config_value'=>$ser]);
        $this->display();
    }
    public function upload(){
        if($_FILES[file][tmp_name][0])
        {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =    $map['author'] = (1024*1024*3);// 设置附件上传大小 管理员10M  否则 3M
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Public/upload/comment/'; // 设置附件上传根目录
            $upload->replace  =     true; // 存在同名文件是否是覆盖，默认为false
            //$upload->saveName  =   'file_'.$id; // 存在同名文件是否是覆盖，默认为false
            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                exit(json_encode(array('status'=>-1,'msg'=>$upload->getError()))); //$this->error($upload->getError());
            }else{
                foreach($info as $key => $val)
                {
                    $comment_img[] = '/Public/upload/comment/'.$val['savepath'].$val['savename'];
                }
                $comment_img = json_encode($comment_img); // 上传的图片文件
                exit($comment_img);
            }
        }else{
            exit(json_encode(['status'=>-1,'msg'=>'未选择图片']));
        }
       /* if(empty($_FILES)){

        }*/
    }
    /**
     * 文章内列表页
     */
    public function articleList(){
        $article_cat = M('ArticleCat')->where("parent_id  = 0")->select();
        $this->assign('article_cat',$article_cat);        
        $this->display();
    }    
    /**
     * 文章内容页
     */
    public function detail(){
    	$article_id = I('article_id',1);
    	$article = D('article')->where("article_id=$article_id")->find();
    	if($article){
    		$parent = D('article_cat')->where("cat_id=".$article['cat_id'])->find();
    		$this->assign('cat_name',$parent['cat_name']);
    		$this->assign('article',$article);
    	}
        $this->display();
    } 
   
}