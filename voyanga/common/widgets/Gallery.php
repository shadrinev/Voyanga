<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.05.12
 * Time: 17:34
 */
class Gallery extends CWidget
{
    public $id;

    public $model;

    public $attribute;

    public function run()
    {
        $items = array();
        $i = 1;
        $total = count($this->model->{$this->attribute});
        foreach ($this->model->{$this->attribute} as $picture)
        {
            $item = array(
                'image' => $picture->url,
                'alt'   => $picture->name,
                'label' => '#'.$i++.' / '.$total.' '.$picture->name
            );
            $items[] = $item;
        }
        $this->widget('bootstrap.widgets.BootCarousel', array(
            'id' => $this->id == null ? null : $this->id,
            'items'=>$items,
            'options'=>array('interval'=>false),
         )
        );
    }

}
