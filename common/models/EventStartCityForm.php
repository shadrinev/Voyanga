<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 03.09.12 15:53
 */
class EventStartCityForm extends CFormModel
{
    public $id;
    public $name;

    public function rules()
    {
        return array(
            array('id', 'required')
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Стартуем из города'
        );
    }
}
