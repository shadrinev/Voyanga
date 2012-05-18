<?php
/**
 * EAdvancedRelations class file.
 *
 * @author Jeanluca
 * @link http://www.yiiframework.com/extension/eadvancedarbehavior/
 * @version 2.3
 */

class EAdvancedArBehavior extends CActiveRecordBehavior
{
    public $useOnUpdate = TRUE; // set empty fields to NULL when updating (NOTE: on insert nullable empty attributes are set to NULL by default)
    public $useNullOnTimestamp = FALSE; // if TRUE it will set timestamp values with a default value of $timestampDefault to NULL
    public $onUpdateColumn = NULL; // define the column-name holding the modification time
    public $timestampDefault = '0000-00-00 00:00:00'; // default timestamp value

    private $relations = null;

    public function afterSave($event)
    {
        $this->fixRelations();
        $this->writeManyManyTables();
        return parent::afterSave($event);
    }

    public function beforeSave($event)
    {
        $this->ensureNULL();
        $this->fixBELONGS_TO();
        return parent::beforeSave($event);
    }

    public function fixBELONGS_TO()
    {
        $owner = $this->owner;
        foreach ($owner->relations() as $key => $relation)
        {
            /* $key         -> relation name
            * $relation[1] -> related table
            * $relation[2] -> foreignkey field
            */
            if ($relation['0'] == CActiveRecord::BELONGS_TO) // ['0'] equals relationType
            {
                Yii::trace('set BELONGS_TO foreign-key field for ' . get_class($owner), 'system.db.ar.CActiveRecord');
                if (!isset($this->relations) || in_array($key, $this->relations))
                {
                    if (isset($owner->{$key}))
                    {
                        $owner->$relation[2] = $owner->{$key}->primaryKey;
                    }
                }
            }
        }

    }

    public function fixRelations()
    {
        $owner = $this->owner;

        foreach ($owner->relations() as $key => $relation)
        {
            /* $key         -> relation name
            * $relation[1] -> related table
            * $relation[2] -> foreignkey field
            */
            if (!isset($this->relations) || in_array($key, $this->relations))
            {
                if ($relation['0'] == CActiveRecord::HAS_ONE) // ['0'] equals relationType
                {
                    // update the related table which contains the foreignkey (BELONGS_TO)
                    Yii::trace('set HAS_ONE foreign-key field for ' . get_class($owner), 'system.db.ar.CActiveRecord');
                    $related = $owner->{$key};
                    if (isset($related) && (empty($related->{$relation[2]}) || $related->{$relation[2]} != $owner->primaryKey || (isset($this->relations) && in_array($key, $this->relations))))
                    {
                        $related->{$relation[2]} = $owner->primaryKey;
                        $related->save(false);
                    }
                }
                else if ($relation['0'] == CActiveRecord::HAS_MANY)
                {
                    Yii::trace('set HAS_MANY foreign-key field for ' . get_class($owner), 'system.db.ar.CActiveRecord');
                    $related = $owner->{$key};
                    if (isset($related))
                    {
                        foreach ($related as $obj)
                        {
                            if (empty($obj->{$relation[2]}) || $obj->{$relation[2]} != $owner->primaryKey)
                            {
                                $obj->{$relation[2]} = $owner->primaryKey;
                                $obj->save(false);
                            }
                        }
                    }
                }
            }
        }
    }

    public function ensureNULL()
    {
        $owner = $this->getOwner();

        if ($owner->getIsNewRecord() || $this->useOnUpdate)
        {
            $mysqlOnly = $this->useNullOnTimestamp && $owner->dbConnection->driverName == 'mysql';

            foreach ($owner->getTableSchema()->columns as $column)
            {
                $value = trim($owner->getAttribute($column->name));
                if (($column->allowNull && $value === '') // set nullable empty fields to NULL
                    ||
                    ($mysqlOnly == 1 && ($owner->isNewRecord && $value == $this->timestampDefault && $column->name != $this->onUpdateColumn // set insert-time field to NULL
                        ||
                        !$owner->isNewRecord && $column->name == $this->onUpdateColumn // set update-time field to NULL
                    )
                    )
                )
                {
                    $column->allowNull = TRUE;
                    $owner->setAttribute($column->name, null);
                }
            }
        }
        return TRUE;
    }

    /**
     * At first, this function cycles through each MANY_MANY Relation. Then
     * it checks if the attribute of the Object instance is an integer, an
     * array or another ActiveRecord instance. It then builds up the SQL-Query
     * to add up the needed Data to the MANY_MANY-Table given in the relation
     * settings.
     */
    public function writeManyManyTables()
    {
        Yii::trace('writing MANY_MANY data for ' . get_class($this->owner), 'system.db.ar.CActiveRecord');

        foreach ($this->owner->relations() as $key => $relation)
        {
            if ($relation['0'] == CActiveRecord::MANY_MANY) // ['0'] equals relationType
            {
                if (!isset($this->relations) || in_array($key, $this->relations))
                {
                    if (isset($this->owner->$key))
                    { // MANY_MANY is set
                        if (is_object($this->owner->$key) || is_numeric($this->owner->$key))
                        {
                            if (is_numeric($this->owner->$key) || $this->findOwner($this->owner->$key) == FALSE)
                            {
                                $this->executeManyManyEntry($this->makeManyManyDeleteCommand(
                                    $relation[2],
                                    $this->owner->{$this->owner->tableSchema->primaryKey}));
                                $this->executeManyManyEntry($this->owner->makeManyManyInsertCommand(
                                    $relation[2],
                                    (is_object($this->owner->$key))
                                        ? $this->owner->$key->{$this->owner->$key->tableSchema->primaryKey}
                                        : $this->owner->{$key}));
                            }
                        }
                        else if (is_array($this->owner->$key))
                        {
                            list($toBeRemovedArray, $toBeRemovedHash) = $this->loadOldRelationsFromDB($relation[2]);
                            $diff = array_diff($toBeRemovedArray, $this->convertObjsToPrimaryKeyArray($this->owner->{$key}));

                            if (sizeof($diff) > 0)
                            {
                                $this->executeManyManyEntry($this->makeManyManyDeleteCommand(
                                    $relation[2],
                                    $this->owner->primaryKey,
                                    $diff
                                ));
                            }
                            $sql = null;
                            foreach ($this->owner->$key as $foreignobject)
                            {
                                if (empty($toBeRemovedHash[$foreignobject->primaryKey]))
                                { // new relation
                                    $sql = $this->makeManyManyInsertCommand(
                                        $relation[2],
                                        (is_object($foreignobject))
                                            ? $foreignobject->{$foreignobject->tableSchema->primaryKey}
                                            : $foreignobject, $sql);
                                }
                            }
                            if (isset($sql))
                                $this->executeManyManyEntry($sql);
                        }
                    }
                }
            }
        }
    }

    private function loadOldRelationsFromDB($model)
    {
        $secondRelation = trim($this->getManyManySecondRelationName($model));
        $reader = $this->owner->dbConnection->createCommand(
            sprintf('SELECT %s FROM %s WHERE %s = %d',
                $secondRelation,
                $this->getManyManyTable($model),
                $this->getManyManyFirstRelationName($model),
                $this->owner->primaryKey
            )
        )->query();

        $listA = array();
        $listH = array();
        foreach ($reader as $row)
        {
            $listA[] = $row[$secondRelation];
            $listH['' . $row[$secondRelation]] = TRUE;
        }
        return array($listA, $listH);
    }

    private function convertObjsToPrimaryKeyArray($list)
    {
        $primaryKeys = array();
        foreach ($list as $obj)
            $primaryKeys[] = $obj->primaryKey;
        return $primaryKeys;
    }

    // We can't throw an Exception when this query fails, because it is possible
    // that there is not row available in the MANY_MANY table, thus execute()
    // returns 0 and the error gets thrown falsely.
    public function executeManyManyEntry($query)
    {
        if (isset($query))
            return $this->owner->dbConnection->createCommand($query)->execute();
    }

    // It is important to use insert IGNORE so SQL doesn't throw an foreign key
    // integrity violation
    public function makeManyManyInsertCommand($model, $rel, $sql = null)
    {
        if (isset($sql)) // append to INSERT statement
            return sprintf('%s,(\'%s\', \'%s\')', $sql, $this->owner->{$this->owner->tableSchema->primaryKey}, $rel);
        else // create INSERT statement
            return sprintf("insert ignore into %s values ('%s', '%s')", $model, $this->owner->{$this->owner->tableSchema->primaryKey}, $rel);
    }

    public function makeManyManyDeleteCommand($model, $rel, $removeIDs = null)
    {
        $sql = sprintf('delete ignore from %s where %s = \'%s\'', $this->getManyManyTable($model), $this->getManyManyFirstRelationName($model), $rel);
        if (isset($removeIDs) && sizeof($removeIDs) > 0)
            $sql .= sprintf(' AND %s IN (\'%s\')', $this->getManyManySecondRelationName($model), implode("','", $removeIDs));
        return $sql;
    }

    public function getManyManyTable($model)
    {
        if (($ps = strpos($model, '(')) !== FALSE)
        {
            return substr($model, 0, $ps);
        }
        else
            return $model;
    }

    /**
     * @param string $model  ( e.g: tbl_project_user_assignment(user_id, project_id) )
     * @return string (e.g:  user_id )
     */
    public function getRelationNameForDeletion($model)
    {
        preg_match('/\((.*),/', $model, $matches);
        return substr($matches[0], 1, strlen($matches[0]) - 2);
    }

    public function getManyManyFirstRelationName($model)
    {
        return $this->getRelationNameForDeletion($model);
    }

    public function getManyManySecondRelationName($model)
    {
        preg_match('/,(.*?)\)/', $model, $matches);
        return $matches[1];
    }

    public function beforeDelete($event)
    {
        Yii::trace('deleting MANY_MANY data for ' . get_class($this->owner), 'system.db.ar.CActiveRecord');
        foreach ($this->owner->relations() as $key => $relation)
        {
            if ($relation['0'] == CActiveRecord::MANY_MANY) // ['0'] equals relationType
            {
                $this->executeManyManyEntry($this->makeManyManyDeleteCommand(
                    $relation[2],
                    $this->owner->{$this->owner->tableSchema->primaryKey}, array()));
            }
        }
        return parent::beforeDelete($event);
    }

    public function ignoreRelationsExcept($relations = array())
    {
        $this->relations = $relations;
    }
}
