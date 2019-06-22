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

 <style>#search-form > .form-group{margin-left: 10px;}</style>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i> 商品列表</h3>
        </div>
        <div class="panel-body">
          <div class="navbar navbar-default">

                <!-- <div class="form-group">
                  <select name="cat_id" id="cat_id" class="form-control">
                    <option value="">所有分类</option>
                    <?php if(is_array($categoryList)): foreach($categoryList as $k=>$v): ?><option value="<?php echo ($v['id']); ?>"> <?php echo ($v['name']); ?></option><?php endforeach; endif; ?>
                  </select>
                </div>
                <div class="form-group">
                  <select name="brand_id" id="brand_id" class="form-control">
                    <option value="">所有品牌</option>
                        <?php if(is_array($brandList)): foreach($brandList as $k=>$v): ?><option value="<?php echo ($v['id']); ?>"><?php echo ($v['name']); ?></option><?php endforeach; endif; ?>
                  </select>
                </div>-->

                <ul id="myTab" class="nav nav-tabs">
                  <li><a href="#ios" class="gb" data-value="1" data-toggle="tab" onclick="funsx(1)">商户</a></li>
                  <li><a href="#home" class="gb" data-value="2" data-toggle="tab" onclick="funsx(2)">寄卖商品</a></li>
                </ul>
                <p></p>

                <div id="myTabContent" class="tab-content">
                  <div class="tab-pane fade in active" id="home">
                    <form action="" id="search-form1" class="navbar-form form-inline" method="post" onsubmit="return false">
                        <b>模块筛选：</b>
                        <div class="form-group">
                            <select name="module_id" class="form-control" style="width: 200px;">
                                <option value="">请选择...</option>
                                <?php if(is_array($module)): foreach($module as $k=>$v): ?><option value="<?php echo ($v['module_id']); ?>" ><?php echo ($v['module_name']); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div><p></p>
                    <b>筛选操作：</b><div class="form-group">
                      <select name="goods_state" id="goods_state" class="form-control">
                        <option value="">全部</option>
                        <option value="0">待审核</option>
                        <option value="1">上架商品</option>
                        <option value="3">违规下架</option>
                      </select>
                        <select name="cat_id1" id="cat_id1" onchange="get_category2(this.value,'cat_id2','0');" class="form-control">
                            <option value="0">请选择商品分类</option>
                            <?php if(is_array($cat_list)): foreach($cat_list as $k=>$v): ?><option value="<?php echo ($v['id']); ?>" <?php if($v['id'] == $goodsInfo['cat_id1']): ?>selected="selected"<?php endif; ?> >
                                <?php echo ($v['mobile_name']); ?>
                                </option><?php endforeach; endif; ?>
                        </select>
                        <select name="cat_id2" id="cat_id2" onchange="get_category2(this.value,'cat_id3','0');" class="form-control">
                            <option value="0">请选择商品分类</option>
                        </select>
                        <select name="cat_id3" id="cat_id3"  class="form-control">
                            <option value="0">请选择商品分类</option>
                        </select>
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-order-id">关键词</label>
                      <div class="input-group">
                        <input type="text" name="key_word" value="" placeholder="搜索词" id="input-order-id" class="form-control">
                      </div>
                    </div>
                    <!--排序规则-->
                    <input type="hidden" name="orderby1" value="goods_id" />
                    <input type="hidden" name="orderby2" value="desc" />
                    <input type="hidden" name="type" value="2" />
                    <button type="submit" onclick="ajax_get_table('search-form1',1,2)" id="button-filter search-order" class="btn btn-primary"><i class="fa fa-search"></i> 筛选</button>
                    </form></div>

                  <div class="tab-pane fade" id="ios">
                    <form action="" id="search-form2" class="navbar-form form-inline" method="post" onsubmit="return false">
                        <b>模块筛选：</b>
                        <div class="form-group">
                            <select name="module_id" class="form-control" style="width: 200px;">
                                <option value="">请选择...</option>
                                <?php if(is_array($module)): foreach($module as $k=>$v): ?><option value="<?php echo ($v['module_id']); ?>" ><?php echo ($v['module_name']); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div><p></p>
                    <b>筛选操作：</b>
                    <div class="form-group">
                      <select name="goods_state" id="goods_state" class="form-control">
                        <option value="">全部</option>
                        <option value="1">上架商品</option>
                        <option value="3">违规下架</option>
                      </select>
                        <select name="cat_id1" id="1cat_id1" onchange="get_category2(this.value,'1cat_id2','0');" class="form-control">
                            <option value="0">请选择商品分类</option>
                            <?php if(is_array($cat_list)): foreach($cat_list as $k=>$v): ?><option value="<?php echo ($v['id']); ?>" <?php if($v['id'] == $goodsInfo['cat_id1']): ?>selected="selected"<?php endif; ?> >
                                <?php echo ($v['mobile_name']); ?>
                                </option><?php endforeach; endif; ?>
                        </select>
                        <select name="cat_id2" id="1cat_id2" onchange="get_category2(this.value,'1cat_id3','0');" class="form-control">
                            <option value="0">请选择商品分类</option>
                        </select>
                        <select name="cat_id3" id="1cat_id3" class="form-control">
                            <option value="0">请选择商品分类</option>
                        </select>
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-order-id">关键词</label>
                      <div class="input-group">
                        <input type="text" name="key_word" value="" placeholder="搜索词" id="input-order-id" class="form-control">
                      </div>
                    </div>
                    <!--排序规则-->
                    <input type="hidden" name="orderby1" value="goods_id" />
                    <input type="hidden" name="orderby2" value="desc" />

                    <input type="hidden" class="zhu" name="type" value="1" />
                    <button type="submit" onclick="ajax_get_table('search-form2',1,1)" id="button-filter search-order" class="btn btn-primary"><i class="fa fa-search"></i> 筛选</button>
                    </form></div>
                </div><p></p><form class="navbar-form form-inline" method="post" onsubmit="return false">
                <b>批量审核：</b>全选
                <input type="checkbox" onclick="$('input[name=\'goods_id\[\]\']').prop('checked', this.checked);">
                <div class="form-group">
                  <select id="func_id" class="form-control" style="width: 120px;" onchange="fuc_change(this);">
                    <option value="-1">请选择...</option>
                    <option value="0">推荐</option>
                    <option value="1">新品</option>
                    <option value="2">热卖</option>
                    <option value="3">审核商品</option>
                  </select>
                </div>
                <div class="form-group" id="state_div" >
                  <select id="state_id" class="form-control" style="display: none" onchange="state_change(this);">

                  </select>
                </div>
                <button id="act_button" type="button" onclick="act_submit();" class="btn btn-primary disabled"><i class="fa"></i> 确定</button>
              </form>
          </div>
          <div id="ajax_return"> </div>
        </div>
      </div>
    </div>
    <!-- /.row --> 
  </section>
  <!-- /.content --> 
</div>


<script>
    $(".gb").click(function () {
        var type = $(this).attr('data-value');
        if(type==1){
            $(".zhu").val(1);
            ajax_get_table('search-form2', 1,1);
        }else {
            $(".zhu").val(2);
            ajax_get_table('search-form1', 1,2);
        }
    })
    $(function () {
        $('#myTab li:eq(0) a').tab('show');
    });
    function funsx(type) {
        $.ajax({
            type: "POST",
            url: "/index.php/admin/goods/ajaxSx",
            data: {'type':type},// 你的formid
            success: function (data) {
                $("#state_id").html(data);
            }
        });
    }
</script>
<!-- /.content-wrapper -->
<script>
    $(document).ready(function () {
        // ajax 加载商品列表
        ajax_get_table('search-form2', 1,1);
        funsx(1);
    });

    // ajax 抓取页面 form 为表单id  page 为当前第几页
    function ajax_get_table(form, page,type) {
        cur_page = page; //当前页面 保存为全局变量
        $('#' + form).serialize();
        $.ajax({
            type: "POST",
            url: "/index.php?m=Admin&c=goods&a=ajaxGoodsList&p=" + page+"&type="+type,//+tab,
            data: $('#' + form).serialize(),// 你的formid
            success: function (data) {
                $("#ajax_return").html('');
                $("#ajax_return").append(data);
            }
        });
    }

    // 点击排序
    function sort(field) {
        $("input[name='orderby1']").val(field);
        var v = $("input[name='orderby2']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='orderby2']").val(v);
        ajax_get_table('search-form2', cur_page,1);
    }

    // 删除操作
    function del(id) {
        if (!confirm('确定要删除吗?'))
            return false;
        $.ajax({
            url: "/index.php?m=Admin&c=goods&a=delGoods&id=" + id,
            success: function (v) {
                var v = eval('(' + v + ')');
                if (v.hasOwnProperty('status') && (v.status == 1))
                    ajax_get_table('search-form2', cur_page);
                else
                    layer.msg(v.msg, {icon: 2, time: 1000}); //alert(v.msg);
            }
        });
        return false;
    }

    //获取选中商品id
    function get_select_goods_id_str() {
        if ($('input[name="goods_id\[\]"]:checked').length == 0)
            return false;
        var goods_arr = Array();
        $('input[name="goods_id\[\]"]:checked').each(function () {
            goods_arr.push($(this).val());
        });
        var goods_id_str = goods_arr.join(',');
        return goods_id_str
    }

    act = '';//操作变量
    //批量操作
    function fuc_change(obj) {
        var fuc_val = $(obj).children('option:selected').val();
        console.log(fuc_val);
        if (fuc_val == 0) {
            //推荐
            act = 'recommend';
            $('#act_button').removeClass('disabled');
            reset_state();
        } else if (fuc_val == 1) {
            act = 'new';
            $('#act_button').removeClass('disabled');
            reset_state();
            //新品
        } else if (fuc_val == 2) {
            act = 'hot';
            $('#act_button').removeClass('disabled');
            reset_state();
            //热卖
        } else if (fuc_val == 3) {
            act = 'examine';
            var show = 'state_id';
            $('#'+show).show();
            $('#act_button').addClass('disabled');
            $("#state_id option:first").prop("selected", 'selected');
            //审核商品
        } else {
            act = '';
            $('#act_button').addClass('disabled');
            reset_state();
            //恢复默认
        }
    }

    //重置审核操作
    function reset_state(type) {
        var first = 'state_id option:first';
        var hide = 'state_id';
        $('#'+first).prop("selected", 'selected');
        $('#'+hide).hide();
    }

    //审核操作
    function state_change(obj) {
        var state_val = $(obj).children('option:selected').val();
        if (state_val == '') {
            $('#act_button').addClass('disabled');
        } else {
            $('#act_button').removeClass('disabled');
        }
    }

    //批量操作提交
    function act_submit() {
        //prompt层
        var ids = get_select_goods_id_str();
        if (ids == false) {
            layer.alert('请勾选要操作的商品', {icon: 2});
            return;
        }
        var text = prompt('请输入操作备注', '填写操作备注,可不填');
        var goods_state = $('#state_id').children('option:selected').val();
        if (text != null && text != "") {
            $.ajax({
                type: "POST",
                url: "/index.php?m=Admin&c=goods&a=act",//+tab,
                data: {act: act,goods_state:goods_state,goods_ids: ids, reason: text},
                dataType: 'json',
                success: function (data) {
                    if(data.status == 1){
                        layer.alert(data.msg, {
                            icon: 1,
                            closeBtn: 0
                        }, function(){
                            window.location.reload();
                        });
                    }else{
                        layer.alert(data.msg, {icon: 2,time: 3000});
                    }

                },
                error:function(){
                    layer.alert('网络异常', {icon: 2,time: 3000});
                }
            });
        }
    }

    /**
     * 获取多级联动的商品分类
     */
    function get_category2(id,next,select_id){
        var url = '/index.php?m=Admin&c=Index&a=get_category&parent_id='+ id;
        $.ajax({
            type : "GET",
            url  : url,
            success: function(v) {
                v = "<option value='0'>请选择商品分类</option>" + v;
                $('#'+next).empty().html(v);
                (select_id > 0) && $('#'+next).val(select_id);//默认选中
            }
        });
    }
</script>
</body>
</html>