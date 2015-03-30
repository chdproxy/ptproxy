<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <title>爬行榜</title>
</head>
<body>
<a href="<?php echo Yii::app()->homeUrl;?>pt2bt/">返回首页</a>
<h3>当前最热门的30个种子</h3>
<?php $this->widget('zii.widgets.grid.CGridView', array(
  'dataProvider' => $top30_dataProvider,
  'columns' => array(
    array(
      'header' => '文件名',
      'class' => 'CLinkColumn',
      'labelExpression' => '$data["title"]',
      'urlExpression' => 'Yii::app()->createAbsoluteUrl("pt2bt/default/download",array("hash"=>$data["info_hash"]))',
    ),
    array(
      'header' => '上传+下载人数',
      'name' => 'nums',
      'type' => 'number'
    )
  )
));?>
<hr/>
<a href="<?php echo Yii::app()->homeUrl;?>pt2bt/">返回首页</a>
<h3>本月TOP 100</h3>(如果本月发布资源不足100，将不会显示出100条结果)
<?php $this->widget('zii.widgets.grid.CGridView', array(
  'dataProvider' => $top100_dataProvider,
  'columns' => array(
    array(
      'header' => '文件名',
      'class' => 'CLinkColumn',
      'labelExpression' => '$data["title"]',
      'urlExpression' => 'Yii::app()->createAbsoluteUrl("pt2bt/default/download",array("hash"=>$data["info_hash"]))',
    ),
    array(
      'header' => '完成下载',
      'name' => 'completed',
      'type' => 'number'
    )
  )
));?>
<a href="<?php echo Yii::app()->homeUrl;?>pt2bt/">返回首页</a>
</body>
</html>
