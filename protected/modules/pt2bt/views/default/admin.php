<!doctype html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title>115网盘下载注册</title>
</head>
<body>
<h3>选择PT种子，点击注册上传，会返回修改过tracker的种子</h3>
<hr/>
<form action="<?php echo Yii::app()->createAbsoluteUrl("pt2bt/default/upload"); ?>" method="post"
      enctype="multipart/form-data">
  <input type="file" name="torrent"/>
  <input type="checkbox" name="rss" checked/>是否推送到RSS链接
  <input type="submit" value="注册"/>
</form>
<span></span>
<br/>
<a href="<?php echo Yii::app()->createUrl('pt2bt/default/list'); ?>">管理历史注册记录
  <small style="color: deeppink;">(整合Peer导入)</small>
</a>&nbsp;
<a href="<?php echo Yii::app()->createUrl('pt2bt/rss'); ?>?nums=30">RSS订阅下载</a>
<hr/>
<br/>

<p>本Tracker在BT网络节点用户数: <?php echo $online !== false ? $online['nums'] : '未知'; ?></p>

<p><a href="<?php echo Yii::app()->homeUrl; ?>pt2bt/rank">爬行榜</a></p>

<h3>更新说明</h3>

<p>2014.07.01 添加是否让服务器上的UT下载该资源功能
  取消选择 <b style="color: red;">是否推送到RSS链接</b>，服务器上的utorrent将不会自动下载该种子，省磁盘空间</p>

<p>2014.07.04 修改种子下载时候默认显示的文件名</p>

<p>2014.07.09 1.添加 爬行榜 统计功能 2.修改导入内站Peer策略，导入同时自动删除 3 天前的旧Peer,无需再手动清理过期peer，仍然保留手动清理功能以便完整清空的情形</p>

<p>2014.09.15 增加从下载设备采集peer，自动导入<b>HDW</b>和<b>TTG</b>的peer，采集量和下载设备上下载的种子量和人数限制有关</p>

<p style="color: #ff0000">2014.09.22 修改内部工作模式，对每个种子对号入座，导入正确的PEER，不再使用随机的模式，提高吸血能力</p>
</body>
</html>