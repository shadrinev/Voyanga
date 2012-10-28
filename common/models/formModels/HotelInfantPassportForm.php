<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class HotelInfantPassportForm extends BasePassportForm
{
    public $birthday;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return CMap::mergeArray(parent::rules(), array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'birthday',
                'date', 'format' => 'dd.MM.yyyy'),
        ));
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return CMap::mergeArray(parent::attributeLabels(), array(
                'birthday' => 'Дата рождения (ДД.ММ.ГГГГ)',
            )
        );
    }
}