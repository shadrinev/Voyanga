<?php $link = Yii::app()->createAbsoluteUrl('/user/newPassword', array('key'=>$key)); ?>
<p>Кажется вы забыли свой пароль на сайте <?php echo CHtml::link('Voyanga.com', Yii::app()->createAbsoluteUrl('/')); ?>?</p>
<p>Чтобы создать новый пароль нажмите по ссылке ниже:</p>
<p><?php echo CHtml::link($link, $link); ?>. </p>
<p>Эта ссылка будет активна до <?php echo $date; ?>.</p>