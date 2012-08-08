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

<?php echo $this->renderPartial('_form_hotel', array('model'=>$hotelForm, 'autosearch'=>$autosearch, 'cityName'=>$cityName)); ?>
<a href='#result' class="result-hotel-link" style="display: none">Результаты поиска</a>

<h3 id='result'>Результаты поиска</h3>
<?php if ($results): ?>
    <?php echo $this->renderPartial('_hotels', array('results'=>$results, 'variable'=>'hotelResults','cacheId'=>$cacheId)); ?>
<?php else: ?>
    <?php echo $this->renderPartial('_hotels_empty', array('cacheId'=>$cacheId)); ?>
<?php endif ?>

<?php Yii::app()->clientScript->registerScript('smooth-scroll', "
var Utils = new Object();
Utils.scrollToInfo = new Object();
Utils.scrollToInfo.duration = 10;
Utils.scrollToInfo.startPos = 0;
Utils.scrollToInfo.endPos = 0;
Utils.scrollToInfo.currentPos = 0;
Utils.scrollToInfo.Interval = 0;
Utils.scrollToIteration = function(){

 var delta = (Utils.scrollToInfo.endPos - Utils.scrollToInfo.startPos) / 24;
 Utils.scrollToInfo.currentPos = Utils.scrollToInfo.currentPos + delta;
 $(window).scrollTop(Utils.scrollToInfo.currentPos);
 if(Math.abs(Utils.scrollToInfo.currentPos - Utils.scrollToInfo.endPos) < Math.abs(delta+1)){
  window.clearInterval(Utils.scrollToInfo.Interval);
 }
}
Utils.scrollTo = function(selector) {
 if(typeof(selector) == 'string'){
  var oPos = $(selector).offset();
 }else{
  var oPos = new Object();
  oPos.top = selector;
 }
    var oDocumentTop = $(document).scrollTop();
    Utils.scrollToInfo.endPos = oPos.top
    Utils.scrollToInfo.startPos = oDocumentTop;
    Utils.scrollToInfo.currentPos = Utils.scrollToInfo.startPos;
    Utils.scrollToInfo.Interval = window.setInterval(Utils.scrollToIteration,Utils.scrollToInfo.duration);
}
Utils.scrollTo('#result');
", CClientScript::POS_READY); ?>

<?php $this->widget('site.common.widgets.expiredNotification.expiredNotificationWidget', array(
    'time' => appParams('hotel_expirationTime'),
    'header' => false,
    'message' => 'Информация о найденных вами отелях устарела. <a href="">Выполнить поиск снова</a>',
    'showCancel' => false,
    'modalOptions' => array()
)); ?>