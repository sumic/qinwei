<?php
use core\widgets\JsBlock;
?>
<!-- #section:basics/content.breadcrumbs -->
<div class="breadcrumbs ace-save-state" id="breadcrumbs">
	<?php JsBlock::begin()?>
	<script type="text/javascript">
		try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
	</script>
	<?php JsBlock::end()?>
	<div class="content-tabs">
		<button class="roll-nav roll-left tabLeft" onclick="scrollTabLeft()">
			<i class="fa fa-backward"></i>
		</button>
		<nav class="page-tabs menuTabs tab-ui-menu" id="tab-menu">
			<div class="page-tabs-content" style="margin-left: 0px;"></div>
		</nav>
		<button class="roll-nav roll-right tabRight"
			onclick="scrollTabRight()">
			<i class="fa fa-forward" style="margin-left: 3px;"></i>
		</button>
		<div class="btn-group roll-nav roll-right">
			<button class="dropdown tabClose" data-toggle="dropdown">
				页签操作<i class="fa fa-caret-down" style="padding-left: 3px;"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right"
				style="min-width: 128px;">
				<li><a class="tabReload" href="javascript:refreshTab();">刷新当前</a></li>
				<li><a class="tabCloseCurrent" href="javascript:closeCurrentTab();">关闭当前</a></li>
				<li><a class="tabCloseAll" href="javascript:closeOtherTabs(true);">全部关闭</a></li>
				<li><a class="tabCloseOther" href="javascript:closeOtherTabs();">除此之外全部关闭</a></li>
			</ul>
		</div>
		<button class="roll-nav roll-right fullscreen"
			onclick="App.handleFullScreen()">
			<i class="fa fa-arrows-alt"></i>
		</button>
	</div>
	<!-- /section:basics/content.searchbox -->
</div>