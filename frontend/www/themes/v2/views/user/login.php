<div id="site_register">
    <div class="container">
        <div class="row">
            <div class="span2"></div>
            <div class="span8">
                <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'login-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                ),
                'htmlOptions' => array(
                    'class' => 'login-form'
                ))); ?>
                <div class="background">
                    <h2>Войти на сайт</h2>
                    <hr>
                    <?php if (isset($model->errors['password'][0])): ?>
                        <span style='color: red'><?php echo $model->errors['password'][0]; ?></span>
                    <?php endif ?>
					<div class="control-group">	
							<?php echo $form->label($model, 'email'); ?>
                        <div class="controls">
							<?php echo $form->textField($model, 'email'); ?>
                        </div>
                    </div>
                    
					<div class="control-group">	
							<?php echo $form->label($model, 'password'); ?>
                        <div class="controls">
							<?php echo $form->passwordField($model, 'password'); ?>
                        </div>
                    </div>
					<div class="control-group">	
                    <label for="LoginForm_rememberMe" class="checkbox"><input name="LoginForm[rememberMe]" id="LoginForm_rememberMe"
                                                             value="1" type="checkbox">&nbsp;Запомнить меня</label>
                        <div class="controls">
					<?php if ($model->requireCaptcha): ?>
                    <?php endif; ?>
                        </div>
                    </div>
                    <div class="actions">
                        <?php echo CHtml::submitButton('Войти', array('class' => 'btn')); ?>
                        <a href='/user/newPassword'>Забыли пароль?</a>
                    </div>

                    <?php $this->endWidget(); ?>
                </div>
                <div class="span2"></div>
            </div>
        </div>
    </div>
</div>