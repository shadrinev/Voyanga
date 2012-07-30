<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class HotelAdultPassportForm extends BasePassportForm
{
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    public $genderId;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return CMap::mergeArray(parent::rules(), array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'genderId', 'required'
            ),
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
            'genderId' => 'Пол',
            )
        );
    }

    public function getPossibleGenders()
    {
        return array(
            self::GENDER_MALE => 'Муж',
            self::GENDER_FEMALE => 'Жен'
        );
    }
}