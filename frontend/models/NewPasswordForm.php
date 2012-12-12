<?php
/**
 * LoginForm.php
 *
 * @author: antonio ramirez <antonio@clevertech.biz>
 * Date: 7/22/12
 * Time: 8:37 PM
 */

class NewPasswordForm extends CFormModel
{
    public $password;

    public function rules()
    {
        return array(
            array('password', 'required'),
            array('password', 'length', 'max' => 50, 'min' => 6),
        );
    }
}
