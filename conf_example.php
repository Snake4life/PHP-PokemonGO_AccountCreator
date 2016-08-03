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
define("DEFAULT_OUTPUT_FILE", OUTPUT_PATH . "accounts.csv");
define("LOG_LEVEL", LogLevel::DEBUG);

/** User options - Change them as you need. This settings get overwritten by cli options!!! */
class Conf
{
    public static $countries = "DE,AT";
    public static $appendOutput = true;
    public static $accountPrefix = "lAd12";
    public static $numberOfAccounts = 5;
    public static $outputFile = DEFAULT_OUTPUT_FILE;
}