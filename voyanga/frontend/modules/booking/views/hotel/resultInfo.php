<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Бронирование'=>array('/admin/booking/'),
    'Гостиница'=>array('/admin/booking/hotel'),
    'Поиск'
);
?>

<h3 id='result'>Информация об отеле</h3>
<h3>Рекомендованные варианты</h3>
<?php echo $this->renderPartial('_hotels_recommended', array('results'=>$resultsRecommended, 'variable'=>'hotelResults','cacheId'=>$cacheId)); ?>

<h3>Отдельные комбинации</h3>
<?php echo $this->renderPartial('_hotels_all', array('results'=>$resultsAll, 'variable'=>'hotelResults','cacheId'=>$cacheId)); ?>


<?php Yii::app()->clientScript->registerScript('smooth-scroll', "
$('.result-hotel-link').trigger('click');
", CClientScript::POS_READY); ?>