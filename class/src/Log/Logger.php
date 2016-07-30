<?php
/**
 * @author Christoph Bessei
 * @version
 */

namespace CB\Log;


use Monolog\Handler\StreamHandler;

class Logger
{
    protected static $name = "Log";

    public static function get()
    {
        $logger = new \Monolog\Logger(self::$name);
        $logger->pushHandler(new StreamHandler(LOG_PATH . "main.log", LOG_LEVEL));
        return $logger;
    }

    public static function logHTML($filename, $html)
    {
        file_put_contents(LOG_PATH . $filename, $html);
    }
}