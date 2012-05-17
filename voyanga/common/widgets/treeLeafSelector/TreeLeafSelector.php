<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.05.12
 * Time: 11:19
 */
class TreeLeafSelector extends CWidget
{

    public $model;

    public $attribute;

    public $form;

    private $_tree;

    private $_elements;

    public function init()
    {
        $relations = $this->model->relations();
        $categoriesClass = $relations[$this->attribute][1];
        $category = new $categoriesClass;
        $roots=$category->roots()->findAll();
        foreach ($roots as $root)
        {
            $this->_tree[$root->id] = $category::model()->findAll(
                array('select'=>'id, title, lft, rgt',
                    'condition'=>'root=:root and id!=:id',
                    'order'=>'lft',
                    'params'=>array(':root'=>$root->id,':id'=>$root->id
                    )
                )
            );

            $id = 0;
            $tmp = $root->title;
            foreach ($this->_tree[$root->id] as $children)
            {
                $tmp .= ' -> ' . $children->title;
                $id = $children->id;
                if ($children->isLeaf())
                {
                    $this->_elements[$id] = $tmp;
                    $this->_elements[$id] = $tmp;
                    $tmp = $root->title;
                }
            }

        }
    }

    public function run()
    {
        $arr = array();
        foreach ($this->model->{$this->attribute} as $cat)
        {
            $arr[] = $cat->primaryKey;
        }
        $this->model->{$this->attribute} = $arr;
        echo $this->form->checkBoxList($this->model, $this->attribute, $this->_elements);
    }
}
