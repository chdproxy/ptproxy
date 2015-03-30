<!doctype html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<div class="form">
  <?php $form = $this->beginWidget('CActiveForm'); ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <?php echo $form->label($model, 'username'); ?>
    <?php echo $form->textField($model, 'username') ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'password'); ?>
    <?php echo $form->passwordField($model, 'password') ?>
  </div>

  <div class="row submit">
    <?php echo CHtml::submitButton('登录'); ?>
  </div>
  <?php $this->endWidget(); ?>
</div>
<!-- login form -->
</body>
</html>
