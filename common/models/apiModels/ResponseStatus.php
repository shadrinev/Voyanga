<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 30.07.12
 * Time: 19:37
 * To change this template use File | Settings | File Templates.
 */
class ResponseStatus extends CModel
{
    const ERROR_CODE_NO_ERRORS = 0;
    const ERROR_CODE_EMPTY = 1;
    const ERROR_CODE_INTERNAL = 2;
    const ERROR_CODE_EXTERNAL = 3;

    public $unhandledExceptions = array(self::ERROR_CODE_EXTERNAL, self::ERROR_CODE_INTERNAL);

    public $responseStatus = self::ERROR_CODE_NO_ERRORS;

    public function attributeNames()
    {
        return array('responseStatus');
    }

    public function hasErrors()
    {
        if (in_array($this->responseStatus, $this->unhandledExceptions))
            return true;
        return false;
    }
}
