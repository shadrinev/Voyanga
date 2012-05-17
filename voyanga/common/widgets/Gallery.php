<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.05.12
 * Time: 17:34
 */
class Gallery extends CWidget
{
    public $model;

    public $attribute;

    public function run()
    {
        $items = array();
        $i = 1;
        foreach ($this->model->{$this->attribute} as $picture)
        {
            $item = array(
                'image' => $picture->url,
                'label' => '#'.$i++.' '.$picture->name
            );
            $items[] = $item;
        }
        $this->widget('bootstrap.widgets.BootCarousel', array('items'=>$items,'options'=>array('interval'=>false)));
    }

}
