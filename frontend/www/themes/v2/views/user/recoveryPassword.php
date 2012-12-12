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
                    <h2>Забыли пароль?</h2>
                    <hr>
                    <?php if (Yii::app()->user->hasFlash('success')): ?>
                        <?php echo Yii::app()->user->getFlash('success'); ?>
                    <?php else: ?>
                        <?php echo CHtml::errorSummary($model); ?>
                        <div class="control-group">
                            <label for="" class="control-label">Email</label>
                            <div class="controls">
                                <?php echo CHtml::activeTextField($model, 'email', array('placeholder' => 'Email')); ?>
                                <?php echo CHtml::error($model, 'email'); ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-large btn-pink">Восстановить пароль</button>
                        </div>
                    <?php endif ?>
                    <?php $this->endWidget(); ?>
                </div>
                <div class="span2"></div>
            </div>
        </div>
    </div>