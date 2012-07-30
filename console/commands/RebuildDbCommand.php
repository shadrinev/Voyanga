<?php
/*
 * This commands rebuilds the database. 
 * That is, it creates a new database if no database exists, or, if one exists destroys the existing one and recreates it
 * 
 * Prerequisites:
 * - params['db.name'], params['db.username'], params['db.password'], set to the proper values
 * 
 * Process:
 * - drops existing database, if one exists
 * - creates a new one
 * - imports base.sql if exists, and if $this->useBaseSql set to true. This is an optional base file, upon which database changes may be added by migrations. Alternatively, database may be built by migrations from the beginning 
 * - runs migrations
 * 
 * - the above is perofrmed for the main database, and if the environment needs a test database, also for the test database that will be named params['db.name'].'_test';
 * 
*/

	function runningOnWindows ()
	{
		return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
	}

	function pth($path)
	{
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	}

	function getPhpPath()
	{
		static $phpPath = false;
		if ($phpPath) return $phpPath;

		if (runningOnWindows()) $phpPath = 'php';
		else if ($path = Yii::app()->params['env.path']) {
			exec('export PATH='.escapeshellarg($path).'; which php', $output, $exit_code);
			if ($exit_code === 0) $phpPath = $output[0];
		}
		if (!$phpPath) $phpPath = '/usr/bin/php';
		return $phpPath;
	}

	function runCommand ($command)
	{
		if (!runningOnWindows())
			$command .= ' 2>&1';
		$result = array ();
		exec ($command, $result);
		foreach($result as $row) echo $row, "\n";
	}

class RebuildDbCommand extends CConsoleCommand
{

	public $noQuestions = false;
	
	public $testEnvironments = array ('private', 'int', 'qa'); //set this to empty array, to avoid working with a test database
	private $_isTestEnv = false;	
	
	public $useBaseSql = false;

    public function getHelp()
	{
		return <<<EOD
USAGE
   ...
DESCRIPTION
   ...
EOD;
	}


	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function actionIndex()
	{
		if( in_array (Yii::app()->params['env.code'], $this->testEnvironments)) 
			$this->_isTestEnv = true;
		
		$dbName = Yii::app()->params['db.name'];

		//connect to the DB server 
		$DSNparts = explode ( ';', Yii::app()->params['db.connectionString']);  
		$serverOnlyDSN = $DSNparts[0]; //because standard dsn is : mysql:host=127.0.0.1;dbname=...
		$db = new CDbConnection($serverOnlyDSN, Yii::app()->params['db.username'], Yii::app()->params['db.password']);
		$db->active = true;

		//drop the, existing database, if it exists indeed
		$dbs = $db->createCommand("show databases")->queryAll();
		if(in_array (array ('Database' => $dbName), $dbs))
		{
			if (!$this->noQuestions)
			{
				echo "\nStop!\n\nThis will completely destroy the existing database ".$dbName."\n\nAre you sure? (Yes/No) ";
				if (strcasecmp(trim(fgets(STDIN)), 'Yes') != 0)
				{
					echo "Cancelled by the user\n";
					Yii::app()->end();
				}
			}

			echo "Destroying existing database ".$dbName."...";
			$db->createCommand("drop database ".$dbName)->execute();
			
			if($this->_isTestEnv)
			{
				if(in_array (array ('Database' => $dbName.'_test'), $dbs))
				{
					echo "Destroying existing test database ".$dbName.'_test'." ...";
					$db->createCommand("drop database ".$dbName.'_test')->execute();
				}
			}
			echo " Done\n";
		}

		//create the database
		echo "Creating new database ".$dbName."...";
		$db->createCommand("create database ".$dbName)->execute();
		echo " Done\n";
		
		if($this->_isTestEnv)
		{
			echo "Creating new database ".$dbName.'_test' . " ...";
			$db->createCommand("create database ".$dbName.'_test')->execute();
			echo " Done\n";
		}

		//reset the connection
		$db->active = false;
		$db = new CDbConnection(Yii::app()->params['db.connectionString'], Yii::app()->params['db.username'], Yii::app()->params['db.password']);
		$db->active = true;
		
		if($this->_isTestEnv)
		{
			$testdb = new CDbConnection(Yii::app()->params['testdb.connectionString'], Yii::app()->params['testdb.username'], Yii::app()->params['testdb.password']);
			$testdb->active = true;
		}

		//import the base sql file, uppon which migrations are built
		if ($this->useBaseSql)
		{
			$baseSqlFile = Yii::getPathOfAlias ('common.migrations.base').'.sql';
			echo "Importing base.sql...";
			$statements = explode(";", file_get_contents($baseSqlFile));
				//dani: are you absolutely sure, no *data* in this file contains ';' ? Even if this is the case, generally it doesn't seem a safe assumption when importing an sql file
			foreach($statements as $statement)
			{
				if(empty($statement))
					continue;
				$db->pdoInstance->exec($statement);
				if($this->_isTestEnv)
				{
					$testdb->pdoInstance->exec($statement);
				}
			}
			echo " Done\n";
			echo "\n";			
		}


		//running migrations
		echo "Running migrations...\n";
		runCommand(getPhpPath()." ".Yii::getPathOfAlias('root.yiic')." migrate --interactive=0 ");


		if($this->_isTestEnv)
		{
			echo "Running migrations for test db...\n";
			runCommand(getPhpPath()." ".Yii::getPathOfAlias('root.yiic')." migrate --interactive=0 --connectionID=testdb");
			echo "Done\n";
		}
    }

}
