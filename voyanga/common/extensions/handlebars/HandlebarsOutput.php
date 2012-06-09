<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 17:09
 */
class HandlebarsOutput extends CWidget
{
    public $id;
    public $compileVariable;
    public $contextVariable;

    public function run()
    {
        if ($this->id === null)
            $this->id = $this->getId();
        echo "<div id='{$this->id}'></div>";
        Yii::app()->clientScript->registerScript('handlebars-output-'.$this->id, "
            var data = {$this->compileVariable}({$this->contextVariable});
            $('#{$this->id}').html(data);
        ");
    }
}
