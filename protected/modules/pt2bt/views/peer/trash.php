<!doctype html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title>清空PT站点用户列表</title>
</head>
<body>
<form action="<?php echo Yii::app()->createAbsoluteUrl("pt2bt/peer/trash"); ?>" method="post"
      enctype="application/x-www-form-urlencoded">
  <label for="site">选择要清理的站点 <b style="color:#ff0000">*</b></label><select name="site" id="site">
    <option value="0">CHDBits</option>
    <option value="1">HDWing</option>
    <option value="2">TTG</option>
  </select>
  <input type="submit" value="清理"/>
</form>
<span></span>
<br/>
<a href="<?php echo Yii::app()->createUrl('pt2bt/default/list'); ?>">查看历史注册记录</a>&nbsp;
<a href="<?php echo Yii::app()->homeUrl;?>pt2bt/">返回首页</a>
</body>
</html>