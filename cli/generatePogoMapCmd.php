<?php
/**
 * @author Christoph Bessei
 * @version
 */

//Use rtrim since __DIR__ returns "/" if the script is inside the root directory:
//http://php.net/manual/en/language.constants.predefined.php
require_once(rtrim(__DIR__, '/') . "/../conf.php");

$options = getopt("f:hs:");

//Print help message
if (isset($options["h"])) {
    $usageMessage = "Usage: php generatePogoMapCmd.php -f [-s] [-h]" . PHP_EOL;
    $usageMessage .= "Options:" . PHP_EOL;
    $usageMessage .= "-f    : path to CSV file which contains account credentials" . PHP_EOL;
    $usageMessage .= "-h    : list available command line options (this page)" . PHP_EOL;
    $usageMessage .= "-s    : Split available accounts into multiple command lines (Default: 1)" . PHP_EOL;
    die($usageMessage);
}

if (!empty($options["f"])) {
    if (file_exists($options["f"])) {
        conf::$outputFile = $options["f"];
    } else if (file_exists(OUTPUT_PATH . $options["f"])) {
        conf::$outputFile = OUTPUT_PATH . $options["f"];
    } else {
        die("Couldn't find CSV file: " . $options["f"]);
    }
}


$splitInto = empty($options["s"]) || !is_numeric($options["s"]) ? 1 : intval($options["s"]);
$currentCommandString = 1;
$commandStrings = array();
for ($i = 1; $i <= $splitInto; $i++) {
    $commandStrings[$i] = "";
}
$rowIndex = 1;

if (false !== ($file = fopen(Conf::$outputFile, "r"))) {
    while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
        if (empty($row[0])) {
            continue;
        }
        //Skip first row if it's the header row
        if (1 === $rowIndex && $row[0] === "Username") {
            continue;
        }

        $account = new \CB\PTCAccount($row[0], $row[1]);
        $commandStrings[$currentCommandString] .= "-u " . $account->username . " -p " . $account->password;

        $rowIndex++;

        $currentCommandString++;
        if ($currentCommandString > $splitInto) {
            $currentCommandString = 1;
        }
    }
    fclose($file);
}
for ($i = 1; $i <= $splitInto; $i++) {
    echo 'USER' . $i . '="' . $commandStrings[$i] . '"' . PHP_EOL;
}

echo "Number of accounts: " . $rowIndex;