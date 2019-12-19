<?php

if(empty($_SERVER['DOCUMENT_ROOT']))
{
	throw new \Exception("Environment config \$_SERVER\['DOCUMENT_ROOT'\] is not defined!");
}

define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS',true);
define('BX_NO_ACCELERATOR_RESET', true);
define('NO_AGENT_CHECK', true);
define('NO_AGENT_STATISTIC', true);
define("BX_CRONTAB", true);
define("DisableEventsCheck", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (php_sapi_name() === 'cli')
{
	header_remove();
	while (ob_end_flush()){}

	@session_destroy();
}
