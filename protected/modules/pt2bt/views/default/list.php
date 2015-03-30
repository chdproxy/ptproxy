<!doctype html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<a href="<?php echo Yii::app()->createUrl('pt2bt/default/admin'); ?>">PT种子注册</a>
<br/>
<?php $this->widget('zii.widgets.grid.CGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array(
      'header' => '文件名',
      'class' => 'CLinkColumn',
      'labelExpression' => '$data->name',
      'urlExpression' => 'Yii::app()->createAbsoluteUrl("pt2bt/default/download",array("hash"=>$data->info_hash))',
    ),
    array(
      'name' => 'created',
      'header' => '生成日期',
      'type' => 'datetime'
    ),
    array(
      'name' => 'last_active',
      'header' => '最后活跃',
      'type' => 'datetime'
    ),
    array(
      'header' => '目标站点',
      'value' => '$data->getReadAbleSiteName()'
    ),
    array(
      'header' => '开放下载',
      'name' => 'status',
      'type' => 'boolean'
    ),
    array(
      'header' => '做种人数',
      'name' => 'seeder',
      'type' => 'number'
    ),
    array(
      'header' => '下载人数',
      'name' => 'leacher',
      'type' => 'number'
    ),
    array(
      'header' => '完成人数',
      'name' => 'completed',
      'type' => 'number'
    ),
    array(
      'header' => '整体上传量',
      'name' => 'uploaded',
      'type' => 'size'
    ),
    array(
      'header' => '整体下载量',
      'name' => 'downloaded',
      'type' => 'size'
    ),
    array(
      'header' => '操作',
      'class' => 'CLinkColumn',
      'label' => '修改|添加Peer',
      'urlExpression' => 'Yii::app()->createAbsoluteUrl("pt2bt/peer/import",array("info_hash"=>$data->info_hash))',
    ),

  ),
));

?><br/><a href="<?php echo Yii::app()->createUrl('pt2bt/default/admin'); ?>">PT种子注册</a>
</body>
</html>