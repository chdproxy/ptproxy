<!doctype html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<div class="form">
  <div><?php echo Yii::app()->user->getFlash('account_reset'); ?></div>
  <form action="<?php echo Yii::app()->createAbsoluteUrl('user/reset'); ?>" method="POST">
    <div class="row">
      <input type="email" name="email" value=""/>
    </div>
    <div class="row submit">
      <?php echo CHtml::submitButton('发送重置邮件'); ?>
    </div>
  </form>
</div>
</body>
</html>
