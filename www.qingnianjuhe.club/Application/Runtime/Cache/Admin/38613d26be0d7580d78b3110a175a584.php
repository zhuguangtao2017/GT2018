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
        	<div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 优惠券列表</h3>
                </div>
                <div class="panel-body">
	                <div class="navbar navbar-default">
	                	<form class="navbar-form form-inline" action="<?php echo U('Admin/Coupon/index');?>"  method="post">
                            <div class="form-group">
                                <select name="type" class="input-sm" style="width:100px;">
                                    <option value="0">全局</option>
                                    <option value="1">分销商</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="time" class="input-sm" style="width:100px;">
                                    <option value="0">使用中</option>
                                    <option value="1">已过期</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="order" class="input-sm" style="width:100px;">
                                    <option value="0">时间倒序</option>
                                    <option value="1">时间正序</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary"><i class="fa fa-search"></i> 筛选</button>
                            </div>
				            <div class="form-group pull-right">
					            <a href="<?php echo U('Admin/Coupon/coupon_info');?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加优惠券</a>
				            </div>		          
			          </form>
	                </div>
                    <div id="ajax_return">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>
                                    <td class="text-center">ID</td>
                                    <td class="text-center">优惠券名称</td>
                                    <!--<td class="text-center">商家名称</td>-->
                                    <td class="text-center">优惠券类型</td>
                                    <td class="text-center">所属分销商ID</td>
                                    <td class="text-center">面额</td>
                                    <td class="text-center">使用需满金额</td>
                                    <td class="text-center">总发行量</td>
                                    <!--<td class="text-center">已发放数</td>-->
                                    <td class="text-center">已使用</td>
                                    <td class="text-center">使用率</td>
                                    <td class="text-center">使用截止日期</td>
                                    <td class="text-center">操作</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="selected[]" value="6">
                                        </td>
                                        <td class="text-center"><?php echo ($list["id"]); ?></td>
                                        <td class="text-center"><?php echo ($list["name"]); ?></td>
                                        <!--<td class="text-center"><?php echo ($store[$list[store_id]]); ?></td>-->
                                        <td class="text-center"><?php echo ($coupons[$list[type]]); ?></td>
                                        <td class="text-center">
                                        <?php if($list["type"] == 0 ): ?>通用<?php elseif($list["type"] == 1): ?> <?php echo ($list["store_id"]); ?> </elseif><?php endif; ?>

                                    </td>
                                        <td class="text-center"><?php echo ($list["money"]); ?></td>
                                        <td class="text-center"><?php echo ($list["condition"]); ?></td>
                                        <td class="text-center"><?php echo ($list["createnum"]); ?></td>
                                        <!--<td class="text-center"><?php echo ($list["send_num"]); ?></td>-->
                                        <td class="text-center"><?php echo ($list["use_num"]); ?></td>
                                        <td class="text-center"><?php echo ($list["usage"]); ?>%</td>
                                        <td class="text-center"><?php echo (date('Y-m-d',$list["use_end_time"])); ?></td>
                                        <td class="text-center">
                                            <!--<a href="<?php echo U('Admin/Coupon/coupon_list',array('id'=>$list['id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看"><i class="fa fa-eye"></i></a>-->
                                            <a href="<?php echo U('Admin/Coupon/coupon_info',array('id'=>$list['id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="编辑"><i class="fa fa-pencil"></i></a>
                                            <a data-url="<?php echo U('Admin/Coupon/del_coupon',array('id'=>$list['id']));?>" onclick="delfun(this)" href="javascript:;" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a></td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
				 <div class="row">
              	    <div class="col-sm-6 text-left"></div>
                    <div class="col-sm-6 text-right"><?php echo ($page); ?></div>		
	              </div>                    
				  </div>
                </div>
            </div>
        </div>        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<script>
$('.send_user').click(function(){
    var url = $(this).attr('data-url');
    layer.open({
        type: 2,
        title: '发放优惠券',
        shadeClose: true,
        shade: 0.5,
        area: ['70%', '85%'],
        content: url, 
    });
});

function delfun(obj){
	if(confirm('确认删除')){		
		$.ajax({
			type : 'post',
			url : $(obj).attr('data-url'),
			dataType : 'json',
			success : function(data){
				if(data){
					$(obj).parent().parent().remove();
				}else{
					layer.alert('删除失败', {icon: 2});  //alert('删除失败');
				}
			}
		})
	}
	return false;
}
</script>
</body>
</html>