<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Туры' => array('viewer/index'),
    'Создание',
);

?>

<?php $this->widget('site.common.widgets.tourViewer.TourViewerWidget',array('urlToBasket'=>$this->createUrl('/admin/tour/basket/show'), 'urlToBasket'=>$this->createUrl('/tour/basket/show'), 'pathToAirlineImg'=>'http://frontend.voyanga/img/airlines/')); ?>
