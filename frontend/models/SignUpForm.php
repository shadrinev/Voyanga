<?php
/**
 * LoginForm.php
 *
 * @author: antonio ramirez <antonio@clevertech.biz>
 * Date: 7/22/12
 * Time: 8:37 PM
 */

class SignUpForm extends CFormModel
{
    public $email;
    public $password;

    /**
     * Model rules
     * @return array
     */
    public function rules()
    {
        return array(
            array('password, email', 'required'),
            array('password', 'length', 'max' => 50, 'min' => 6),
            array('email', 'email'),
            array('email', 'length', 'max' => 125),
        );
    }
}
