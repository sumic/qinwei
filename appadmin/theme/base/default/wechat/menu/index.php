<?php
use appadmin\assets\WechatAsset;
use appadmin\assets\NotifyAsset;
use core\widgets\JsBlock;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
WechatAsset::register($this);
NotifyAsset::register($this);

$this->title = '自定义菜单管理';
$chosenOptions = [
    'options' => [
        'class' => 'no-padding-right'
    ],
];
?>

<div class="col-xs-12 col-sm-3" style="min-width: 375px">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">预览</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;min-height:530px">
			<div class="widget-main clearfix">
				<div style="margin-bottom:10px">
				<input type="hidden" id="needsave" name='needsave' value=''/>
					<?= Html::dropDownList('mpid',
					    $currentMp,
					    ArrayHelper::map($mpbase, 'id', 'mpname'),
					    ['class' => 'chosen-select', 'encode'=>false,'prompt' => '选择公众号...','id'=>'mpid']); ?>
				</div>
                <div class="hs-ph-area">
				<div class="hs-ph-view">
					<div class="hs-ph-hd"></div>
					<div class="hs-ph-bd">
						<ul class="hs-menu-ul">
							<li class="hs-menu-li hs-menu-item-add"><a class="hs-nocan"
								href="javascript:void(0)"> <span class="hs-madd"></span>
							</a></li>
						</ul>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="col-xs-12 col-sm-8" style="min-width:800px">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">菜单信息</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main clearfix">
                <div class="hs-ph-edit">
				<div class="hs-menuright-box">
					<div class="hs-menuright-hd">
						<div class="hs-menuright-row">
							<h4 class="hs-menutitle">子菜单名称</h4>
							<a class="hs-menuright-del" href="javascript:void(0)">删除菜单</a>
						</div>
					</div>
					<div class="hs-menuright-bd">
						<div class="hs-menuright-mname">
							<label class="hs-menuright-mlabel">菜单名称</label>
							<div class="hs-menuright-mlabelr">
								<span class="hs-menuright-ipt"> <input
									class="hs-menuright-iptval" type="text" id="hsCurrentMenuName" />
								</span>
							</div>
						</div>
						<div class="hs-menuright-mval">
							<div class="hs-menuright-row">
								<label class="hs-menuright-mlabel">菜单内容</label>
								<div class="hs-menuright-mlabelr">
									<label class=""> <input class="hs-ico-control" type="radio"
										name="menu_type" value="1" checked="checked" /> <i
										class="hs-ico-radio hs-selected"></i> <span>发送消息</span>
									</label> <label class=""> <input class="hs-ico-control"
										type="radio" name="menu_type" value="2" /> <i class="hs-ico-radio"></i>
										<span>跳转网页</span>
									</label>
								</div>
							</div>
							<div class="hs-menuright-row" style="margin-top: 15px">
								<!--row start-->
								<div class="hs-menuright-panel hs-mrp1" style="display: block;">
									<!--编辑 start-->
									<div style="background: #fff">
										<div class="hs-ui-beditor hs-js-tab hs-auto">
											<div class="hs-etap-nav">
												<ul>
													<li jstab-target="newsArea"
														class="hs-etap-one hs-etap-news active"><a
														href="javascript:void(0);" onclick="return false;">&nbsp;<i
															class="hs-etap-icon"></i><span class="hs-etap-title">图文</span></a>
													</li>
													<li jstab-target="imageArea"
														class="hs-etap-one hs-etap-image"><a
														href="javascript:void(0);" onclick="return false;">&nbsp;<i
															class="hs-etap-icon"></i><span class="hs-etap-title">图片</span></a>
													</li>
													<li jstab-target="audioArea"
														class="hs-etap-one hs-etap-audio"><a
														href="javascript:void(0);" onclick="return false;">&nbsp;<i
															class="hs-etap-icon"></i><span class="hs-etap-title">语音</span></a>
													</li>
													<li jstab-target="videoArea"
														class="hs-etap-one hs-etap-video"><a
														href="javascript:void(0);" onclick="return false;">&nbsp;<i
															class="hs-etap-icon"></i><span class="hs-etap-title">视频</span></a>
													</li>
												</ul>
											</div>
											<div class="hs-etap-panel">
												<div jstab-des="newsArea" class="hs-etap-content">
													<div class="hs-etap-newsArea hs-inner">
														<!--** ---图文--- ***-->
														<div id="newsxz">
															<div class="hs-con-cover">
																<div class="hs-media-cover">
																	<span class="hs-create-access"> <a href="javascript:;"
																		id="hsSelNewsBtn"> <i class="hs-icon36 hs-icon36-add"></i>
																			<strong>从素材库中选择</strong>
																	</a>
																	</span>
																</div>
																<div class="hs-media-cover">
																	<span class="hs-create-access"> <a href="javascript:;">
																			<i class="hs-icon36 hs-icon36-add"></i> <strong>新建素材</strong>
																	</a>
																	</span>
																</div>
															</div>
														</div>
														<div id="newssed" style="display: none;"></div>
														<!--*****-->
													</div>
												</div>
												<div jstab-des="imageArea" class="hs-etap-content"
													style="display: none;">
													<div class="hs-etap-imageArea hs-inner">
														<div class="hs-con-cover">
															<div class="hs-media-cover">
																<span class="hs-create-access"> <a href="javascript:;">
																		<i class="hs-icon36 hs-icon36-add"></i> <strong>从素材库中选择</strong>
																</a>
																</span>
															</div>
															<div class="hs-media-cover">
																<span class="hs-create-access"> <a href="javascript:;">
																		<i class="hs-icon36 hs-icon36-add"></i> <strong>上传图片</strong>
																</a>
																</span>
															</div>
														</div>
													</div>
												</div>
												<div jstab-des="audioArea" class="hs-etap-content"
													style="display: none;">
													<div class="hs-etap-audioArea hs-inner">
														<div class="hs-con-cover">
															<div class="hs-media-cover">
																<span class="hs-create-access"> <a href="javascript:;">
																		<i class="hs-icon36 hs-icon36-add"></i> <strong>从素材库中选择</strong>
																</a>
																</span>
															</div>
															<div class="hs-media-cover">
																<span class="hs-create-access"> <a href="javascript:;">
																		<i class="hs-icon36 hs-icon36-add"></i> <strong>新建语音</strong>
																</a>
																</span>
															</div>
														</div>
													</div>
												</div>
												<div jstab-des="videoArea" class="hs-etap-content"
													style="display: none;">
													<div class="hs-etap-videoArea hs-inner">
														<div class="hs-con-cover">
															<div class="hs-media-cover">
																<span class="hs-create-access"> <a href="javascript:;">
																		<i class="hs-icon36 hs-icon36-add"></i> <strong>从素材库中选择</strong>
																</a>
																</span>
															</div>
															<div class="hs-media-cover">
																<span class="hs-create-access"> <a href="javascript:;">
																		<i class="hs-icon36 hs-icon36-add"></i> <strong>新建视频</strong>
																</a>
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!--编辑 end-->
								</div>
								<div class="hs-menuright-panel hs-mrp2" style="display: none;">
									<div class="hs-menuright-rediect">
										<form onsubmit="return false;">
											<p class="hs-c-8d8d8d">订阅者点击该菜单会跳到以下链接</p>
											<div class="">
												<label class="hs-menuright-mlabel">页面地址</label>
												<div class="hs-menuright-mlabelr">
													<span class="hs-menuright-ipt "> <!--hs-spandisabled--> <input
														class="hs-menuright-iptval" type="text" id="hsUrlValue"
														name="" />
													</span>
													<div class="hs-clearfix"></div>
													<a href="javascript:void(0)" class="hs-handle-a">从公众号图文消息中选择</a>
												</div>
											</div>
										</form>
									</div>
								</div>
								<!--row end-->
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="col-xs-12 col-sm-8" style="margin:10px auto;">
	<button class="btn btn-success " type="button" id="hsSubmitSave">保存</button>
    <button class="btn btn-info " type="button" id="hsSubmitSyncWx">生成微信菜单</button> 
</div>
<?php JsBlock::begin()?>
<script>
	$.notifyDefaults({
		icon: 'fa fa-bell',
		placement: {from: "top",align: "center"},
		template : '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} alert-with-icon alert-rose" role="alert">'+
		'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>'+
		'<i data-notify="icon"></i>'+
		'<span data-notify="title">{1}</span>'+
		'<span data-notify="message">{2}</span>'+
		'<div class="progress" data-notify="progressbar">'+
		'	<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>'+
		'</div>'+
		'<a href="{3}" target="{4}" data-notify="url"></a>'+
	'</div>',
	})
    $(function(){
        //选择公众号后初始化
    	$('#mpid').change(function(){
        	getMenuData();
        	});
    	$('.chosen-select').chosen({
			allow_single_deselect:false,
			no_results_text: "没有找到相关栏目",
			search_contains:true
			});
		$(window)
		.off('resize.chosen')
		.on('resize.chosen', function() {
			$('.chosen-select').each(function() {
				 var $this = $(this);
				 $this.next().css({'width': $this.parent().width()});
			})
		}).trigger('resize.chosen'); 
        /**
         * 选择图文
         */  
        $(document).on('click', '#hsSelNewsBtn', function(){
           hs_show_dialog('imgtext', {
               submit: function(e, d, i){

                   var media_id = i.substr(9);
                   var dialog = $(d._div);
                   var newsed = dialog.find('#'+i);
                   var del = '<a id="newsdel" class="clearfix" href="#">删除</a>'
                   if(newsed){
                       $('#newssed').show();
                       $('#newssed').html(newsed).append(del);
                       $('#newsxz').hide();
                   }
                   hsUpdateCurrentData({ act_list:[ { type:'news', value:media_id } ] })
               }
           })
        })
        /**
         * 点击删除选中的图文
         */
        $(document).on('click','#newsdel',function(){
            $('#newsxz').show();
            $('#newssed').hide();
            $('#newssed').html('');
        });

        //保存
        $(document).on('click', '#hsSubmitSave', function(){
            var newv = JSON.stringify(hsGetCurrentAllData());
            var mpid = $('#mpid').val();

            $.post('/wechat/menu/create',
                {
                    newv : newv,
                    mpid : mpid
                },
                function(data){
                    if(data.errCode == 0){
                    	swal("保存成功", '点击生成微信菜单提交。',"success");
                    	$("#needsave").val('false');
                    	getMenuData();
                    }else{
                    	swal("错误！", data.errMsg,"error")
                    }
                }
            );
        });
        
        //生成微信菜单
        $(document).on('click', '#hsSubmitSyncWx', function(){
        	if($("#mpid").val() == ''){
    			swal('错误','请选择要生成的公众号','error');
    		}
        	if($("#needsave").val() == 'true'){
    			swal('错误','菜单更新后还未保存，请保存后再提交','error');
    		}
    		
        	$.post('/wechat/menu/asyncwxmenu',
                    {
                		mpid:$("#mpid").val(),
                	},
                    function(data){
                        if(data.errCode == 0){
                            swal("成功", data.msg,"success");
                        }else{
                        	swal("错误！", data.msg,"error")
                        }
                    }
                )
        });

        function getMenuData()
        {
        	var mpid = $('#mpid').val();
        	var mptext =  $("#mpid option:selected").text();
        	//init default blank menu
        	$('.hs-menu-ul>.hs-menu-item').remove();
        	$.get('/wechat/menu/search',
                    {
						"offset":"0",
						"limit":"25",
                		"filters" : {"mpid":mpid},
                		"orderBy":"id asc"
                    },
                    function(result){
                        if(result.code !== 0 && result.recordsTotal > 0){
                        	$.notify({icon: 'fa fa-bell',message: mptext+" 菜单信息已刷新"},{type: "success"});
							var menudata = JSON.stringify(result.data);
                        	hsInitMenu(menudata);
                        }else{
                        	$.notify({icon: 'fa fa-bell',message: mptext+" 获取菜单数据失败"},{type: "danger"});
                        	hsInitMenu(null);
                        }
                    }
                );
            }
    })
</script>
<?php JsBlock::end();?>