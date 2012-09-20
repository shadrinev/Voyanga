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

<?php $this->widget('site.common.widgets.tourViewer.TourViewerWidget',array(
    'urlToBasket'=>$this->createUrl('/tour/basket/show'),
    'urlToConstructor'=>$this->createUrl('/tour/constructor/new'),
    'pathToAirlineImg'=>'http://test.voyanga.com/img/airlines/'));
?>
