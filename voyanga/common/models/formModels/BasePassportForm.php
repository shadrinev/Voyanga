<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class BasePassportForm extends CFormModel
{
    public $id;
    public $firstName;
    public $lastName;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'firstName, lastName',
                'required'),
           );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'firstName' => 'Имя',
            'lastName' => 'Фамилия',
        );
    }
}