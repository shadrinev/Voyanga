<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class BookingForm extends CFormModel
{
    public $firstName;
    public $contactPhone;
    public $passports;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'contactPhone, firstName, passports',
                'required',
            ),
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

    public function getForm() {
        $form = new EForm(require(Yii::getPathOfAlias('application.views.site.bookingForm').'.php'), $this);
        $elements = $form->getElements();

        $subForm = new EForm(array('elements' => array()), null, $form); // Sub-form to act as a container for the parameter forms.
        $subForm->visible = true;
        $subForm->title = 'Passports';// Title to make it a fieldset
        $subFormElements = $subForm->getElements();

        if($this->passports)
        {
            foreach ($this->passports as $parameterId => $parameter)
            {
                $subFormElements->add($parameterId, $parameter->getForm($subForm));
            }
        }

        $elements->add('passports', $subForm);

        return $form;
    }

    /*public function getForm() {
        $form = new CForm($this->_form, $this);
        $elements = $form->getElements();

        $subForm = new CForm(array('elements' => array()), null, $form); // Sub-form to act as a container for the parameter forms.
        $subForm->title = 'Parameters';// Title to make it a fieldset
        $subFormElements = $subForm->getElements();

        // NOTE:: parameters were set earlier as related models to the product
        foreach ($this->passports as $parameterId => $parameter)
            $subFormElements->add($parameterId, $parameter->getForm($subForm));

        $elements->add('passports', $subForm);

        return $form;
    }*/
}