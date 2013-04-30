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
    public $unique_id;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'contactPhone, contactEmail, unique_id', 'required',
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

    public function tryToPrefetch()
    {
        $unique_id = Yii::app()->request->cookies->contains('unique_id') ? Yii::app()->request->cookies['unique_id']->value : false;
        $orderBooking = $this->getOrderBookingBySessionId();
        if (!$orderBooking)
        {
            $this->unique_id = md5(time().rand(0, 100000));
            Yii::app()->request->cookies['unique_id'] = new CHttpCookie('unique_id', $this->unique_id, array('expire'=>time()+2*30*24*3600));
            $orderBooking = $this->getOrderBookingByUser();
        }
        else
        {
            $this->unique_id = $unique_id;
        }

        if (!$orderBooking)
            return;
        if ((isset($orderBooking->flightBookers[0])) && (isset($orderBooking->flightBookers[0]->flightBookingPassports[0])))
        {
            $this->fillAttributes($orderBooking);
        }
    }

    private function getOrderBookingBySessionId()
    {
        $criteria = new CDbCriteria();
        $unique_id = Yii::app()->request->cookies->contains('unique_id') ? Yii::app()->request->cookies['unique_id']->value : false;
        if (!$unique_id)
            return;
        $criteria->together = true;
        $criteria->with = array('flightBookers', 'flightBookers.flightBookingPassports'=>array('joinType'=>'RIGHT JOIN'));
        $criteria->addCondition('firstName is not null');
        $criteria->order='t.id desc, flightBookingPassports.sequence, flightBookingPassports.id desc';
        $criteria->addCondition('unique_id=:uniq');
        $criteria->params = array(':uniq'=>$unique_id);
        $criteria->limit=1;
        $orderBooking = OrderBooking::model()->find($criteria);
        return $orderBooking;
    }

    private function getOrderBookingByUser()
    {
        $criteria = new CDbCriteria();
        $userId = Yii::app()->user->isGuest ? 0 : Yii::app()->user->id;
        if ($userId == 0)
            return;
        $criteria->addCondition('userId=:userId');
        $criteria->params = array(':userId'=>$userId);
        $criteria->together = true;
        $criteria->with = array('flightBookers', 'flightBookers.flightBookingPassports'=>array('joinType'=>'RIGHT JOIN'));
        $criteria->addCondition('firstName is not null');
        $criteria->order='t.id desc, flightBookingPassports.sequence, flightBookingPassports.id desc';
        $criteria->limit=1;
        $orderBooking = OrderBooking::model()->find($criteria);
        return $orderBooking;
    }

    private function fillAttributes($orderBooking)
    {
        $this->contactEmail = $orderBooking->email;
        $this->contactPhone = $orderBooking->phone;
    }
}