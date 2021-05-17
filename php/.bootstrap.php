<?php

if (empty($_SERVER['DOCUMENT_ROOT']))
{
	throw new \Exception("Environment config \$_SERVER\['DOCUMENT_ROOT'\] is not defined!");
}

require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/cli/bootstrap.php');

@header_remove();
while (@ob_end_flush()){}


$runBxTestLib = (function()
{
	global $argv;

	if (isset($_SERVER['argv']))
	{
		$argv = &$_SERVER['argv'];
	}

	$argTestPos = -1;
	$argBootstrapPos = -1;
	$argTestDirPos = -1;
	$argTeamCityPos = -1;
	$argCacheFilePos = -1;
	$argTestSuffix = '.php';

	foreach ($argv as $argPos => $arg)
	{
		$arg = trim(str_replace('\\', '/', $arg));
		$sufLength = mb_strlen($argTestSuffix);

		// check if it is path to test case class
		if (
			$argTestPos < 0 &&
			mb_strpos($arg, '/tests/') !== false &&
			mb_substr($arg, -$sufLength, $sufLength) === $argTestSuffix &&
			is_file($arg)
		)
		{
			$content = file_get_contents($arg);
			if(mb_strpos($content, 'CBitrix'.'TestCase'))
			{
				$argTestPos = $argPos;
			}
			unset($content);
		}
		// directory with tests
		elseif (
			$argTestDirPos < 0 &&
			mb_strpos($arg, '/tests') !== false &&
			is_dir($arg)
		)
		{
			$dirList = function ($arg) use (&$dirList, $argTestSuffix)
			{
				$sufLength = mb_strlen($argTestSuffix);
				$list = glob($arg.'/*');
				foreach ($list as $entry)
				{
					if (mb_substr(basename($entry), 0, 1) === '.')
					{
						continue;
					}
					if (mb_substr($entry, -$sufLength, $sufLength) === $argTestSuffix && is_file($entry))
					{
						yield $entry;
					}
					elseif (is_dir($entry))
					{
						foreach ($dirList($entry) as $subEntry)
						{
							yield $subEntry;
						}
					}
				}
			};
			foreach ($dirList(rtrim($arg, '/')) as $entry)
			{
				$content = file_get_contents($entry);
				if(mb_strpos($content, 'CBitrix'.'TestCase'))
				{
					$argTestDirPos = $argPos;
					break;
				}
			}
			unset($content, $dirList, $entry);
		}
		// check argument for PhpStorm integration
		elseif ($arg === '--teamcity')
		{
			$argTeamCityPos = $argPos;
		}
		elseif ($arg === '--bootstrap')
		{
			$argBootstrapPos = $argPos;
		}
		elseif (mb_strpos($arg, '--cache-result-file') !== false)
		{
			$argCacheFilePos = $argPos;
		}
		elseif (mb_strpos($arg, '--test-suffix') !== false)
		{
			$argTestSuffix = $argv[$argPos + 1];
		}
	}

	if ($argTestPos > 0 || $argTestDirPos > 0)
	{
		if ($argCacheFilePos > 0)
		{
			unset($argv[$argCacheFilePos]);
		}
		// we have to move argument --teamcity before file or directory argument
		// put to before --bootstrap
		if ($argTeamCityPos > 0)
		{
			unset($argv[$argTeamCityPos]);
		}
		if ($argBootstrapPos > 0)
		{
			$argv = array_merge(
				array_slice($argv, 0, $argBootstrapPos),
				['--teamcity'],
				array_slice($argv, $argBootstrapPos)
			);
		}

		return true;// run old phpunit
	}

	return false;
})();


// run old phpunit test
if ($runBxTestLib && \Bitrix\Main\Loader::includeModule('bxtest') && class_exists('CBitrix'.'TestCase'))
{
	\PHPUnit_TextUI_Command::main();
	exit;
}
else
{
	//make autoloading for the tests directories
	require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/bxtest/lib/loader.php';
	\Bitrix\Main\Loader::registerHandler([\Bitrix\Bxtest\Loader::class, 'autoLoad']);
}