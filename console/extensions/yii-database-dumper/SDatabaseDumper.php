<?php

/**
 * Creates DB dump.
 *
 * Usage:
 * <pre>
 *      Yii::import('ext.yii-database-dumper.SDatabaseDumper');
 *      $dumper = new SDatabaseDumper;
 *      // Get path to backup file
 *      $file = Yii::getPathOfAlias('webroot.protected.backups').DIRECTORY_SEPARATOR.'dump_'.date('Y-m-d_H_i_s').'.sql';
 *
 *      // Gzip dump
 *      if(function_exists('gzencode'))
 *          file_put_contents($file.'.gz', gzencode($dumper->getDump()));
 *      else
 *          file_put_contents($file, $dumper->getDump());
 * </pre>
 */
class SDatabaseDumper
{

	/**
	 * Dump all tables
	 * @return string sql structure and data
	 */
	public function getDump()
	{
		ob_start();

        echo '
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
'.PHP_EOL;

        foreach ($this->getDbs() as $db)
        {
           $this->dumpDb($db);
        }

        echo '
SET FOREIGN_KEY_CHECKS=1;
'.PHP_EOL;
		$result=ob_get_contents();
		ob_end_clean();
		return $result;
	}

    public function dumpDb($db)
    {
        echo '
--
-- Dump of database `'.$db.'`
--
'.PHP_EOL;

        $dbObject = Yii::app()->$db;
        $dbRaw = $dbObject->connectionString;
        $dbName = substr($dbRaw, strrpos($dbRaw,'=')+1);

        echo 'DROP DATABASE IF EXISTS '.$dbName.';'.PHP_EOL;
        echo 'CREATE DATABASE '.$dbName.';'.PHP_EOL;
        echo 'USE '.$dbName.';'.PHP_EOL;

        $allTables = array_keys($this->getTables($db));
        $excludeTables = $this->getExcludeTables($db);
        $tables = array_diff($allTables, $excludeTables);
        $excludeTablesData = $this->getExcludeTablesData();
        $limitTables = $this->getLimitForTables();
        if (isset($limitTables[$db]))
        {
            $limitTables = $limitTables[$db];
        }
        else
        {
            $limitTables = array();
        }
        foreach($tables as $key)
        {
            $limit = 10000; //all
            if (in_array($key, array_keys($limitTables)))
                $limit = $limitTables[$key];
            if ((isset($excludeTablesData[$db])) and (in_array($key, $excludeTablesData[$db])))
            {
                $limit = 0;
            }
            $this->dumpTable($db, $key, $limit);
        }
    }

	/**
	 * Create table dump
	 * @param $tableName
	 * @return mixed
	 */
	public function dumpTable($db, $tableName, $limit)
	{
		$db = Yii::app()->$db;
		$pdo = $db->getPdoInstance();

		echo '
--
-- Structure for table `'.$tableName.'`
--
'.PHP_EOL;
		echo 'DROP TABLE IF EXISTS '.$db->quoteTableName($tableName).';'.PHP_EOL;

		$q = $db->createCommand('SHOW CREATE TABLE '.$db->quoteTableName($tableName).';')->queryRow();
		echo $q['Create Table'].';'.PHP_EOL.PHP_EOL;

        if ($limit == 0)
        {
            echo '-- NO BACKUP DATA FOR THIS TABLE '.$limit.PHP_EOL;
            return;
        }

        if ($limit < 0)
		    $rows = $db->createCommand('SELECT * FROM '.$db->quoteTableName($tableName).';')->queryAll();
        else
            $rows = $db->createCommand('SELECT * FROM '.$db->quoteTableName($tableName).' limit '.$limit.';')->queryAll();

        echo '
--
-- Data for table `'.$tableName.'`
--
'.PHP_EOL;

       if ($limit>0)
            echo '-- Limit '.$limit.PHP_EOL;

		if(empty($rows))
			return;

		$attrs = array_map(array($db, 'quoteColumnName'), array_keys($rows[0]));
		echo 'INSERT INTO '.$db->quoteTableName($tableName).''." (", implode(', ', $attrs), ') VALUES'.PHP_EOL;
		$i=0;
		$rowsCount = count($rows);
		foreach($rows AS $row)
		{
			// Process row
			foreach($row AS $key => $value)
			{
				if($value === null)
					$row[$key] = 'NULL';
				else
					$row[$key] = $pdo->quote($value);
			}

			echo " (", implode(', ', $row), ')';
			if($i<$rowsCount-1)
				echo ',';
			else
				echo ';';
			echo PHP_EOL;
			$i++;
		}
		echo PHP_EOL;
		echo PHP_EOL;
	}

	/**
	 * Get mysql tables list
	 * @return array
	 */
	public function getTables($db)
	{
		$db = Yii::app()->$db;
		return $db->getSchema()->getTables();
	}

    public function getDbs()
    {
        return array(
            'db',
            'logdb',
            'userDb',
            'backendDb',
        );
    }

    public function getExcludeTables($db)
    {
        return array();
    }

    public function getExcludeTablesData()
    {
        return array(
            'db' => array(
                'flight_cache',
                'hotel_cache'
            )
        );
    }

    public function getLimitForTables()
    {
        return array(
            'db' => array(
                'hotel_rating'=>1000,
            )
        );
    }
}
