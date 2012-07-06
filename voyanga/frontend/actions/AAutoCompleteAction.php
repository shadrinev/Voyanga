<?php
/**
 * Provides drop in auto complete functionality for models.
 * @author Charles Pick
 * @package packages.actions
 */
class AAutoCompleteAction extends CAction
{

    /**
     * The name of the GET or POST parameter that
     * contains the value that should be autocompleted.
     * Defaults to "term"
     * @var string
     */
    public $paramName = "term";

    /**
     * Whether to use POST or GET when auto completing.
     * Defaults to false meaning use GET
     * @var voolean
     */
    public $usePost = false;

    /**
     * The class of the model that is used for
     * auto completion.
     * @var string
     */
    public $modelClass;

    /**
     * The attributes on the model that should be searched
     * when auto completing.
     * @var array
     */
    public $attributes = array();

    /**
     * The template that should be used to create the label for each item.
     * Attribute names enclosed in {brackets} will be replaced with the
     * attribute value.
     * e.g.
     * <pre>
     * $action->labelTemplate = "{firstName} {surname}";
     * </pre>
     * If this value is null, the attributes named in $this->attributes will be used instead
     * @var string
     */
    public $labelTemplate;

    /**
     * The template that should be used to create the value for each item.
     * Attribute names enclosed in {brackets} will be replaced with the
     * attribute value.
     * e.g.
     * <pre>
     * $action->valueTemplate = "{firstName} {surname}";
     * </pre>
     * If this value is null, the attributes named in $this->attributes will be used instead
     * @var string
     */
    public $valueTemplate;


    /**
     * The CDbCriteria configuration that should be used when
     * auto completing.
     * @var array
     */
    public $criteria = array();

    /**
     * The maximum number of results to show at a time.
     * Set this to false to disable limits
     * Defaults to 10.
     * @var integer
     */
    public $maxResults = 10;

    /**
     * The minimum length of the string to auto complete.
     * Defaults to 2.
     * @var integer
     */
    public $minLength = 2;

    public $withHotels = false;

    /**
     * The named scopes that should be applied, in order, when
     * auto completing. This is an array with the following format:
     * <pre>
     * array(
     *     "firstScopeName",
     *     "secondScopeName" => array("secondScopeParameter", "anotherSecondScopeParameter").
     *     "thirdScopeName"
     * )
     * </pre>
     * If the an array item is a string, it will be treated as the name of a scope.
     * If the item is an array, its key will be used as the scope name and the array items
     * will be passed as parameters to the scope.
     * @var array
     */
    public $scopes = array();

    /**
     * The memcache configuration. True - memcache on, false - memcache off.
     * Defaults to true.
     * @var bool
     */
    public $cache = true;

    /**
     * The memcache expire time.
     * Defaults to 0.
     * @var int
     */
    public $cacheExpire = 0;

    /**
     * Runs the widget and displays the JSON for the auto complete
     */
    public function run()
    {

        $this->withHotels = (isset($_GET['withHotels']) and ($_GET['withHotels'] == 1)) ? true : false;

        if (count($this->attributes) == 0)
        {
            throw new CException("No attributes specified for auto complete");
        }
        $response = array();
        if ($this->usePost)
        {
            $value = Yii::app()->request->postParam($this->paramName);
        }
        else
        {
            $value = Yii::app()->request->getParam($this->paramName);
        }
        if ($this->cache)
        {
            //todo: check correctness
            $key = 'autoDcomplete' . md5($value . serialize(get_object_vars($this)));
            if ($val = Yii::app()->cache->get($key))
            {
                header("Content-type: application/json");
                echo $val;
                Yii::app()->end();
            }
        }
        $model = new $this->modelClass;
        foreach ($this->scopes as $scope => $parameters)
        {
            if (is_array($parameters))
            {
                $model = call_user_func_array(array($model, $scope), $parameters);
            }
            else
            {
                $model = $model->{$parameters};
            }
        }
        $criteria = new CDbCriteria($this->criteria);
        if ($this->maxResults)
        {
            $criteria->limit = $this->maxResults;
        }
        $conditions = array();
        $params = array();
        foreach ($this->attributes as $attribute)
        {
            if (strpos($attribute, ':'))
            {
                $tmp = explode(':', $attribute);
                $param = $tmp[0];
                $checker = $tmp[1];
                $conditions[] = "t." . $param . " " . $checker . " :$param";
                $params[":" . $param] = $value;
            }
            else
            {
                $conditions[] = "t." . $attribute . " LIKE :$attribute";
                $params[":" . $attribute] = $value . "%";
            }
            $criteria->params = array_merge($criteria->params, $params);
        }
        $criteria->addCondition(implode(" OR ", $conditions));
        if ($this->withHotels)
            $criteria->addCondition('t.hotelbookId IS NOT NULL');
        foreach ($model->findAll($criteria) as $result)
        {
            $item = array();
            $item['id'] = $result->primaryKey;
            $item['label'] = $this->parseTemplate($this->labelTemplate, $result);
            $item['value'] = $this->parseTemplate($this->valueTemplate, $result);
            $response[] = $item;
        }
        header("Content-type: application/json");
        if (function_exists("json_encode"))
        {
            $val = json_encode($response);
        }
        else
        {
            $val = CJSON::encode($response);
        }
        if ($this->cache)
        {
            Yii::app()->cache->set($key, $val, $this->cacheExpire);
        }
        echo $val;
    }

    /**
     * Parses a template and replaces the list of attribute names with their values.
     * If the given template is null, a list of attribute names from $this->attributes will
     * be used instead.
     * @param string $template The template to use
     * @param Traversable $model The model that contains the
     * @return string The template with attribute names replaced by their values
     */
    protected function parseTemplate($template, Traversable $model)
    {
        $replacements = array();
        if ($template === null)
        {
            $template = array();
            foreach ($this->attributes as $attribute)
            {
                $template[] = "{$attribute}";
            }
            $template = implode(" ", $template);
        }
        preg_match_all('|\{(.+?)\}|is', $template, $matches);
        foreach ($matches[1] as $property)
        {
            if (strpos($property, '.'))
            {
                $path = explode('.', $property);
                $replacements['{' . $property . '}'] = $model->{$path[0]}->{$path[1]};
            }
            else
            {
                $replacements['{' . $property . '}'] = $model->{$property};
            }
        }
        ;
        /*		foreach($model as $attribute => $value) {
              $replacements['{'.$attribute.'}'] = $value;
          }*/
        return strtr($template, $replacements);
    }
}
