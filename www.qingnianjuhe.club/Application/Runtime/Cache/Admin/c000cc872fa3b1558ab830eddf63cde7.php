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

    <section class="content ">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 添加优惠券</h3>
                </div>
                <div class="panel-body ">   
                    <!--表单数据-->
                    <form action="" method="post">              
                        <!--通用信息-->
                    <div class="tab-content col-md-10">                 	  
                        <div class="tab-pane active" id="tab_tongyong">                           
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="col-sm-2">优惠券名称：</td>
                                    <td class="col-sm-4">
                                        <input type="text" value="<?php echo ($coupon["name"]); ?>" class="form-control" id="name" name="name" >
                                        <span id="err_attr_name" style="color:#F00; display:none;"></span>                                        
                                    </td>
                                    <td class="col-sm-4">请填写优惠券名称
                                    </td>
                                </tr>  
                                <tr>
                                    <td>优惠券面额：</td>
                                    <td >
                         				<input type="text" value="<?php echo ($coupon["money"]); ?>" class="form-control" id="money" name="money"  onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                    </td>
                                    <td class="col-sm-4">优惠券可抵扣金额</td>
                                </tr>  
                                <tr>
                                    <td>消费金额：</td>
                                    <td>
                      					<input type="text" value="<?php echo ($coupon["condition"]); ?>" class="form-control active" id="condition" name="condition"  onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" />
                                    </td>
                                    <td class="col-sm-4">订单需满足的最低消费金额(必需为整数)才能使用</td>
                                </tr>
                                <tr>
			                        <td>优惠券类型:</td>
			                        <td id="order-status">
			                         	<input name="type" type="radio" class="en" value="0" <?php if($coupon['type'] == 0): ?>checked<?php endif; ?> >通用型优惠券
			                            <input id="guazi" name="type" class="en" type="radio" value="1" <?php if($coupon['type'] == 1): ?>checked<?php endif; ?> >分销商专属优惠券

			                            <!--<input name="type" type="radio" value="3" <?php if($coupon['type'] == 3): ?>checked<?php endif; ?> >邀请发放-->
			                            <!--<input name="type" type="radio" value="4" <?php if($coupon['type'] == 4): ?>checked<?php endif; ?> >线下发放-->
			                        </td>
									<td class="add"></td>
			                    </tr>
								<tr class="dis" style="display:none">
									<td>请输入分销商:</td>
									<td colspan="2"><input class="form-control" name="store_id" value="<?php echo ($coupon["store_id"]); ?>"  placeholder="多个分销商ID请用英文逗号(,)隔开;默认为全部分销商"></td>
								</tr>
								<tr class="dis2" style="display:none">
									<td>请输入分销商承担比例:</td>
									<td colspan="2"><input class="form-control" name="ratio" value="<?php echo ($coupon["ratio"]); ?>"  placeholder="分销商承担的代金券比例"></td>
								</tr>
			                    <tr>
			                    	<td>发放数量:</td>
			                    	<td><input type="text" value="<?php if(empty($coupon['createnum'])): ?>50<?php endif; ?> <?php echo ($coupon["createnum"]); ?>" class="form-control" id="createnum" name="createnum" placeholder="0" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" /></td>
			                    	<td class="col-sm-4">发放数量限制</td>
			                    </tr>
			                    <tr class="timed">
			                        <td>发放起始日期:</td>
			                        <td>
			                            <div class="input-prepend input-group">
			                                <span class="add-on input-group-addon">
			                                      <i class="glyphicon glyphicon-calendar fa fa-calendar">  </i>
			                                </span>
			                                <input type="text" value="<?php echo (date('Y-m-d H:i:s',$coupon["send_start_time"])); ?>" class="form-control" id="send_start_time" name="send_start_time">
			                            </div>
			                        </td>
			                        <td class="col-sm-4"></td>
			                    </tr>
			
			                    <tr class="timed">
			                        <td>发放结束日期:</td>
			                        <td>
			                            <div class="input-prepend input-group">
			                                <span class="add-on input-group-addon">
			                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"> </i>
			                                </span>
			                                <input type="text" value="<?php echo (date('Y-m-d H:i:s',$coupon["send_end_time"])); ?>" class="form-control" id="send_end_time" name="send_end_time">
			                            </div>
			                        </td>
			                        <td class="col-sm-4"></td>
			                    </tr>
					            <tr>
			                        <td>使用起始日期:</td>
			                        <td>
			                            <div class="input-prepend input-group">
			                                <span class="add-on input-group-addon">
			                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
			                                </span>
			                                <input type="text" value="<?php echo (date('Y-m-d H:i:s',$coupon["use_start_time"])); ?>" class="form-control" id="use_start_time" name="use_start_time">
			                            </div>
			                        </td>
			                        <td class="col-sm-4"></td>
			                    </tr> 
			                    <tr>
			                        <td>使用结束日期:</td>
			                        <td>
			                            <div class="input-prepend input-group">
			                                <span class="add-on input-group-addon">
			                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
			                                </span>
			                                <input type="text" value="<?php echo (date('Y-m-d H:i:s',$coupon["use_end_time"])); ?>" class="form-control" id="use_end_time" name="use_end_time">
			                            </div>
			                        </td>
			                        <td class="col-sm-4"></td>
			                    </tr>
								<tr>
									<td>选择使用模块</td>
									<td>
										<?php if(is_array($module)): foreach($module as $k=>$v): ?><input type="checkbox" name="module[]" value="<?php echo ($v['module_id']); ?>" <?php if(in_array($v['module_id'],$module_id)): ?>checked="checked"<?php endif; ?>> <?php echo ($v['module_name']); ?>&nbsp;&nbsp;<?php endforeach; endif; ?>
									</td>
								</tr>
                                </tbody> 
                                <tfoot>
                                	<tr>
                                	<td>
                                		<input type="hidden" name="id" value="<?php echo ($coupon["id"]); ?>">
                                	</td>
                                	<td class="col-sm-4"></td>
                                	<td class="text-right"><input class="btn btn-primary" type="submit" value="保存"></td>
                                	</tr>
                                </tfoot>                               
                            </table>
                        </div>                           
                    </div>              
			    	</form><!--表单数据-->
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$('.en').click(function () {
	if($(this).val()==1){
	    $(".dis").show();
	}else {
	    $(".dis").hide();
	}
    if($(this).val()==1){
        $(".dis2").show();
    }else {
        $(".dis2").hide();
    }
})
$('input[type="radio"]').click(function(){
    if($(this).val() == 0){
    	$('.timed').hide();
    }else{
    	$('.timed').show();
    }
})


$(function(){
    data_pick('send_start_time');
    data_pick('send_end_time');
    data_pick('use_start_time');
    data_pick('use_end_time');
    $('input[type="radio"]:checked').trigger('click');
})
    
function data_pick(id){
    var myDate = new Date();
    $('#'+id).daterangepicker({
    	format:"YYYY-MM-DD HH:mm:ss",
        singleDatePicker: true,
        showDropdowns: true,
        minDate:myDate.getFullYear()+'-'+myDate.getMonth()+'-'+myDate.getDate(),
        maxDate:'2030-01-01',
		timePicker : true, //是否显示小时和分钟  
        timePickerIncrement:1,//time选择递增数
		timePicker12Hour : false, //是否使用12小时制来显示时间 		
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
}
</script>
</body>
</html>