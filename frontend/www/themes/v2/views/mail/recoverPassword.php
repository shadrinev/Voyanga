<?php $link = Yii::app()->createAbsoluteUrl('/site/newPassword', array('key'=>$key)); ?>
<p>Кажется вы забыли свой пароль на сайте <?php Yii::app()->createAbsoluteUrl('/'); ?>?</p>
<p>Чтобы создать новый пароль нажмите по ссылке ниже:</p>
<p><?php echo CHtml::link($link, $link); ?>. </p>
<p>Эта ссылка будет активна до <?php echo $date; ?>.</p>