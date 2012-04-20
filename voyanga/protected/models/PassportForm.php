<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class PassportForm extends CFormModel {
    public $id;
    public $first_name;
    public $last_name;
    public $number;
    public $birthday;
    public $series;
    public $document_type_id;
    public $country_id;
    public $gender_id;
    
    /**
     * Declares the validation rules.
     */
    public function rules() {
        return array(
                // first_name, last_name, number, birthday, document_type_id, gender_id are required
                array(
                        'first_name, last_name, number, birthday, document_type_id, gender_id', 
                        'required' ), 
                // email has to be a valid birthday format
                array(
                        'birthday', 
                        'date','format'=>'dd.MM.yyyy' ) );
    }
    
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels() {
        return array(
                'verifyCode' => 'Verification Code', 
                'first_name' => 'Имя', 
                'last_name' => 'Фамилия', 
                'number' => 'Номер документа', 
                'birthday' => 'Дата рождения', 
                'document_type_id' => 'Тип документа', 
                'gender_id' => 'Пол', 
                'series' => 'Серия документа', 
                'country_id' => 'Гражданство' );
    }
}