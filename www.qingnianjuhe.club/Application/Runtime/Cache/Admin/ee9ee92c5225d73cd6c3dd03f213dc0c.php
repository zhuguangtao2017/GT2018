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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 用户列表</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                            <form action="" id="search-form2" class="navbar-form form-inline" method="post" onsubmit="return false">
                                <div class="form-group">
                                    <label class="control-label" for="input-mobile">手机号码</label>
                                    <div class="input-group">
                                        <input type="text" name="mobile" value="" placeholder="手机号码" id="input-mobile" class="form-control">
                                    </div>
                                </div>

<!--                                 <div class="form-group">
                                    <label class="control-label" for="input-email">email</label>
                                    <div class="input-group">
                                        <input type="text" name="email" value="" placeholder="email" id="input-email" class="form-control">
                                    </div>
                                </div> -->
                                 <div class="form-group">
                                    <input type="hidden" name="order_by" value="user_id">
                                	<input type="hidden" name="sort" value="desc">
                                	<button type="submit" onclick="ajax_get_table('search-form2',1)" id="button-filter search-order" class="btn btn-primary pull-right"><i class="fa fa-search"></i> 筛选</button>
                                 </div>
                                <!-- <button type="button" onclick="send_message(0);" class="btn btn-primary"><i class="fa"></i> 发送站内信</button>
                                <button type="button" onclick="send_mail();" class="btn btn-primary"><i class="fa"></i> 发送邮箱</button> -->
								 <a href="<?php echo U('User/add_user');?>" class="btn btn-info pull-right">添加会员</a>
                            </form>
                    </div>
                    <div id="ajax_return">

                    </div>
                </div>
            </div>
        </div>        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    $(document).ready(function(){
        ajax_get_table('search-form2',1);

    });

    // ajax 抓取页面
    function ajax_get_table(tab,page){
        cur_page = page; //当前页面 保存为全局变量
            $.ajax({
                type : "POST",
                url:"/index.php/Admin/user/ajaxlock/p/"+page,//+tab,
                data : $('#'+tab).serialize(),// 你的formid
                success: function(data){
                    $("#ajax_return").html('');
                    $("#ajax_return").append(data);
                }
            });
    }

    // 点击排序
    function sort(field)
    {
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2',cur_page);
    }

    // //发送站内信
    // function send_message(id)
    // {
    //     var obj = $("input[name*='selected']");
    //     var url = "<?php echo U('Admin/User/sendMessage');?>";
    //     if(obj.is(":checked")){
    //         var check_val = [];
    //         for(var k in obj){
    //             if(obj[k].checked)
    //                 check_val.push(obj[k].value);
    //         }
    //         url += "?user_id_array="+check_val;
    //     }
    //     layer.open({
    //         type: 2,
    //         title: '站内信',
    //         shadeClose: true,
    //         shade: 0.8,
    //         area: ['580px', '480px'],
    //         content: url
    //     });
    // }

    // //发送邮件
    // function send_mail()
    // {
    //     var obj = $("input[name*='selected']");
    //     var url = "<?php echo U('Admin/User/sendMail');?>";
    //     if(obj.is(":checked")){
    //         var check_val = [];
    //         for(var k in obj){
    //             if(obj[k].checked)
    //                 check_val.push(obj[k].value);
    //         }
    //         url += "?user_id_array="+check_val;
    //         layer.open({
    //             type: 2,
    //             title: '发送邮箱',
    //             shadeClose: true,
    //             shade: 0.8,
    //             area: ['580px', '480px'],
    //             content: url
    //         });
    //     }else{
    //         layer.msg('请选择会员');
    //     }

    // }

    /**
     * 回调函数
     */
    function call_back(v) {
        layer.closeAll();
        if (v == 1) {
            layer.msg('发送成功');
        } else {
            layer.msg('发送失败');
        }
    }
</script>
</body>
</html>