<?php
/**
 * An email message that contains a link to reset the user's password
 * @var AEmail $this The email being sent
 * @var AUser $user The user being emailed
 */

$this->subject = "Восстановление пароля";
?>
<p>Привет<?php echo (isset($user->name) ? ", " . $user->name : "")?>!</p>
<p>Чтобы сбросить твой пароль на <?php echo Yii::app()->name; ?>, пожалуйста, кликни по следующей ссылке:</p>
<p><?php
    $url = Yii::app()->createAbsoluteUrl("/users/user/resetPassword", array("id" => $user->id, "key" => $user->passwordResetCode));
    echo CHtml::link($url, $url);
    ?></p>
<p>Если ты получил это письмо по ошибке, то просто не реагируй на него.</p>
<p>Спасибо,<br/> твой робот сервиса <?php echo Yii::app()->name; ?>.</p>

