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
 

<link href="/Public/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<script src="/Public/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="/Public/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<div class="wrapper">
  <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>

  <section class="content">
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i>提现申请</h3>
        </div>
        <div class="panel-body">    
		<div class="navbar navbar-default">                    
                <form id="search-form2" class="navbar-form form-inline"  method="post" action="<?php echo U('/Admin/Distribut/withdrawals');?>">
                <div class="form-group">
                  <label for="input-order-id" class="control-label">状态:</label>
                <div class="form-group">
                  <select class="form-control" id="status" name="status">
                    <option value="">全部</option>
                    <option value="0"<?php if($_REQUEST['status'] === '0'): ?>selected<?php endif; ?>>申请中</option>
                    <option value="1"<?php if($_REQUEST['status'] == 1): ?>selected<?php endif; ?>>申请成功</option>
                    <option value="2"<?php if($_REQUEST['status'] == 2): ?>selected<?php endif; ?>>申请失败</option>
                  </select>
                </div>
                  <!--<label for="input-order-id" class="control-label">用户ID:</label>-->
                  <!--<div class="input-group">-->
                    <!--<input type="text" class="form-control" id="user_id" placeholder="用户id" value="<?php echo ($_REQUEST['user_id']); ?>" name="user_id" />-->
                  <!--</div>-->
                  
                  <!--<label for="input-order-id" class="control-label">收款账号:</label>                -->
                  <!--<div class="input-group">-->
                    <!--<input type="text" class="form-control" id="input-order-id" placeholder="收款账号" value="<?php echo ($_REQUEST['account_bank']); ?>" name="account_bank" />                    -->
                  <!--</div>-->
                  <!---->
                  <!--<label for="input-order-id" class="control-label">收款账户名:</label>                -->
                  <!--<div class="input-group">-->
                    <!--<input type="text" class="form-control" id="input-order-id" placeholder="收款账户名" value="<?php echo ($_REQUEST['account_name']); ?>" name="account_name" />                    -->
                  <!--</div>-->
                  
                   <!--<div class="input-group margin">                    -->
                    <!--<div class="input-group-addon">-->
                        <!--申请时间<i class="fa fa-calendar"></i>-->
                    <!--</div>-->
                       <!--<input type="text" id="start_time" value="<?php echo ($create_time); ?>" name="create_time" class="form-control pull-right">-->
                  <!--</div>                  -->
                <!--</div>-->
                <div class="form-group">
                    <button class="btn btn-primary" id="button-filter search-order" type="submit"><i class="fa fa-search"></i> 筛选</button>
                </div>
                </form>
          </div>
                        
          <div id="ajax_return"> 
                 
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="sorting text-left">ID</th>
                                <th class="sorting text-left">用户id</th>
                                <th class="sorting text-left">微信名称</th>
                                <th class="sorting text-left">微信号</th>
                                <th class="sorting text-left">申请时间</th>                                                                
                                <th class="sorting text-left">申请金额</th>
                                <th class="sorting text-left">申请类型</th>

                                <th class="sorting text-left">状态</th>
                                <th class="sorting text-left">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                    <td class="text-left"><?php echo ($v["id"]); ?></td>                                   
                                    <td class="text-left">
                                        <a href="<?php echo U('Admin/user/detail',array('id'=>$v[user_id]));?>">
                                             <?php echo ($v["user_id"]); ?>
                                        </a>
                                    </td>
                                    <td class="text-left"><?php echo ($v["nickname"]); ?></td>
                                    <td class="text-left"><?php echo ($v["wxname"]); ?></td>
                                    <td class="text-left"><?php echo (date("Y-m-d",$v["create_time"])); ?></td>                                    
                                    <td class="text-left"><?php echo ($v["money"]); ?></td>
                                    <td class="text-left"><?php echo ($v["remark"]); ?></td>


                                    <td class="text-left">                                         
                                        <?php if($v[status] == 0): ?>申请中<?php endif; ?>                                        
                                        <?php if($v[status] == 1): ?>申请成功<?php endif; ?>
                                        <?php if($v[status] == 2): ?>申请失败<?php endif; ?>
                                    </td>                                    
                                    <td class="text-left">
                                        <a href="<?php echo U('Admin/Distribut/editWithdrawals',array('id'=>$v['id'],'p'=>$_GET[p]));?>" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑"><i class="fa fa-pencil"></i></a>                                        
                                        <?php if(in_array($v[status],array(0,2))): ?><a href="javascript:void(0);" onclick="del('<?php echo ($v[id]); ?>')" id="button-delete6" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a><?php endif; ?>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>
                
                <div class="row">
                    <div class="col-sm-6 text-left"></div>
                    <div class="col-sm-6 text-right"><?php echo ($show); ?></div>
                </div>
          
          </div>
        </div>
      </div>
    </div>
    <!-- /.row --> 
  </section>
  <!-- /.content --> 
</div>
<!-- /.content-wrapper --> 
 <script>
 // 删除操作
function del(id)
{
	if(!confirm('确定要删除吗?'))
		return false;		
		$.ajax({
			url:"/index.php?m=Admin&c=Distribut&a=delWithdrawals&id="+id,
			success: function(v){	
                            var v =  eval('('+v+')');                                 
                            if(v.hasOwnProperty('status') && (v.status == 1))
                               location.href='<?php echo U('Admin/Distribut/withdrawals');?>';
                            else                                
			        layer.msg(v.msg, {icon: 2,time: 1000}); //alert(v.msg);
			}
		}); 
	 return false;
}
 
$(document).ready(function() {
	$('#start_time').daterangepicker({
		format:"YYYY/MM/DD",
		singleDatePicker: false,
		showDropdowns: true,
		minDate:'2018/01/01',
		maxDate:'2030/01/01',
		startDate:'2018/01/01',
	    locale : {
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
	});
});
</script>
</body>
</html>