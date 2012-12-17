<?php
class ConsoleUser extends CApplicationComponent implements IWebUser
{
    private static $state =array();

    public function init()
    {
        parent::init();
   }

    /**
     * Returns a value that uniquely represents the identity.
     * @return mixed a value that uniquely represents the identity (e.g. primary key value).
     */
    public function getId()
    {
        return 1;
    }

    /**
     * Returns the display name for the identity (e.g. username).
     * @return string the display name for the identity.
     */
    public function getName()
    {
        return 'ADMIN';
    }

    /**
     * Returns a value indicating whether the user is a guest (not authenticated).
     * @return boolean whether the user is a guest (not authenticated)
     */
    public function getIsGuest()
    {
        return false;
    }

    /**
     * Performs access check for this user.
     * @param string $operation the name of the operation that need access check.
     * @param array $params name-value pairs that would be passed to business rules associated
     * with the tasks and roles assigned to the user.
     * @return boolean whether the operations can be performed by this user.
     */
    public function checkAccess($operation, $params=array())
    {
        return true;
    }

    public function getIsAdmin()
    {
        return true;
    }

    public function setFlash($key,$value,$defaultValue=null)
    {
        
    }

    public function loginRequired(){}

    public function getState($key)
    {
        $data = @self::$state[$key];
        return @self::$state[$key];
    }
    public function setState($key,$value)
    {
        self::$state[$key] = $value;
    }
}
