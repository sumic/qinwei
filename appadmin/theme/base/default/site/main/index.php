<?php

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = "控制台";
use appadmin\assets\PageBlankAsset;
use core\widgets\JsBlock;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
PageBlankAsset::register($this);
//获得menu的当前缓存KEY

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	 <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style type="text/css">
        #page-content{overflow:hidden;padding:0}
    </style>
</head>
<?php $this->beginBody() ?>
<body class="no-skin">
		<!-- #section:basics/navbar.layout -->
		<!-- 顶部导航开始 -->
		<?= Yii::$service->page->widget->render('nav',$this); ?>
		<!-- 顶部导航结束 -->
		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
			<?php JsBlock::begin()?>
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
			<?php JsBlock::end()?>

			<!-- #section:basics/sidebar -->
			<!-- #左侧菜单开始 -->
			<?= Yii::$service->page->widget->render('menu',[
                    'options' => [
                        'id' => 'nav-list-main',
                        'class' => 'nav nav-list',
                    ],
                    'labelName' => 'menu_name',
                    'items' =>  '',
                    'itemsName' => 'child'
                ]); ?>
			<!-- /左侧菜单结束 -->
			<!-- /section:basics/sidebar -->
			<div class="main-content">
			    <!-- #面包屑开始 -->
				<?= Yii::$service->page->widget->render('breadcrumbs',$this); ?>
				<!-- /面包屑结束 -->
				<div class="page-content" id="page-content">
					<div class="page-content-area">
						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="content-iframe" style="background-color: #ffffff;">
                            		<div class="tab-content" id="tab-content"></div>
                            	</div>
								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content-area -->
				</div><!-- /.page-content -->
			</div><!-- /.main-content -->
			    <!-- #页脚开始 -->
			
				<!-- /页脚结束 -->
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->
<?php JsBlock::begin() ?>
    <script type="text/javascript">
        $(function () {
            addTabs({
                id: '10000',
                title: '控制台',
                close: false,
                url: '/site/main/system',
                urlType: "absolute"
            });
            //App.fixIframeCotent();
            $("#nav-list-main").find("a").click(function (e) {
                if ($(this).attr("href") != "#") {
                    var $parent = $(this).closest("li").parent();
                    if ($parent.hasClass("nav-list")) {
                        $parent.children("li").removeClass("active");
                        $parent.find("li.hsub ul.submenu").hide().removeClass("open active").find("li").removeClass("active")
                    } else if ($parent.hasClass("submenu")) {
                        $parent.find("li.active").removeClass("active");
                        $parent.parent("li").siblings("li").removeClass("active")
                    }
                    $(this).closest("li").addClass("active")
                }
            });
            });
    </script>
<?php JsBlock::end() ?>
		
</body>
<?php $this->endBody() ?>
</html>
<?php $this->endPage() ?>
