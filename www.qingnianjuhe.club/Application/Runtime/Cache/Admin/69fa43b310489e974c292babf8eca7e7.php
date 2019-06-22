<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>小程序管理后台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
 	<link href="/Public/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
    	folder instead of downloading all of them to reduce the load. -->
    <link href="/Public/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="/Public/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />   
    <!-- jQuery 2.1.4 -->
    <script src="/Public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<script src="/Public/js/global.js"></script>
    <script src="/Public/js/myFormValidate.js"></script>    
    <script src="/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/Public/js/layer/layer-min.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
    <script src="/Public/js/myAjax.js"></script>
    <script type="text/javascript">
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
    		    // 确定
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
   						if(data==1){
   							layer.msg('操作成功', {icon: 1});
   							$(obj).parent().parent().remove();
   						}else{
   							layer.msg(data, {icon: 2,time: 2000});
   						}
//   						layer.closeAll();
   					}
   				})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);
    }
    
    function selectAll(name,obj){
    	$('input[name*='+name+']').prop('checked', $(obj).checked);
    }   
    
    function get_help(obj){
        layer.open({
            type: 2,
            title: '帮助手册',
            shadeClose: true,
            shade: 0.3,
            area: ['70%', '80%'],
            content: $(obj).attr('data-url'), 
        });
    }
    </script>        
  </head>
  <body style="background-color:#ecf0f5;">
 

<div class="wrapper">
     <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>

     <section class="content">
        
        <div class="row">
            <div class="col-md-12">
              <div class="box box-info">
                <div class="box-header">
                  <h3 class="box-title">今日统计</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-2 col-xs-6">
                            新增订单：<?php echo ($count["new_order"]); ?>
                        </div>
                            <div class="col-sm-2 col-xs-6">
                            今日访问：<?php echo ($count["today_login"]); ?>
                        </div>
                            <div class="col-sm-2 col-xs-6">
                            新增会员：<?php echo ($count["new_users"]); ?>
                        </div>
                            <div class="col-sm-2 col-xs-6">
                             待审商品:<?php echo ($count['wGoods']); ?>
                                </div>
                         <div class="col-sm-2 col-xs-6">
                                     库存不足: <?php echo ($count['goods']['kucun']); ?>
                         </div>
                    </div>

                </div>
              </div>
            </div>
        </div>
          <div class="row">
                 <div class="col-md-12">
                     <div class="box  box-primary">
                        <div class="box-body">
                            <div class="info-box">                 
                                <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>服务器操作系统：</td>
                                    <td><?php echo ($sys_info["os"]); ?></td>
                                    <td>服务器域名/IP：</td>
                                    <td><?php echo ($sys_info["domain"]); ?> [ <?php echo ($sys_info["ip"]); ?> ]</td> 
                                    <td>服务器环境：</td> 
                                    <td><?php echo ($sys_info["web_server"]); ?></td>       
                                </tr> 
                                <tr>
                                    <td>PHP 版本：</td>
                                    <td><?php echo ($sys_info["phpv"]); ?></td>
                                    <td>Mysql 版本：</td>
                                    <td><?php echo ($sys_info["mysql_version"]); ?></td> 
                                    <td>GD 版本</td> 
                                    <td><?php echo ($sys_info["gdinfo"]); ?></td>  
                                </tr>   
                                <tr>
                                    <td>文件上传限制：</td>
                                    <td><?php echo ($sys_info["fileupload"]); ?></td>
                                    <td>最大占用内存：</td>
                                    <td><?php echo ($sys_info["memory_limit"]); ?></td> 
                                    <td>最大执行时间：</td> 
                                    <td><?php echo ($sys_info["max_ex_time"]); ?></td>  
                                </tr>  
                                <tr>
                                    <td>安全模式：</td>
                                    <td><?php echo ($sys_info["safe_mode"]); ?></td>
                                    <td>Zlib支持：</td>
                                    <td><?php echo ($sys_info["zlib"]); ?></td> 
                                    <td>Curl支持：</td> 
                                    <td><?php echo ($sys_info["curl"]); ?></td>  
                                </tr>  
                                </table>                
                            </div>
                        </div>
                    </div>
                </div>
          </div>


     </section>
 </div>
 </body>
 </html>