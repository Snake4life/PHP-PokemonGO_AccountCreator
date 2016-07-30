<?php
use Psr\Log\LogLevel;

/** Do not change the following constants, unless you know what you do! */

require_once("class/vendor/autoload.php");
define("ROOT_PATH", __DIR__ . "/");
define("CLASS_PATH", ROOT_PATH . "class/");
define("WEB_PATH", ROOT_PATH . "www/");
define("LOG_PATH", ROOT_PATH . "log/");
define("CLI_PATH", ROOT_PATH . "cli/");
define("OUTPUT_PATH", ROOT_PATH . "output/");
define("LOG_LEVEL", LogLevel::DEBUG);


/** User options - Change them as you need. This settings get overwritten by cli options!!! */

if (!defined("COUNTRIES")) {
    define("COUNTRIES", "DE,AT");
}

if (!defined("APPEND_OUTPUT")) {
    define("APPEND_OUTPUT", true);
}

if (!defined("CSV_OUTPUT_FILE")) {
    /** Path to CSV file which contains the created account credentials (PHP will create it if it doesn't exist) */
    define("CSV_OUTPUT_FILE", OUTPUT_PATH . "accounts.csv");
}

if (!defined("ACCOUNT_PREFIX")) {
    /** Username prefix. The maximum length of a username is 10 characters! */
    define("ACCOUNT_PREFIX", "lasw");
}

if (!defined("NUMBER_OF_ACCOUNTS")) {
    /** Number of accounts to create */
    define("NUMBER_OF_ACCOUNTS", 5);
}