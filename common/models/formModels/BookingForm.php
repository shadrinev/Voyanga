<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class BookingForm extends CFormModel
{
    public $contactEmail;
    public $contactPhone;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'contactPhone, contactEmail', 'required',
            ),
            array('contactEmail', 'email')
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
            'contactEmail' => 'Контактный email',
            'contactPhone' => 'Контактный телефон',
        );
    }

    public function getForm() {
        $form = new EForm(require(Yii::getPathOfAlias('application.views.site.bookingForm').'.php'), $this);
        $elements = $form->getElements();

        $subForm = new EForm(array('elements' => array()), new AviaPassportForm(), $form); // Sub-form to act as a container for the parameter forms.
        $subForm->visible = true;
        $subForm->title = 'Passports';// Title to make it a fieldset
        $subFormElements = $subForm->getElements();

        if($this->passports)
        {
            foreach ($this->passports as $parameterId => $parameter)
            {
                //VarDumper::dump($parameter->getForm($subForm));
                $subFormElements->add($parameterId, $parameter->getForm($subForm));
            }
        }

        $elements->add('passports', $subForm);

        return $form;
    }
}