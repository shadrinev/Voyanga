#!/usr/bin/php
<?php
/*
 * Post deployment script.
 * 
 * Scope:
 * - Sets permissions (mainly write permissions wherever needed)
 * - Creates some directories
 * - Flushes runtime directories
 * - Runs migrations (will not on your local machine, unless you explicitly ask it to)
 */
		if ($argc < 2)
		{
			echo "\nUsage:\n\n";
			echo "php ".$argv[0]." environmentType\n";
			echo "\nenvironmentType can be: int, qa, prod, demo, private (private is your PC)\n";
			exit();
		}

		$runningOnWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

		$envType = $argv[1];
		$root = realpath (dirname(__FILE__)."/../..")."/";

		function pth($path)
		{
			return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		}

		function getPhpPath()
		{
			global $runningOnWindows;
			if ($runningOnWindows) return "php";
			else return '/usr/bin/php';
		}

		function runCommand ($command)
		{
			global $runningOnWindows;
			if (!$runningOnWindows)
				$command .= ' 2>&1';
			echo "Running command:\n $command ";
			$result = array ();
			exec ($command, $result);
			echo "\nResult is: \n";
			foreach($result as $row) echo $row, "\n";
			echo "========================================================\n";
		}

		function createDirIfNotExists ($path)
		{
			if (!file_exists($path))
			 mkdir ($path);
		}

		function rmdirRecursive($path)
		{
			global $runningOnWindows;
			if (!file_exists($path)) return;
			
			if ($runningOnWindows)
				runCommand("rd /S /Q ".$path);
			else
				runCommand("/bin/rm -rf ".$path);
		}

		if (!$runningOnWindows)
		{
			$result = array ();
			echo "Running as:"; exec('/usr/bin/whoami 2>&1', $result);
			foreach($result as $row) echo $row, "\n";
		}

// Below is legacy:
// 
//		//Copy *-environmnetName.php to *-env.php
//		$directories = array (pth($root."common/config/"), pth($root."frontend/config/"), pth($root."console/config/"), pth($root."api/config/"));
//
//		//choose environemnet specific configs
//		foreach ($directories as $dir)
//		{
//			$files = glob (pth($dir."environments/"."*-".$envType.'.php'));
//			foreach ($files as $srcFile)
//			{
//				$pattern = "/-".$envType.".php$/";
//				$dstFile = preg_replace ($pattern, "-env.php", $srcFile);
//				$dstFile = preg_replace ('/environments\\\|environments\//', "", $dstFile); // remove "environments/" or "environments\" from the path
//				$res = copy ($srcFile, $dstFile);
//				if (!$res)
//					echo "Error. Failed to copy $srcFile to $dstFile\n";
//				chmod ($dstFile, 0644); // for some reason after copying the permissions are wrong
//			}
//		}

		//Create and flush assets directory
		rmdirRecursive(pth($root."frontend/www/assets"));
		createDirIfNotExists(pth($root."frontend/www/assets"));

		// runtime
		createDirIfNotExists(pth($root."frontend/runtime"));
		createDirIfNotExists(pth($root."console/runtime"));
		createDirIfNotExists(pth($root."api/runtime"));

		//Setting permissions
		chmod (pth($root."frontend/runtime"), 02775); //permissions with setguid
		chmod (pth($root."console/runtime"), 02775);
		chmod (pth($root."api/runtime"), 02775);
		chmod (pth($root."frontend/www/assets"), 02775);

		//applying migrations
		//adding in ability to specify to not migrate even when not in local environment. Needed for our newclient script 
		if ( ($envType != 'private' && !in_array('no-migrate', $argv)) || in_array ('migrate', $argv)) // for local machines, I prefer to leave the control on when to migrate, to developers
		{
			runCommand (getPhpPath().' '.$root."yiic migrate --interactive=0");
			if(in_array ($envType, array ('private', 'int', 'qa'))) 
				runCommand (getPhpPath().' '.$root."yiic migrate --interactive=0 --connectionID=testdb");
		}

		echo "Done.\n";

?>
