<?php
/*
 * A flash message shown when a password reset email has been sent
 * @var AUser $user the user who was emailed
 */
?>
<h3>Пожалуйста, проверьте свой e-mail.</h3>
<p>Письмо с ссылкой для сброса пароля было отправлено на адрес <?php echo $user->email; ?>.</p>
<p>Кликните по ссылке в письме, чтобы установить новый пароль.</p>