<?php
// Error Log for Bluefish Module
/*ini_set('error_reporting', E_ALL);
error_reporting(E_ALL| E_STRICT);
ini_set('log_errors',TRUE);
ini_set('html_errors',FALSE);
ini_set('error_log','./var/log/Bluefish_error.log.text');
ini_set('display_errors',FALSE);
*/
//Error reporting
#error_reporting(E_ALL);
#ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(0);
?>