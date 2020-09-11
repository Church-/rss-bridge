<?php
require_once __DIR__ . '/lib/rssbridge.php';

Configuration::verifyInstallation();
Configuration::loadConfiguration();

Authentication::showPromptIfNeeded();

/*
Move the CLI arguments to the $_GET array, in order to be able to use
rss-bridge from the command line
*/
if (isset($argv)) {
	parse_str(implode('&', array_slice($argv, 1)), $cliArgs);
	$params = array_merge($_GET, $cliArgs);
} else {
	$params = $_GET;
}

define('USER_AGENT',
	'Mozilla/5.0 (compatible; archive.org_bot +http://www.archive.org/details/archive.org_bot)'
);

ini_set('user_agent', USER_AGENT);

try {

	$actionFac = new \ActionFactory();
	$actionFac->setWorkingDir(PATH_LIB_ACTIONS);

	if(array_key_exists('action', $params)) {
		$action = $actionFac->create($params['action']);
		$action->setUserData($params);
		$action->execute();
	} else {
		$showInactive = filter_input(INPUT_GET, 'show_inactive', FILTER_VALIDATE_BOOLEAN);
		echo BridgeList::create($showInactive);
	}
} catch(\Exception $e) {
	error_log($e);
	header('Content-Type: text/plain', true, $e->getCode());
	die($e->getMessage());
}
