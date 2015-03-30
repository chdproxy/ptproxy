<!doctype html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title>导入数据</title>
</head>
<body>
<form action="<?php echo Yii::app()->createAbsoluteUrl('pt2bt/peer/import') ?>" method="post"
      enctype="application/x-www-form-urlencoded">
  <p>在文本域输入 IP:PORT 的用户信息，一行一个用户信息，例如：</p>

  <p>192.168.1.1:51414 <br>192.168.1.2:51414 </p>
  <label for="peers">用户信息导入 <b style="color:#ff0000">*</b>&nbsp;库存用户 <b><?php echo $peerNums; ?></b> 人
    <br></label><textarea name="peers" id="peers" cols="30"
                          rows="30"></textarea><br>
  <input type="hidden" name='info_hash' value="<?php echo $info_hash ?>"/>
  <br>
  <input type="submit" value="导入"/>
</form>
<hr/>
<form action="<?php echo Yii::app()->createAbsoluteUrl('pt2bt/peer/delete') ?>" method="post">
  <input type="hidden" name="info_hash" value="<?php echo $info_hash ?>"/>
  <input type="submit" value="清除过期Peer"/>
</form>
<hr/>
<!--<a href="--><?php //echo Yii::app()->createAbsoluteUrl("pt2bt/peer/trash"); ?><!--">点击这里清理无效的PT内站用户信息</a>-->
<a href="<?php echo Yii::app()->homeUrl; ?>pt2bt/">返回首页</a>
</body>
</html>