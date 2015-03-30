<?xml version = "1.0" ?>
<rss version="2.0">
  <channel>
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link><?php echo CHtml::encode(Yii::app()->createAbsoluteUrl('')); ?></link>
    <description>PT2BT The Latest Torrent</description>
    <language>en-us</language>
    <pubDate><?php echo date(DATE_RSS); ?></pubDate>
    <generator>ChinaHDTV</generator>
    <?php if ($models): ?>
      <?php foreach ($models as $model): ?>
        <item>
          <title><?php echo CHtml::encode($model->name); ?></title>
          <link><?php echo CHtml::encode(Yii::app()
              ->createAbsoluteUrl('pt2bt/rss/download') . '?hash=' . bin2hex($model->info_hash)); ?></link>
          <pubDate><?php echo date(DATE_RSS, $model->created); ?></pubDate>
        </item>
        <?php unset($model); ?>
      <?php endforeach;endif; ?>
  </channel>
</rss>