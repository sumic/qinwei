<?php
use yii\widgets\DetailView;
// 定义标题和面包屑信息
$this->title = '文章详情';
?>
<style>
<!--
th{min-width:100px}
-->
</style>
<div class="col-xs-12 col-sm-12">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title"><?=$model->title;?></h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main">
			<?php
                        echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                ['label' => '类型', 'value' => function($model){
                                    return $model->type == 2 ? '单页' :'文章';
                        }],
                                'cid',
                                
                                'title',
                                'sub_title',
                                'summary',
                                [
                                    'label' => '属性',
                                    'format' => 'html',
                                    'value' => function($model){
                                                $str  = '<span class="label label-';
                                                $str .= ($model->flag_headline == 1) ? 'success">' : 'disable">';
                                                $str .= '头条</span>';
                                                
                                                $str .= ' <span class="label label-';
                                                $str .= ($model->flag_recommend == 1) ? 'success">' : 'disable">';
                                                $str .= '推荐</span>';
                                                
                                                $str .= ' <span class="label label-';
                                                $str .= ($model->flag_slide_show == 1) ? 'success">' : 'disable">';
                                                $str .= '幻灯</span>';
                                                
                                                $str .= ' <span class="label label-';
                                                $str .= ($model->flag_special_recommend == 1) ? 'success">' : 'disable">';
                                                $str .= '推荐</span>';
                                                
                                                $str .= ' <span class="label label-';
                                                $str .= ($model->flag_roll == 1) ? 'success">' : 'disable">';
                                                $str .= '滚动</span>';
                                                
                                                $str .= ' <span class="label label-';
                                                $str .= ($model->flag_bold == 1) ? 'success">' : 'disable">';
                                                $str .= '加粗</span>';
                                                
                                                $str .= ' <span class="label label-';
                                                $str .= ($model->flag_picture == 1) ? 'success">' : 'disable">';
                                                $str .= '图片</span>';
                                                return $str;
                                }
                            ],
                                'tag',
                                ['label' => '内容正文', 'format'=>'html','value' => $model->content],
                                'thumb',
                                'seo_title',
                                'seo_keywords',
                                'seo_description',
                                ['label' => '状态', 
                                    'format'=> 'html',
                                    'value' => function($model){
                                    return $model->status == 0 ? '<span class="label label-disable">草稿</span>' :'<span class="label label-success">发布</span>';
                            }],
                                'sort',
                                'author_name',
                                'scan_count',
                                'comment_count',
                                ['label' => '是否可评论',
                                    'format'=> 'html',
                                    'value' => function($model){
                                    return $model->can_comment == 0 ? '<span class="label label-disable">不可评论</span>' :'<span class="label label-success">可评论</span>';
                            }],
                                ['label' => '可见性',
                                    'format'=> 'html',
                                    'value' => function($model){
                                    $vis = [1=>'公开',2=>'评论可见',3=>'加密文章',4=>'登陆可见'];
                                    return '<span class="label label-success">'.$vis[$model->visibility].'</span>';
                            }],
                                'password',
                                ['label' => '添加时间', 'value' => date('Y-m-d H:i:s', $model->created_at)],
                                'created_id',
                                ['label' => '修改时间', 'value' => date('Y-m-d H:i:s', $model->updated_at)],
                                'updated_id'
                            ],
                        ]);
                        ?>               
			</div>
		</div>
	</div>
</div>