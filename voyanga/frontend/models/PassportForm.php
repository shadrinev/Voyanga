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

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'firstName, lastName, number, birthday, documentTypeId, genderId',
                'required'),
            // email has to be a valid birthday format
            array(
                'birthday',
                'date', 'format' => 'dd.MM.yyyy'));
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
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
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
                "[{$this->parameterId}]firstName"=>array(
                    'type'=>'text',
                    'label' => $this->parameterName,
                    'after' => $this->parameterUnits,
                    'maxlength'=>32,
                ),
                "[{$this->parameterId}]lastName"=>array(
                    'type'=>'text',
                    'maxlength'=>32,
                    'label' => $this->parameterName,
                    'after' => $this->parameterUnits,
                ),
                "[{$this->parameterId}]number"=>array(
                    'type'=>'text',
                    'maxlength'=>32,
                    'label' => $this->parameterName,
                    'after' => $this->parameterUnits,
                ),
                "[{$this->parameterId}]birthday"=>array(
                    'type'=>'text',
                    'maxlength'=>32,
                    'label' => $this->parameterName,
                    'after' => $this->parameterUnits,
                ),
                "[{$this->parameterId}]documentTypeId"=>array(
                    'type'=>'dropdownlist',
                    'items'=>array(1=>'Пасспорт РФ',2=>'Загран паспорт', 3=>'св-во о рожд'),
                    'prompt'=>'Тип документа:',
                    'label' => $this->parameterName,
                    'after' => $this->parameterUnits,
                ),
                "[{$this->parameterId}]genderId"=>array(
                    'type'=>'dropdownlist',
                    'items'=>array(1=>'Мужской',2=>'Женский'),
                    'prompt'=>'Пол:',
                    'label' => $this->parameterName,
                    'after' => $this->parameterUnits,
                ),

            )
        ), $this, $parent, $this->parameterId);
    }
}