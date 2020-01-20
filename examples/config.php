<?php
/**
 * config.php
 * Setting test variables
 * @author David Tapia (c) 2018 - Lleida.net
 * @version 4.0
 */

/***************************************
 * Compile with composer
 *  composer update
 *
 * Update autoloads
 *  composer dumpautoload -o
 *
 ***************************************/

// http://php.net/manual/en/sockets.examples.php
error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

define("USER", "");
define("PASS", "");
define("RECIPIENT", "+34...");

/* no cli compatibility  */
if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}
if (!defined('STDOUT')) {
    define('STDOUT', fopen('php://stdout', 'w'));
}
if (!defined('STDERR')) {
    define('STDERR', fopen('php://stderr', 'w'));
}
