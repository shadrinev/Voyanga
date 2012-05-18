<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class PassportForm extends CFormModel
{
    public $id;
    public $firstName;
    public $lastName;
    public $number;
    public $birthday;
    public $series;
    public $documentTypeId;
    public $countryId;
    public $genderId;
    public $parameterName = 'Name Of Param';
    public $parameterUnits = 'Unit';

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'firstName, lastName, number, birthday, documentTypeId, genderId, countryId',
                'required'),
            // email has to be a valid birthday format
            array(
                'birthday',
                'date', 'format' => 'dd.MM.yyyy'),
            array(
                'series',
                'safe'));
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'verifyCode' => 'Verification Code',
            'firstName' => 'Имя',
            'lastName' => 'Фамилия',
            'number' => 'Номер документа',
            'birthday' => 'Дата рождения',
            'document_type_id' => 'Тип документа',
            'gender_id' => 'Пол',
            'series' => 'Серия документа',
            'country_id' => 'Гражданство');
    }

    public function getForm($parent) {
        return new EForm(array(
            'elements' => array(
                "[{$this->id}]firstName"=>array(
                    'type'=>'text',
                    'label' => 'Имя',
                    'after' => $this->parameterUnits,
                    'maxlength'=>32,
                ),
                "[{$this->id}]lastName"=>array(
                    'type'=>'text',
                    'maxlength'=>32,
                    'label' => 'Фамилия',
                    'after' => $this->parameterUnits,
                ),
                "[{$this->id}]number"=>array(
                    'type'=>'text',
                    'maxlength'=>32,
                    'label' => 'Номер документа',
                    'after' => $this->parameterUnits,
                ),
                "[{$this->id}]birthday"=>array(
                    'type'=>'text',
                    'maxlength'=>32,
                    'label' => 'Дата рождения',
                    'after' => $this->parameterUnits,
                ),
                "[{$this->id}]documentTypeId"=>array(
                    'type'=>'dropdownlist',
                    'items'=>array(1=>'Пасспорт РФ',2=>'Загран паспорт', 3=>'св-во о рожд'),
                    'prompt'=>'Тип документа:',
                    'label' => 'Тип документа',
                    'after' => $this->parameterUnits,
                ),
                "[{$this->id}]genderId"=>array(
                    'type'=>'dropdownlist',
                    'items'=>array(1=>'Мужской',2=>'Женский'),
                    'prompt'=>'Пол:',
                    'label' => 'Пол',
                    'after' => $this->parameterUnits,
                ),

            )
        ), $this, $parent, $this->id);
    }
}