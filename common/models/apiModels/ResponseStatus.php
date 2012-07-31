<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 30.07.12
 * Time: 19:37
 * To change this template use File | Settings | File Templates.
 */
class ResponseStatus
{
    public $status = 0;
    public $errors = array();
    const ERROR_CODE_NO_ERRORS = 0;
    const ERROR_CODE_EMPTY = 1;
    const ERROR_CODE_INTERNAL = 3;
    const ERROR_CODE_EXTERNAL = 4;
    const ERROR_CODE_INVALID = 2;

    public function getErrorsDescription()
    {
        return join(', ', $this->errors);
    }
    public function addErrorDescription($errorDescription)
    {
        $this->errors[] = $errorDescription;
    }
}
