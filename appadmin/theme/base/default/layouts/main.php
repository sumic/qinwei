<?php

/* @var $this \yii\web\View */
/* @var $content string */

use appadmin\assets\AceAsset;
use yii\helpers\Html;
use core\widgets\JsBlock;

AceAsset::register($this);
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
</head>
<?php $this->beginBody() ?>

<body class="no-skin">
	<div class="main-container" id="main-container">
		<?php JsBlock::begin() ?>
		<script type="text/javascript">
			try {
				ace.settings.check('main-container', 'fixed')
			} catch (e) {}
		</script>
		<?php JsBlock::end() ?>
		<div class="main-content">
			<div class="page-content">
				<div class="page-content-area">
					<div class="page-header">
						<h1><?= $this->title; ?></h1>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->
							<?= $content ?>
							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content-area -->
			</div><!-- /.page-content -->
		</div><!-- /.main-content -->
		<!-- #页脚开始 -->
		<div class="footer">
			<div class="footer-inner">
				<!-- #section:basics/footer -->
				<?= Yii::$service->page->widget->render('footer', $this); ?>
				<!-- /section:basics/footer -->
			</div>
		</div>
		<!-- /页脚结束 -->
		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
		</a>
	</div><!-- /.main-container -->
</body>
<?php $this->endBody() ?>

</html>
<?php $this->endPage() ?>