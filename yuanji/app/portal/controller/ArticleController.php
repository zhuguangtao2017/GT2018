<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;
use app\portal\service\PostService;
use app\portal\model\PortalPostModel;
use think\Db;
use think\Request;
use think\Session;
class ArticleController extends HomeBaseController
{
    public function addbuy(){

		$MerNo="43578";
		//MD5私钥
		$MD5key = "yuanjitituan";
		//订单号
		$BillNo = $this->request->param('BillNo'); 
		//一麻袋支付订单号
		$OrderNo= $this->request->param('OrderNo'); 
		//金额
		$Amount = $this->request->param('Amount');  
		//支付状态
		$Succeed = $this->request->param('Succeed');   
		//支付结果
		$Result = $this->request->param('Result');   
		//取得的MD5校验信息
		$SignInfo = $this->request->param('SignInfo');  
		//备注
		$Remark = $this->request->param('Remark'); 
    	//校验源字符串
         $md5src = "MerNo=".$MerNo."&BillNo=".$BillNo."&OrderNo=".$OrderNo."&Amount=".$Amount."&Succeed=".$Succeed."&".$MD5key;
       //MD5检验结果
	     $md5sign = strtoupper(md5($md5src));

		  if ($SignInfo!=$md5sign){                
				 die();
		  }

		  if($Succeed=='88' && $Result=='SUCCESS'){
		     $uid= $this->request->param('uid', 0, 'intval');
			 $num=Db::name('user')->where(['id'=>$uid])->count();
			 if($num==0){
				die('error');
			 }
			 $uuid=$uid;
			 $num=Db::name('pay')->where('type',1)
			 ->where('uid',$uuid)->count();
		    if($num>0){
		      die('no power');
		    }
			 $articleId  = $this->request->param('id', 0, 'intval');
			  
			 if ($articleId) {
			  	$rs=Db::name('portal_post')->where(['id' => $articleId ])->find();
				$money=$rs['post_source'];//商品价钱
			
				 
				$dizhi=input('ls');//收货地址
				$articleId  = $this->request->param('ls', 0, 'intval');
				//die();
				//$fangshi=$dz['fangshi'];
				$xixi='';
				$panpan='';
				$fspan='';
				$fangshi=input('interface');//支付方式
				//die("yibi");
				$zhanghao=Db::name('payment')->where(['uid'=>$uuid])->find();
				if($fangshi==1)
				{
					$zhanghaos=input('hao');//支付账号
					 $xixi='支付宝('.$zhanghaos.')';
					 $panpan=1;
					 $fspan=1;
				}else if($fangshi==2)
				{
					$zhanghaos=input('hao');//支付账号
					 $xixi='微信('.$zhanghaos.')';
					  $panpan=1;
					  $fspan=2;
				}
				else if($fangshi==3)
				{
					//$my=Db::name('user_money_detail')->where(['uid'=>$uuid])->sum('money');
					
					$xixi='余额支付';
					 $panpan=2;
					 $fspan=3;
				}else if($fangshi==4)
				{
					$yhk=input('yhk');
					$aa='';
					if($yhk==1)
					{
						$aa=$zhanghao['yhk1'];
					}elseif($yhk==2)
					{
						$aa=$zhanghao['yhk2'];
					}
					elseif($yhk==3)
					{
						$aa=$zhanghao['yhk3'];
					}
					$xixi='银行卡('.$aa.')';
					$panpan=1;
					$fspan=4;
				}
				
				$jibaifen=Db::name('baifen')->find();//加钱方式
				$arr=array();
				$j=-1;
				foreach($jibaifen as $va)
				{
					$arr[$j]=$va;
					$j++;
				}
				$uu = ['uid'=>$uuid,'money'=>'-'.$money,'from'=>'','time'=>time(),'type'=>'4','fs'=>$xixi,'pan'=>$panpan,'fspan'=>$fspan];//自己消费记录
				$ddd=Db::name('user_money_detail')->insertGetId($uu);
				//print_r($arr);


				//判断商品
				//$bili=$this->bili($money);
				/*九级分销
				for($i=0;$i<8;$i++){

						$fid=Db::name('user')->where(['id'=>$uid])->find();
						
						
						if(!empty($fid)&&$fid['fid']!='0')
						{
							$rsr=Db::name('user')->where(['id'=>$fid['fid']])->find();
							$uid=$fid['fid'];
							$chaoe=$this->selmoney($uid);//判断是否超额
							if($rsr['user_type']=='5'||$arr[$i]=='0'||$chaoe==false){
								continue;
							}
							
							//$arr[$i]=$rs;
							$moneys=$money*$bili*($arr[$i]/100);//收益金额
							//insert into mingxi ('贡献人,当前人,收入金额') values ($uid,$fid,$moneys); 

							$user = ['uid'=>$fid['fid'],'money'=>$moneys,'from'=>$uuid,'time'=>time(),'type'=>'1'];
							Db::name('user_money_detail')->insert($user);
						}
						else
						{
							break;
						}
						
				} 
				*/
				$sno=$BillNo;
				$aa=Db::name('pay')->insert(['uid'=>$uuid,'pid'=>$rs['id'],'money'=>$money,'pname'=>$rs['post_title'],'time'=>time(),'son'=>$sno,'fhd'=>'','address_id'=>$dizhi,'float_id'=>$ddd,'type'=>"1"]);
 

				if($aa){
					if($money>=3000){
					  $rrr=Db::name('user')->where(['id'=>$uuid])->find();
					
						if($money==50000)
						{
							Db::name('user')->where(['id'=>$uuid])->update(['user_type'=>2]);
							
						}else if($money==30000){
							Db::name('user')->where(['id'=>$uuid])->update(['user_type'=>3]);
							
						}else{
						Db::name('user')->where(['id'=>$uuid])->update(['user_type'=>4]);
						}
					
					}
					echo 'ok';
					//$this->success('购买成功','user/profile/gouwu');
					}
				else
					{
					echo 'fail';
				}
			}
		  
		  }
		  
	
	
	}

	 public function addbuy2(){

		  
		     $uid= $this->request->param('uid', 0, 'intval');
			 $num=Db::name('user')->where(['id'=>$uid])->count();
			 if($num==0){
				die('error');
			 }
			 $uuid=$uid;
			 $articleId  = $this->request->param('id', 0, 'intval');
			  
			 if ($articleId) {
			  	$rs=Db::name('portal_post')->where(['id' => $articleId ])->find();
				$money=$rs['post_source'];//商品价钱
			
				 
				$dizhi=84;//收货地址
				$articleId  = $this->request->param('ls', 0, 'intval');
				//die();
				//$fangshi=$dz['fangshi'];
				$xixi='';
				$panpan='';
				$fspan='';
				$fangshi=1;//支付方式
				//die("yibi");
				$zhanghao=Db::name('payment')->where(['uid'=>$uuid])->find();
				if($fangshi==1)
				{
					$zhanghaos=2123;//支付账号
					 $xixi='支付宝('.$zhanghaos.')';
					 $panpan=1;
					 $fspan=1;
				}else if($fangshi==2)
				{
					$zhanghaos=input('hao');//支付账号
					 $xixi='微信('.$zhanghaos.')';
					  $panpan=1;
					  $fspan=2;
				}
				else if($fangshi==3)
				{
					//$my=Db::name('user_money_detail')->where(['uid'=>$uuid])->sum('money');
					
					$xixi='余额支付';
					 $panpan=2;
					 $fspan=3;
				}else if($fangshi==4)
				{
					$yhk=input('yhk');
					$aa='';
					if($yhk==1)
					{
						$aa=$zhanghao['yhk1'];
					}elseif($yhk==2)
					{
						$aa=$zhanghao['yhk2'];
					}
					elseif($yhk==3)
					{
						$aa=$zhanghao['yhk3'];
					}
					$xixi='银行卡('.$aa.')';
					$panpan=1;
					$fspan=4;
				}
				
				$jibaifen=Db::name('baifen')->find();//加钱方式
				$arr=array();
				$j=-1;
				foreach($jibaifen as $va)
				{
					$arr[$j]=$va;
					$j++;
				}
				$uu = ['uid'=>$uuid,'money'=>'-'.$money,'from'=>'','time'=>time(),'type'=>'4','fs'=>$xixi,'pan'=>$panpan,'fspan'=>$fspan];//自己消费记录
				$ddd=Db::name('user_money_detail')->insertGetId($uu);
				//print_r($arr);


				//判断商品
				//$bili=$this->bili($money);
				/*
				for($i=0;$i<8;$i++){

						$fid=Db::name('user')->where(['id'=>$uid])->find();
						
						
						if(!empty($fid)&&$fid['fid']!='0')
						{
							$rsr=Db::name('user')->where(['id'=>$fid['fid']])->find();
							$uid=$fid['fid'];
							$chaoe=$this->selmoney($uid);//判断是否超额
							if($rsr['user_type']=='5'||$arr[$i]=='0'||$chaoe==false){
								continue;
							}
							
							//$arr[$i]=$rs;
							$moneys=$money*$bili*($arr[$i]/100);//收益金额
							//insert into mingxi ('贡献人,当前人,收入金额') values ($uid,$fid,$moneys); 

							$user = ['uid'=>$fid['fid'],'money'=>$moneys,'from'=>$uuid,'time'=>time(),'type'=>'1'];
							Db::name('user_money_detail')->insert($user);
						}
						else
						{
							break;
						}
						
				} 
				*/
				$sno="SNO".date('Ymd',time()).substr(time(),'-6');
				 
				$aa=Db::name('pay')->insert(['uid'=>$uuid,'pid'=>$rs['id'],'money'=>$money,'pname'=>$rs['post_title'],'time'=>time(),'son'=>$sno,'fhd'=>'','address_id'=>$dizhi,'float_id'=>$ddd,'type'=>"1"]);
				if($aa){
					if($money>=3000){
					  $rrr=Db::name('user')->where(['id'=>$uuid])->find();
					if($rrr['user_type']=='5'){
						Db::name('user')->where(['id'=>$uuid])->update(['user_type'=>4]);
					}
					}
					echo 'ok';
					//$this->success('购买成功','user/profile/gouwu');
					}
				else
					{
					echo 'fail';
				}
			}
		  
		 
	
	
	}

	public function buy(){		

		$uid=cmf_get_current_user_id();
		$uuid=$uid;

		$num=Db::name('pay')->where('type',1)
			 ->where('uid',$uuid)->count();
		 if($num>0){
		   die('no power');
		 }

		if(empty($uuid)){
					$this->success('请先登录！','user/login/index','','2');
					return;
		}
		$articleId  = $this->request->param('id', 0, 'intval');
		if(!empty($articleId)){

			 if ($this->request->isPost()) {
				 $articleId  = $this->request->param('id', 0, 'intval');
				 $rs=Db::name('portal_post')->where(['id' => $articleId])->find();

				 $money=$rs['post_source'];//商品价钱

				 $dizhi=input('ls');//收货地址

				 $rdizhi=Db::name('address')->where(['id' => $dizhi])->find();
				 $dizhi2=$rdizhi['address']."&nbsp;&nbsp;收货人:".$rdizhi['name'].'&nbsp;&nbsp;联系方式:'.$rdizhi['tel'];
				
				 $fangshi=input('interface');//支付方式

				 //支付接口
				     $MD5key = "yuanjitituan";		//MD5私钥
					 $MerNo = "43578";	
				  
				
					$BillNo ="SNO".date('Ymd',time()).substr(time(),'-6');		//[必填]订单号(商户自己产生：要求不重复)
					 $Amount = $money;//$money;				//[必填]订单金额
				   
					 $ReturnURL = "http://".$_SERVER['SERVER_NAME']."/yuanji/public/index.php/portal/article/payresult"; 			//[必填]返回数据给商户的地址(商户自己填写):::注意请在测试前将该地址告诉我方人员;否则测试通不过
					
					 $AdviceURL  = "http://".$_SERVER['SERVER_NAME']."/yuanji/public/index.php/portal/article/addbuy/id/$articleId/ls/$dizhi/interface/$fangshi/uid/$uid"; 	 //[必填]支付完成后，后台接收支付结果，可用来更新数据库值
					 $Remark = "";  //[选填]升级。
					 
					 $OrderTime =date('YmdHis');   //[必填]交易时间YYYYMMDDHHMMSS

					$md5src = "MerNo=".$MerNo. "&BillNo=".$BillNo."&Amount=".$Amount."&OrderTime=".$OrderTime. "&ReturnURL=".$ReturnURL ."&AdviceURL=".$AdviceURL."&".$MD5key ;		//校验源字符串
					 $SignInfo = strtoupper(md5($md5src));		//MD5检验结果

					 
					 //送货信息(方便维护，请尽量收集！如果没有以下信息提供，请传空值:'')
					 //因为关系到风险问题和以后商户升级的需要，如果有相应或相似的内容的一定要收集，实在没有的才赋空值,谢谢。

					$products=$rs['post_title'];// '------------------物品信息


				 //

				 return $this->fetch('/cardpay',['money'=>$money,'MerNo'=>$MerNo,'BillNo'=>$BillNo,'Amount'=>$Amount,'ReturnURL'=>$ReturnURL,'AdviceURL'=>$AdviceURL,'OrderTime'=>$OrderTime,'SignInfo'=>$SignInfo,'Remark'=>$Remark,'products'=>$products,'rs'=>$rs,'dizhi'=>$dizhi2,'zhendizhi'=>$dizhi,'articleId'=>$articleId]);

				 

			 }else{

				$articleId  = $this->request->param('id', 0, 'intval');
				$rs=Db::name('portal_post')->where(['id' => $articleId])->find();
				$money=$rs['post_source'];//商品价钱
			    $zhifubao='';				
				$address=Db::name('address')->where(['uid'=>$uuid])->select();
				$zhanghao=Db::name('payment')->where(['uid'=>$uuid])->find();
				if(!empty($zhanghao)){
				$zhifubao=$zhanghao['zhifubao'];
				}
				return $this->fetch('/buy',['money'=>$money,'adderss'=>$address,'rs'=>$rs,'iid'=>$articleId,'zhanghao'=>$zhanghao,'zhifubao'=>$zhifubao]);
			 }
			 
		}else
		{
			echo "错误";
		}
	//echo 131;
	
	
	}
	public function yuepay()
	{
		$uid=cmf_get_current_user_id();
		
		$num=Db::name('pay')->where('type',1)
			 ->where('uid',$uid)->count();
		 if($num>0){
		   die('no power');
		 }

		$address=$this->request->param('zhendizhi');;
		$BillNo = $this->request->param('BillNo');
		//支付订单号
		$OrderNo= $this->request->param('OrderNo'); 
		//金额
		$money = $this->request->param('Amount');


		$articleId = $this->request->param('articleId');
		
		if(!empty($money))
		{
			Session::set("yue_money",$money);
		}
		if(!empty($address))
		{
			Session::set("yue_dizhi",$address);
		}
		if(!empty($BillNo))
		{
			Session::set("yue_son",$BillNo);
		}
		if(!empty($articleId))
		{
			Session::set("yue_shangid",$articleId);
		}
		if (!empty(input("zhimoney"))&&!empty(input("shangid"))) {
			$zhimoney=input("zhimoney");//支付金额
			$dingdan=input("son");//订单号
			$shangid=input("shangid");//商品id
			$dizhi=input("adderss");//地址id
			$uid=cmf_get_current_user_id();
			$uuid=$uid;
			//echo Session::get('yue_money');
			$zongyu=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->sum('money');//总余额
			if($zongyu-$zhimoney<0)
			{
				die("错误！");
			}
				$jibaifen=Db::name('baifen')->find();//加钱方式
				$arr=array();
				$j=-1;
				foreach($jibaifen as $va)
				{
					$arr[$j]=$va;
					$j++;
				}
				$uu = ['uid'=>$uuid,'money'=>'-'.$zhimoney,'from'=>'','time'=>time(),'type'=>'4','fs'=>'余额','pan'=>2,'fspan'=>3];//自己消费记录
				$ddd=Db::name('user_money_detail')->insertGetId($uu);
				//print_r($arr);

				//判断商品
				/*$bili=$this->bili($zhimoney);
				
				for($i=0;$i<8;$i++){

						$fid=Db::name('user')->where(['id'=>$uid])->find();
						
						
						if(!empty($fid)&&$fid['fid']!='0')
						{
							$rsr=Db::name('user')->where(['id'=>$fid['fid']])->find();
							$uid=$fid['fid'];
							$chaoe=$this->selmoney($uid);//判断是否超额
							if($rsr['user_type']=='5'||$arr[$i]=='0'||$chaoe==false){
								continue;
							}
							
							//$arr[$i]=$rs;
							$moneys=$zhimoney*$bili*($arr[$i]/100);//收益金额
							//insert into mingxi ('贡献人,当前人,收入金额') values ($uid,$fid,$moneys); 

							$user = ['uid'=>$fid['fid'],'money'=>$moneys,'from'=>$uuid,'time'=>time(),'type'=>'1'];
							Db::name('user_money_detail')->insert($user);
						}
						else
						{
							break;
						}
						
				}*/ 
				$sno=$dingdan;
				 $rs=Db::name('portal_post')->where(['id' =>$shangid])->find();
				$aa=Db::name('pay')->insert(['uid'=>$uuid,'pid'=>$shangid,'money'=>$zhimoney,'pname'=>$rs['post_title'],'time'=>time(),'son'=>$sno,'fhd'=>'','address_id'=>$dizhi,'float_id'=>$ddd,'type'=>"1"]);
				if($aa){
					if($zhimoney>=3000){
					  $rrr=Db::name('user')->where(['id'=>$uuid])->find();
					
						if($zhimoney==50000)
						{
							Db::name('user')->where(['id'=>$uuid])->update(['user_type'=>2]);
							return $this->success('购买成功,你已成为省代,请重新登录','user/Index/logout',"",3);
						}else if($zhimoney==30000){
							Db::name('user')->where(['id'=>$uuid])->update(['user_type'=>3]);
							return $this->success('购买成功,你已成为代理,请重新登录','user/Index/logout',"",3);
						}else{
						Db::name('user')->where(['id'=>$uuid])->update(['user_type'=>4]);
						}
					
					}
					//echo 'ok';
					$this->success('购买成功','user/profile/gouwu');
					}
				else
					{
					echo 'fail';
				}
		}
		else
		{
			$zongyu=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->sum('money');
			return $this->fetch('/yuepay',['money'=>Session::get('yue_money'),'adderss'=>Session::get('yue_dizhi'),'zongyu'=>$zongyu,'BillNo'=>Session::get('yue_son'),'articleId'=>Session::get('yue_shangid')]);
		}
	}
	public function payresult(){
	   $this->success('购买成功','user/profile/gouwu');
	}
	public function index()
    {

        $portalCategoryModel = new PortalCategoryModel();
        $postService         = new PostService();

        $articleId  = $this->request->param('id', 0, 'intval');
        $categoryId = $this->request->param('cid', 0, 'intval');
        $article    = $postService->publishedArticle($articleId, $categoryId);

        if (empty($articleId)) {
            abort(404, '文章不存在!');
        }


        $prevArticle = $postService->publishedPrevArticle($articleId, $categoryId);
        $nextArticle = $postService->publishedNextArticle($articleId, $categoryId);

        $tplName = 'article';

        if (empty($categoryId)) {
            $categories = $article['categories'];

            if (count($categories) > 0) {
                $this->assign('category', $categories[0]);
            } else {
                abort(404, '文章未指定分类!');
            }

        } else {
            $category = $portalCategoryModel->where('id', $categoryId)->where('status', 1)->find();
 

            if (empty($category)) {
                abort(404, '文章不存在!');
            }

            $this->assign('category', $category);

            $tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
        }

        Db::name('portal_post')->where(['id' => $articleId])->setInc('post_hits');


        hook('portal_before_assign_article', $article);

        $this->assign('article', $article);
        $this->assign('prev_article', $prevArticle);
        $this->assign('next_article', $nextArticle);

        $tplName = empty($article['more']['template']) ? $tplName : $article['more']['template'];

        return $this->fetch("/$tplName");
    }

    // 文章点赞
    public function doLike()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id', 0, 'intval');


        $canLike = cmf_check_user_action("posts$articleId", 1);

        if ($canLike) {
            Db::name('portal_post')->where(['id' => $articleId])->setInc('post_like');

            $this->success("赞好啦！");
        } else {
            $this->error("您已赞过啦！");
        }
    }

    public function myIndex()
    {
        //获取登录会员信息
        $user = cmf_get_current_user();
        $this->assign('user_id', $user['id']);
        return $this->fetch('user/index');
    }

    //用户添加
    public function add()
    {
        return $this->fetch('user/add');
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $post   = $data['post'];
            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $portalPostModel = new PortalPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }
            $portalPostModel->adminAddArticle($data['post'], $data['post']['categories']);

            $this->success('添加成功!', url('Article/myIndex', ['id' => $portalPostModel->id]));
        }
    }

    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $portalCategoryModel = new PortalCategoryModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
                               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
    <td>\$description</td>
</tr>
tpl;

        $categoryTree = $portalCategoryModel->adminCategoryTableTree($selectedIds, $tpl);

        $where      = ['delete_time' => 0];
        $categories = $portalCategoryModel->where($where)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch('user/select');
    }
	public function aj()
	{
		$uid=cmf_get_current_user_id();
		$lei=input('lei');
		$zhanghao=Db::name('payment')->where(['uid'=>$uid])->find();
		if($lei=='1')
		{
			$str='<td>账号：</td>						
						<td><input type="text" id="hao" value="'.$zhanghao['zhifubao'].'" name="hao" id="hao" style="margin-top:15px"></td>';
		}else if($lei=='2')
		{
			$str='<td>账号：</td>						
						<td><input type="text" id="hao" value="'.$zhanghao['weixin'].'" name="hao" id="hao" style="margin-top:15px"></td>';
		}
		else if($lei=='3')
		{
			$my=Db::name('user_money_detail')->where(['uid'=>$uid])->where('pan','neq','1')->sum('money');
			$str='<td>余额：</td>						
						<td><input type="text" value="'.$my.'" name="zhanghao" id="hao" style="margin-top:15px" disabled="disabled"></td>';
		}else if($lei=='4')
		{		
				$zz=!empty($zhanghao['yhk1'])?'<input type="radio" value=1 name="yhk" id="hao" class="yh">'.$zhanghao['yhk1'].'<br>':'';	
				$cc=!empty($zhanghao['yhk2'])?'<input type="radio" value=2 name="yhk" id="hao" class="yh">'.$zhanghao['yhk2'].'<br>':'';
				$xx=!empty($zhanghao['yhk3'])?'<input type="radio" value=3 name="yhk" id="hao" class="yh">'.$zhanghao['yhk3'].'<br>':'';
			if($zz==''&&$cc==''&&$xx==''){
				$str='<td class="fieldVal" valign="middle" style="border:0px solid #eee;">银行卡：</td>						
						<td>暂无银行卡！</td>';
			}else
			{
			$str='<td>银行卡：</td>						
						<td class="fieldVal" valign="middle" style="border:0px solid #eee;">'.$zz.$cc.$xx.'</td>';
			}
		}
		return $str;
	}

	//判断是否超出收益
	public function selmoney($id)
	{
		$pay=Db::name("pay")->where("uid",$id)->where("type","1")->find();
		
		
		if(!empty($pay))
		{
			 $shouyi=Db::table("cmf_user_money_detail")->where("uid",$id)->where("type",['=',1],['=',5],'or')->where("time",">=",$pay['time'])->sum("money");
			 $abs=Db::name("abonus")->where("price",$pay['money'])->find();
			 //print_r($abs);
			  $moneyy=$abs['multiple']*$pay['money'];
			  if($shouyi>=$moneyy)
			{
				Db::name('pay')->where(['id'=>$pay['id']])->update(['type'=>2]);
				return false;
			}
			else
			{
				return true;
			}
		}else
		{
			return true;
		}

	}

	//根据钱取得比例
	public function bili($money)
	{
		 $abs=Db::name("abonus")->where("price",$money)->find();
		 $bili=$abs['multiple']*$abs['abonus']/100;	
		 return $bili;				
	}
}
