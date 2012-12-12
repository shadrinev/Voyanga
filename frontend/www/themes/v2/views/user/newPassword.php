<div id="site_register">
    <div class="container">
        <div class="row">
            <div class="span2"></div>
            <div class="span8">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'enableAjaxValidation' => false,
                    'htmlOptions' => array(
                        'class' => 'form-horizontal'
                    )
                )); ?>
                <div class="background">
                    <?php if (Yii::app()->user->hasFlash('success')): ?>
                        <h2>Пароль сохранён</h2>
                        <hr>
                        <?php echo Yii::app()->user->getFlash('success'); ?>
                    <?php else: ?>
                        <h2>Введите ваш новый пароль</h2>
                        <hr>
                        <?php echo CHtml::errorSummary($model); ?>
                        <div class="control-group">
                            <label for="" class="control-label">Пароль</label>

                            <div class="controls">
                                <?php echo CHtml::activePasswordField($model, 'password', array('placeholder' => 'Новый пароль')); ?>
                                <?php echo CHtml::error($model, 'password'); ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-large btn-pink">Сохранить</button>
                        </div>
                    <?php endif ?>
                    <?php $this->endWidget(); ?>
                </div>
                <div class="span2"></div>
            </div>
        </div>
    </div>