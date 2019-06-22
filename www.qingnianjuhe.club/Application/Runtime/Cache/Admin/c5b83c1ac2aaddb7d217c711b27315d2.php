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
 

<script type="text/javascript" charset="utf-8" src="/Public/js/fileupload/jquery.fileupload.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/js/fileupload/jquery.fileupload-ui.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/js/fileupload/jquery.fileuploadui.css"></script>
<script type="text/javascript" charset="utf-8" src="/Public/js/fileupload/jquery.iframe-transport.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/js/fileupload/jquery.ui.widget.js"></script>

<!--物流配置 css -start-->
<style>
    ul.group-list {
        width: 96%;min-width: 1000px; margin: auto 5px;list-style: disc outside none;
    }
    ul.group-list li {
        white-space: nowrap;float: left;
        width: 150px; height: 25px;
        padding: 3px 5px;list-style-type: none;
        list-style-position: outside;border: 0px;margin: 0px;
    }
</style>
<!--物流配置 css -end-->

<!--以下是在线编辑器 代码 -->
<script type="text/javascript">
    /*
   * 在线编辑器相 关配置 js 
   *  参考 地址 http://fex.baidu.com/ueditor/
   */
    window.UEDITOR_Admin_URL = "/Public/plugins/Ueditor/";
    var URL_upload = "<?php echo ($URL_upload); ?>";
    var URL_fileUp = "<?php echo ($URL_fileUp); ?>";
    var URL_scrawlUp = "<?php echo ($URL_scrawlUp); ?>";
    var URL_getRemoteImage = "<?php echo ($URL_getRemoteImage); ?>";
    var URL_imageManager = "<?php echo ($URL_imageManager); ?>";
    var URL_imageUp = "<?php echo ($URL_imageUp); ?>";
    var URL_getMovie = "<?php echo ($URL_getMovie); ?>";
    var URL_home = "<?php echo ($URL_home); ?>";
</script>
<script type="text/javascript" charset="utf-8" src="/Public/plugins/Ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/plugins/Ueditor/ueditor.all.min.js"> </script>
 <script type="text/javascript" charset="utf-8" src="/Public/plugins/Ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">  
  
    var editor;
    $(function () {
        //具体参数配置在  editor_config.js  中
        var options = {
            zIndex: 999,
            initialFrameWidth: "95%", //初化宽度
            initialFrameHeight: 400, //初化高度
            focus: false, //初始化时，是否让编辑器获得焦点true或false
            maximumWords: 99999, removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign'
            , //允许的最大字符数 'fullscreen',
            pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
            autoHeightEnabled: true
         /*   autotypeset: {
                mergeEmptyline: true,        //合并空行
                removeClass: true,           //去掉冗余的class
                removeEmptyline: false,      //去掉空行
                textAlign: "left",           //段落的排版方式，可以是 left,right,center,justify 去掉这个属性表示不执行排版
                imageBlockLine: 'center',    //图片的浮动方式，独占一行剧中,左右浮动，默认: center,left,right,none 去掉这个属性表示不执行排版
                pasteFilter: false,          //根据规则过滤没事粘贴进来的内容
                clearFontSize: false,        //去掉所有的内嵌字号，使用编辑器默认的字号
                clearFontFamily: false,      //去掉所有的内嵌字体，使用编辑器默认的字体
                removeEmptyNode: false,      //去掉空节点
                                             //可以去掉的标签
                removeTagNames: {"font": 1},
                indent: false,               // 行首缩进
                indentValue: '0em'           //行首缩进的大小
            }*/,
          toolbars: [
                   ['fullscreen', 'source', '|', 'undo', 'redo',
                       '|', 'bold', 'italic', 'underline', 'fontborder',
                       'strikethrough', 'superscript', 'subscript',
                       'removeformat', 'formatmatch', 'autotypeset',
                       'blockquote', 'pasteplain', '|', 'forecolor',
                       'backcolor', 'insertorderedlist',
                       'insertunorderedlist', 'selectall', 'cleardoc', '|',
                       'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                       'customstyle', 'paragraph', 'fontfamily', 'fontsize',
                       '|', 'directionalityltr', 'directionalityrtl',
                       'indent', '|', 'justifyleft', 'justifycenter',
                       'justifyright', 'justifyjustify', '|', 'touppercase',
                       'tolowercase', '|', 'link', 'unlink', 'anchor', '|',
                       'imagenone', 'imageleft', 'imageright', 'imagecenter',
                       '|', 'insertimage', 'emotion', 'insertvideo',
                       'attachment', 'map', 'gmap', 'insertframe',
                       'insertcode', 'webapp', 'pagebreak', 'template',
                       'background', '|', 'horizontal', 'date', 'time',
                       'spechars', 'wordimage', '|',
                       'inserttable', 'deletetable',
                       'insertparagraphbeforetable', 'insertrow', 'deleterow',
                       'insertcol', 'deletecol', 'mergecells', 'mergeright',
                       'mergedown', 'splittocells', 'splittorows',
                       'splittocols', '|', 'print', 'preview', 'searchreplace']
               ]
        };
        editor = new UE.ui.Editor(options);
        editor.render("goods_content");  //  指定 textarea 的  id 为 goods_content

    }); 
</script>
<!--以上是在线编辑器 代码  end-->
<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>

    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
        
                <a href="<?php echo U('Goods/goodsList',array('goods_state'=>1,'is_back'=>1));?>" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
                </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>商品详情</h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">通用信息</a></li>
<!--                        <li><a href="#tab_goods_desc" data-toggle="tab">描述信息</a></li>-->
                        <li><a href="#tab_goods_images" data-toggle="tab">商品相册</a></li>
                         <!--<li><a href="#tab_goods_spec" data-toggle="tab">商品规格</a></li>-->
                        
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditGoodsForm">
                    <!--通用信息-->
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_tongyong">
                           
                            <table class="table table-bordered">
                                <tbody>
                                <tr hidden>
                                    <td>商品货号：</td>
                                    <td>                                                                               
                                        <input type="text" value="<?php echo ($goodsInfo["goods_sn"]); ?>" name="goods_sn" class="form-control" style="width:350px;"/>
                                        <span id="err_goods_sn" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                <tr hidden>
                                    <td>SPU：</td>
                                    <td>                                                                               
                                        <input type="text" value="<?php echo ($goodsInfo["spu"]); ?>" name="spu" class="form-control" style="width:350px;"/>
                                        <span id="err_goods_spu" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                <tr hidden>
                                    <td>SKU：</td>
                                    <td>                                                                               
                                        <input type="text" value="<?php echo ($goodsInfo["sku"]); ?>" name="sku" class="form-control" style="width:350px;"/>
                                        <span id="err_goods_sku" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>模块：</td>
                                    <td>
                                        <div class="col-xs-3">
                                            <select name="module_id" class="form-control" style="width:250px;margin-left:-15px;">
                                                <option value="0">请选择所在模块</option>
                                                <?php if(is_array($module)): foreach($module as $k=>$v): ?><option value="<?php echo ($v['module_id']); ?>" <?php if($v['module_id'] == $goodsInfo['module_id']): ?>selected="selected"<?php endif; ?>><?php echo ($v['module_name']); ?></option><?php endforeach; endif; ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>品类：</td>
                                    <td>
                                      <div class="col-xs-3">
                                      <select name="cat_id1" id="cat_id1" onchange="get_category2(this.value,'cat_id2','0');" class="form-control" style="width:250px;margin-left:-15px;">
                                        <option value="0">请选择商品分类</option>
                                             <?php if(is_array($cat_list)): foreach($cat_list as $k=>$v): ?><option value="<?php echo ($v['id']); ?>" <?php if($v['id'] == $goodsInfo['cat_id1']): ?>selected="selected"<?php endif; ?> >
                                                  <?php echo ($v['mobile_name']); ?>
                                               </option><?php endforeach; endif; ?>
                                      </select>
                                      </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>品牌/品项：</td>
                                    <td>
                                        <div class="col-xs-3">
                                            <select name="cat_id2" id="cat_id2" onchange="get_category2(this.value,'cat_id3','0');" class="form-control" style="width:250px;margin-left:-15px;">
                                                <option value="0">请选择商品分类</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr hidden>
                                    <td>品项：</td>
                                    <td>
                                        <div class="col-xs-3">
                                            <select name="cat_id3" id="cat_id3"  class="form-control" style="width:250px;margin-left:-15px;">
                                                <option value="0">请选择商品分类</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr hidden>
                                    <td>本店分类:</td>
                                    <td>
                                      <div class="col-xs-3">
                                      <select name="store_cat_id1" id="store_cat_id1" onchange="get_store_category(this.value,'store_cat_id2','0');" class="form-control" style="width:250px;margin-left:-15px;">
                                        <option value="0">请选择分类</option>                                      
                                             <?php if(is_array($store_goods_class_list)): foreach($store_goods_class_list as $k=>$v): ?><option value="<?php echo ($v['cat_id']); ?>" <?php if($v['cat_id'] == $goodsInfo['store_cat_id1']): ?>selected="selected"<?php endif; ?> >
                                                  <?php echo ($v['cat_name']); ?>
                                               </option><?php endforeach; endif; ?>
                                      </select>
                                      </div>
                                      <div class="col-xs-3">
                                      <select name="store_cat_id2" id="store_cat_id2" class="form-control" style="width:250px;margin-left:-15px;">
                                        <option value="0">请选择分类</option>
                                      </select>  
                                      </div>                                     
                                      <span id="err_cat_id" style="color:#F00; display:none;"></span>                                 
                                    </td>
                                </tr>                                                                
                                <tr hidden>
                                    <td>商品品牌:</td>
                                    <td>
                  <select name="brand_id" id="brand_id" class="form-control" style="width:250px;">
                                           <option value="0">选择品牌</option>
                                            <?php if(is_array($brandList)): foreach($brandList as $k=>$v): if($v['status'] == 0): ?><option value="<?php echo ($v['id']); ?>"  data-cat_id1="<?php echo ($v['cat_id1']); ?>" <?php if($v['id'] == $goodsInfo['brand_id'] ): ?>selected="selected"<?php endif; ?>>
                                                        <?php echo ($v['name']); ?>
                                                    </option><?php endif; endforeach; endif; ?>
                                      </select>                                    
                                    </td>
                                </tr>
                                <tr hidden>
                                    <td>供应商:</td>
                                    <td>
                                        <select name="suppliers_id" id="suppliers_id" class="form-control" style="width:250px;">
                                            <option value="0">不指定供应商属于本店商品</option>
                                            <?php if(is_array($suppliersList)): foreach($suppliersList as $k=>$v): ?><option value="<?php echo ($v['suppliers_id']); ?>"  <?php if($v['suppliers_id'] == $goodsInfo['suppliers_id'] ): ?>selected="selected"<?php endif; ?>>
                                                <?php echo ($v['suppliers_name']); ?>
                                                </option><?php endforeach; endif; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr hidden>
                                    <td>本店会员售价:</td>
                                    <td>
                                        <input type="text" value="<?php echo ($goodsInfo["vip_price"]); ?>1" name="vip_price" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                    </td>
                                </tr>

                                <!--<tr>
                                    <td>成本价:</td>-->
                                    <!--<td>-->
                                        <!--<input type="text" value="<?php echo ($goodsInfo["cost_price"]); ?>" name="cost_price" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />-->
                                        <!--<span id="err_cost_price" style="color:#F00; display:none"></span>                                                  -->
                                    <!--</td>-->
                                <!--</tr>       -->
                                <tr>
                                    <td>商品名称:</td>
                                    <td>
                                        <input type="text" value="<?php echo ($goodsInfo["goods_name"]); ?>" name="goods_name" class="form-control" style="width:350px;"/>
                                        <span id="err_goods_name" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>本店售价:</td>
                                    <td>
                                        <input type="text" value="<?php echo ($goodsInfo["shop_price"]); ?>" name="shop_price" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                        <span id="err_shop_price" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                <tr>
                                <tr>
                                    <td>市场价:</td>
                                    <td>
                                        <input type="text" value="<?php echo ($goodsInfo["market_price"]); ?>" name="market_price" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                        <span id="err_market_price" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                    <td>上传商品图片:</td>
                                    <td>
                                        <input type="button" value="上传图片"  onclick="GetUploadify(10,'/Public/images/add-button.jpg','goods','call_back2');"/>
                                        <input type="text" class="input-sm"  name="original_img" id="original_img" value="<?php echo ($goodsInfo["original_img"]); ?>"/>
                                        <?php if($goodsInfo['original_img'] != null): ?>&nbsp;&nbsp;
                                            <a target="_blank" href="<?php echo ($goodsInfo["original_img"]); ?>" id="original_img2">
                                                <img width="25" height="25" src="/Public/images/image_icon.jpg">
                                            </a><?php endif; ?>
                                        <input type="hidden" name="imgs">
                                        <span id="err_original_img" style="color:#F00; display:none;"></span>                                                 
                                    </td>
                                </tr>

                                <tr>
                                <style>
                                    .kctd1,.kctd{text-align: center}
                                    .kctd div,.kctd1 div{border-left: 1px solid #99999A;border-right: 1px solid #99999A;padding: 5px;}
                                </style>
                                <tr class="kctd">
                                    <td colspan="2">
                                        <div class="col-md-2">商品规格名称</div>
                                        <div class="col-md-2">价格</div>
                                        <div class="col-md-2">库存</div>
                                    </td>
                                </tr>
                                <?php if(!empty($goodsPrice)): if(is_array($goodsPrice)): foreach($goodsPrice as $key=>$val): ?><tr class="kctd">
                                    <td colspan="2"><div class="col-md-2"><input type="text" class="form-control enheng" name="name[]" placeholder="请输入规格名称" value="<?php echo ($val['key_name']); ?>"></div><div class="col-md-2"><input type="text" class="form-control en" name="price[]" placeholder="请输入商品价格" value="<?php echo ($val['price']); ?>"></div><div class="col-md-2"><input type="text" class="form-control aa" placeholder="请输入库存数量" name="count[]" value="<?php echo ($val['store_count']); ?>"></div></td></tr><?php endforeach; endif; else: ?>
                                    <tr class="kctd">
                                        <td colspan="2"><div class="col-md-2"><input type="text" class="form-control enheng" name="name[]" placeholder="请输入规格名称" value="默认规格"></div><div class="col-md-2"><input type="text" class="form-control en" name="price[]" placeholder="请输入商品价格" value="0"></div><div class="col-md-2"><input type="text" class="form-control aa" placeholder="请输入库存数量" name="count[]" value="0"></div></td></tr><?php endif; ?>
                                <tr class="kctd1">
                                    <td colspan="2" >
                                        <div class="col-md-6"><button class="btn btn-info add" onclick="return false">继续添加</button></div>
                                    </td>
                                </tr>
                                <script>
                                    $(".add").click(function () {
                                        $(".kctd1").before('<tr class="kctd"><td colspan="2"><div class="col-md-2"><input type="text" class="form-control enheng" name="name[]" placeholder="请输入规格名称" value="默认规格"></div></div><div class="col-md-2"><input type="text" class="form-control en" name="price[]" value="0"placeholder="请输入商品价格"></div><div class="col-md-2"><input type="text" class="form-control aa" value="0" placeholder="请输入库存数量" name="count[]"></div></td></tr>');
                                    })
                                </script>
                                <tr >
                                    <td>用于分销的分成金额:</td>
                                    <td>
                                        <input type="text" value="<?php echo ((isset($goodsInfo["distribut"]) && ($goodsInfo["distribut"] !== ""))?($goodsInfo["distribut"]):0.00); ?>" name="distribut" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />

                                    </td>
                                </tr>
                                <!--<tr>
                                    <td>库存数量:</td>
                                    <td>
                                        <?php if($goodsInfo[goods_id] > 0): ?><input type="text" value="<?php echo ($goodsInfo["store_count"]); ?>" class="form-control" style="width:150px;" name="store_count" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                        <?php else: ?>
                                            <input type="text" value="<?php echo ($tpshop_config[basic_default_storage]); ?>" class="form-control" style="width:150px;" name="store_count" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" /><?php endif; ?>
                                        
                                        <span id="err_store_count" style="color:#F00; display:none;"></span>                                                  
                                    </td>
                                </tr>-->
                                <!--<tr>-->
                                    <!--<td>赠送积分:</td>-->
                                    <!--<td>-->
                                        <!--<input type="text" class="form-control" style="width:150px;" value="<?php echo ($goodsInfo["give_integral"]); ?>" name="give_integral" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />-->
                                        <!--<span id="err_give_integral" style="color:#F00; display:none;"></span>                                                  -->
                                    <!--</td>-->
                                <!--</tr>-->
                                <!--<tr hidden>-->
                                    <!--<td>兑换积分:</td>-->
                                    <!--<td>-->
                                        <!--<input type="text" class="form-control" style="width:150px;" value="<?php echo ($goodsInfo["exchange_integral"]); ?>" name="exchange_integral" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />-->
                                        <!--<span id="err_exchange_integral" style="color:#F00; display:none;"></span>-->
                                    <!--</td>-->
                                <!--</tr>-->
                                <!--
                                <tr>
                                    <td>设置:</td>
                                    <td>
                                      <input type="checkbox" checked="checked" value="<?php echo ($goodsInfo["is_on_sale"]); ?>" name="is_on_sale"/> 上架&nbsp;&nbsp;
                                  <input type="checkbox" checked="checked" value="<?php echo ($goodsInfo["is_free_shipping"]); ?>" name="is_free_shipping"/> 包邮&nbsp;&nbsp;
                                        <input type="checkbox" checked="checked" value="<?php echo ($goodsInfo["is_recommend"]); ?>" name="is_recommend"/>推荐&nbsp;&nbsp;
                                        <input type="checkbox" checked="checked" value="<?php echo ($goodsInfo["is_new"]); ?>" name="is_new"/>新品&nbsp;&nbsp;
                                    </td>
                                </tr>
                                -->
                                <tr>
                                    <td>是否包邮:</td>
                                    <td>
                                        是:<input type="radio" <?php if($goodsInfo[is_free_shipping] == 1): ?>checked="checked"<?php endif; ?> value="1" name="is_free_shipping" />
                                        否:<input type="radio" <?php if($goodsInfo[is_free_shipping] == 0): ?>checked="checked"<?php endif; ?> value="0" name="is_free_shipping" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>商品重量:</td>
                                    <td>
                                        <input type="text" class="form-control" style="width:150px;" value="<?php echo ($goodsInfo["weight"]); ?>" name="weight" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                        &nbsp;克 (以克为单位)
                                        <span id="err_weight" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>商品简介:</td>
                                    <td>
                                        <textarea rows="3" cols="50" name="goods_remark"><?php echo ($goodsInfo["goods_remark"]); ?></textarea>
                                        <span id="err_goods_remark" style="color:#F00; display:none;"></span>

                                    </td>
                                </tr>
                                <tr>
                                    <td>商品优势:</td>
                                    <td>
                                        <textarea rows="3" cols="50" name="advantage"><?php echo ($goodsInfo["advantage"]); ?></textarea>
                                        <span id="err_goods_remark" style="color:#F00; display:none;"></span>

                                    </td>
                                </tr>
                                <tr>
                                    <td>商品关键词:</td>
                                    <td>
                                        <input type="text" class="form-control" style="width:350px;" value="<?php echo ($goodsInfo["keywords"]); ?>" name="keywords"/>用空格分隔
                                        <span id="err_keywords" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>                                    
                                <tr>
                                    <td>商品详情:</td>
                                    <td>
                    <textarea class="span12 ckeditor" id="goods_content" name="goods_content" title="">
				            <?php echo ($goodsInfo["goods_content"]); ?>
				        </textarea>
                                        <span id="err_goods_content" style="color:#F00; display:none;"></span>                                         
                                    </td>                                                                       
                                </tr>

                                </tbody>                                
                                </table>
                        </div>
                         <!--其他信息-->
                         
                        <!-- 商品相册-->
                        <div class="tab-pane" id="tab_goods_images">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>                                    
                                    <td>                                                            
                                    <?php if(is_array($goodsImages)): foreach($goodsImages as $k=>$vo): ?><div style="width:100px; text-align:center; margin: 5px; display:inline-block;" class="goods_xc">
                                            <input type="hidden" value="<?php echo ($vo['image_url']); ?>" name="goods_images[]">
                                            <a onclick="" href="<?php echo ($vo['image_url']); ?>" target="_blank"><img width="100" height="100" src="<?php echo ($vo['image_url']); ?>"></a>
                                            <br>
                                            <a href="javascript:void(0)" onclick="ClearPicArr2(this,'<?php echo ($vo['image_url']); ?>')">删除</a>
                                        </div><?php endforeach; endif; ?>
                                    
                                        <div class="goods_xc" style="width:100px; text-align:center; margin: 5px; display:inline-block;">
                                            <input type="hidden" name="goods_images[]" value="" />
                                            <!--<a href="javascript:void(0);" onclick="GetUploadify(10,'/Public/images/add-button.jpg','goods','call_back2');"><img src="/Public/images/add-button.jpg" width="100" height="100" /></a>-->
                                            <br/>
                                            <a href="javascript:void(0)">&nbsp;&nbsp;</a>
                                        </div>
                                    </td>
                                </tr>                                              
                                </tbody>
                            </table>
                        </div>
                         <!--商品相册--> 
   
                        <!-- 商品规格-->
                        <!--</div>-->
                        <!-- 商品规格-->

                        <!-- 商品属性-->
                        <!--<div class="tab-pane" id="tab_goods_attr">-->
                            <!--<table class="table table-bordered" id="goods_attr_table">                                -->
                                <!--<tr>-->
                                    <!--<td colspan="2">-->
                                                        <!---->
                                    <!--</td>-->
                                <!--</tr>                                -->
                            <!--</table>-->
                        <!--</div>-->
                        <!--&lt;!&ndash; 商品属性&ndash;&gt;-->

                        <!--&lt;!&ndash; 商品物流&ndash;&gt;-->
                        <!--<div class="tab-pane" id="tab_goods_shipping">-->
                            <!--<h4><b>物流配送：</b><input type="checkbox" onclick="choosebox(this)">全选</h4>-->
                            <!--<table class="table table-bordered table-striped dataTable" id="goods_shipping_table">-->
                                <!--<?php if(is_array($plugin_shipping)): foreach($plugin_shipping as $kk=>$shipping): ?>-->
                                    <!--<tr>-->
                                        <!--<td class="title left" style="padding-right:50px;">-->
                                            <!--<b><?php echo ($shipping[name]); ?>：</b>-->
                                            <!--<label class="right"><input type="checkbox" value="1" cka="mod-<?php echo ($kk); ?>">全选</label>-->
                                        <!--</td>-->
                                    <!--</tr>-->
                                    <!--<tr>-->
                                        <!--<td>-->
                                            <!--<ul class="group-list">-->
                                                <!--<?php if(is_array($shipping_area)): foreach($shipping_area as $key=>$vv): ?>-->
                                                    <!--<?php if($vv[shipping_code] == $shipping[code]): ?>-->
                                                        <!--<li><label><input type="checkbox" name="shipping_area_ids[]" value="<?php echo ($vv["shipping_area_id"]); ?>" <?php if(in_array($vv['shipping_area_id'],$goods_shipping_area_ids)): ?>checked='checked='<?php endif; ?> ck="mod-<?php echo ($kk); ?>"><?php echo ($vv["shipping_area_name"]); ?></label></li>-->
                                                    <!--<?php endif; ?>-->
                                                <!--<?php endforeach; endif; ?>-->
                                                <!--<div class="clear-both"></div>-->
                                            <!--</ul>-->
                                        <!--</td>-->
                                    <!--</tr>-->
                                <!--<?php endforeach; endif; ?>-->
                            <!--</table>-->
                        <!--</div>-->
                        <!--&lt;!&ndash; 商品物流&ndash;&gt;-->
                    </div>              
                    <div class="pull-right">
                        <input type="hidden" name="goods_id" value="<?php echo ($goodsInfo["goods_id"]); ?>">
                        <button class="btn btn-primary" onclick="ajax_submit_form('addEditGoodsForm','<?php echo U('Goods/addEditGoods?is_ajax=1');?>');" title="" data-toggle="tooltip" type="button" data-original-title="保存">保存</button>
                    </div>
          </form><!--表单数据-->
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
</div>




<script>
    $(document).ready(function(){
        $(":checkbox[cka]").click(function(){
            var $cks = $(":checkbox[ck='"+$(this).attr("cka")+"']");
            if($(this).is(':checked')){
                $cks.each(function(){$(this).prop("checked",true);});
            }else{
                $cks.each(function(){$(this).removeAttr('checked');});
            }
        });
    });
    function choosebox(o){
        var vt = $(o).is(':checked');
        if(vt){
            $('input[type=checkbox]').prop('checked',vt);
        }else{
            $('input[type=checkbox]').removeAttr('checked');
        }
    }
    /*
     * 以下是图片上传方法
     */
 
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp){
        $("#original_img").val(fileurl_tmp[0]);
      $("#original_img2").attr('href', fileurl_tmp);
    }
 
    // 上传商品相册回调函数
    function call_back2(paths){
        
    //alert(paths.length);
        $("#original_img").val(paths[0]);
        $("#original_img2").attr('href', paths);
        var  last_div = $(".goods_xc:last").prop("outerHTML");  
        for (i=0;i<paths.length ;i++ )
        {                    
            $(".goods_xc:eq(0)").before(last_div);  // 插入一个 新图片
                $(".goods_xc:eq(0)").find('a:eq(0)').attr('href',paths[i]).attr('onclick','').attr('target', "_blank");// 修改他的链接地址
            $(".goods_xc:eq(0)").find('img').attr('src',paths[i]);// 修改他的图片路径
                $(".goods_xc:eq(0)").find('a:eq(1)').attr('onclick',"ClearPicArr2(this,'"+paths[i]+"')").text('删除');
            $(".goods_xc:eq(0)").find('input').val(paths[i]); // 设置隐藏域 要提交的值
        }          
    }
    /*
     * 上传之后删除组图input     
     * @access   public
     * @val      string  删除的图片input
     */
    function ClearPicArr2(obj,path)
    {

        $(obj).parent().remove(); // 删除完服务器的, 再删除 html上的图片
        console.log(path);
    /*
      $.ajax({
                    type:'GET',
                    url:"<?php echo U('Admin/Uploadify/delupload');?>",
                    data:{action:"del", filename:path},
                    success:function(){
                      $(obj).parent().remove(); // 删除完服务器的, 再删除 html上的图片         
                    }
    });
    */
    // 删除数据库记录
      $.ajax({
                    type:'GET',
                    url:"<?php echo U('Seller/Goods/del_goods_images');?>",
                    data:{filename:path},
                    success:function(data){
                        console.log(data);
                    }
    });     
    }
 
// 属性输入框的加减事件
function addAttr(a)
{
  var attr = $(a).parent().parent().prop("outerHTML");  
  attr = attr.replace('addAttr','delAttr').replace('+','-');  
  $(a).parent().parent().after(attr);
}
// 属性输入框的加减事件
function delAttr(a)
{
   $(a).parent().parent().remove();
}
 
/**
* ajax 加载规格 和属性
*/
function ajaxGetSpecAttr(goods_id,cat_id1)
{
  // ajax调用 返回规格
  $.ajax({
      type:'GET',
//      data:{goods_id:goods_id,cat_id1:cat_id1},
      url:"/index.php?m=Admin&c=Goods&a=ajaxGetSpecSelect&goods_id="+goods_id+"&cat_id1="+cat_id1,
      success:function(data){                            
           $("#ajax_spec_data").html('');
           $("#ajax_spec_data").append(data);
        if($.trim(data) != '')  
           ajaxGetSpecInput();  // 触发完  马上处罚 规格输入框
      }
  }); 
  
  // 商品类型切换时 ajax 调用  返回不同的属性输入框          
  $.ajax({
      type:'GET',
//      data:{goods_id:goods_id,cat_id3:cat_id3},
      url:"/index.php?m=Admin&c=Goods&a=ajaxGetAttrInput&goods_id="+goods_id+"&cat_id3="+cat_id3,     
      success:function(data){                            
          $("#goods_attr_table tr:gt(0)").remove();
          $("#goods_attr_table").append(data);
      }        
  }); 
}


/** 以下是编辑时默认选中某个商品分类*/
$(document).ready(function(){

   // 商品分类第二个下拉菜单
  <?php if($goodsInfo['cat_id2'] > 0): ?>get_category2("<?php echo ($goodsInfo['cat_id1']); ?>",'cat_id2',"<?php echo ($goodsInfo['cat_id2']); ?>");<?php endif; ?>
  
  // 商品分类第三个下拉菜单  
  <?php if($goodsInfo['cat_id3'] > 0): ?>get_category2("<?php echo ($goodsInfo['cat_id2']); ?>",'cat_id3',"<?php echo ($goodsInfo['cat_id3']); ?>");<?php endif; ?>

  // 店铺内部分类
  <?php if($goodsInfo['store_cat_id2'] > 0): ?>get_store_category("<?php echo ($goodsInfo['store_cat_id1']); ?>",'store_cat_id2',"<?php echo ($goodsInfo['store_cat_id2']); ?>");<?php endif; ?>
       
  // 如果是编辑的时候 加载 规格 和属性
  <?php if($goodsInfo['cat_id1'] > 0): ?>ajaxGetSpecAttr(<?php echo ($goodsInfo['goods_id']); ?>,<?php echo ($goodsInfo['cat_id1']); ?>);<?php endif; ?>     
  
  // 商品品牌根据分类显示相关的品牌
  $('#brand_id option').each(function(){
    var cat_id1 = $('#cat_id1').val();
    if($(this).data('cat_id1') != cat_id1 && $(this).val() > 0)
      $(this).hide();   
  });  
   

});

// 商品分类切换时,过滤掉非当前分类下的品牌 不能给太多品牌选择
$("#cat_id1").change(function(){   
  $('#brand_id option').show();
  $('#brand_id option').each(function(){
    var cat_id1 = $('#cat_id1').val();
    if($(this).data('cat_id1') != cat_id1 && $(this).val() > 0)   
      $(this).hide();   
  });  
     
});

// 商品类型切换时 ajax 调用  返回不同的属性输入框
$("#cat_id3").change(function(){        
    var goods_id = '<?php echo ($goodsInfo["goods_id"]); ?>';
    var cat_id3 = $(this).val();
    if(cat_id3 == 0)
     return false;
     ajaxGetSpecAttr(goods_id,cat_id3);        
});
  
function get_store_category(id,next,select_id){
    var url = '/index.php?m=Home&c=api&a=get_store_category&parent_id='+ id;
    $.ajax({
        type : "GET",
        url : url,
        error: function(request) {
            alert("服务器繁忙, 请联系管理员!");
            return;
        },
        success: function(v) {
      v = "<option value='0'>请选择商品分类</option>" + v;
            $('#'+next).empty().html(v);
      (select_id > 0) && $('#'+next).val(select_id);//默认选中
        }
    });
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