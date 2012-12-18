<?php $link = Yii::app()->createAbsoluteUrl('/') . '?open=newPassword&key='.$key; ?>
<p>Дорогой друг!</p>
<p>Кажется Вы забыли свой пароль от личного кабинета на сайте <?php echo CHtml::link('Voyanga.com', Yii::app()->createAbsoluteUrl('/')); ?>?</p> <br />
Чтобы создать новый пароль нажмите по ссылке: <?php echo CHtml::link('восстановить пароль', $link); ?>. <br />
Эта ссылка будет активна до <?php echo $date; ?>.</p>
<p>Если же Вы не теряли пароль или мы случайно ошиблись - просто удалите это письмо.</p>




