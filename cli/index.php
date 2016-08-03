<?php
use CB\Log\Logger;
use CB\Mail\Fyii;
use CB\PTCAccount;

//Use rtrim since __DIR__ returns "/" if the script is inside the root directory:
//http://php.net/manual/en/language.constants.predefined.php
require_once(rtrim(__DIR__, '/') . "/../conf.php");

$options = getopt("ac:d:f:hn:p:");

//Print help message
if (isset($options["h"])) {
    $usageMessage = "Usage: php index.php [-p prefix] [-n number] [-f filename] [-c countries] [-h]" . PHP_EOL;
    $usageMessage .= "Options:" . PHP_EOL;
    $usageMessage .= "-c    : comma seperated list of possible countries ISO2 shortcode" . PHP_EOL;
    $usageMessage .= "-d    : debug level. Possible modes: debug, info, notice, warning, error, critical, alert, emergency" . PHP_EOL;
    $usageMessage .= "-f    : Path or name of the CSV output file" . PHP_EOL;
    $usageMessage .= "-h    : list available command line options (this page)" . PHP_EOL;
    $usageMessage .= "-n    : number of accounts. Use this with care, too many accounts could cause a ban." . PHP_EOL;
    $usageMessage .= "-o    : If CSV output file already exists it gets overwritten (otherwise the accounts are appended)" . PHP_EOL;
    $usageMessage .= "-p    : account prefix (maximum 7 characters long)" . PHP_EOL;
    die($usageMessage);
}

if (!empty($options["c"])) {
    $parts = explode(",", $options["c"]);
    foreach ($parts as $part) {
        $part = trim($part);
        if (2 !== strlen($part)) {
            die("Invalid ISO2 shortcode: " . $part . PHP_EOL);
        }
    }
    Conf::$countries = $options["c"];
}

if (!empty($options["f"])) {
    if (file_exists($options["f"])) {
        Conf::$outputFile = $options["f"];
    } else {
        die("Couldn't find CSV file: " . $options["f"]);
    }
}

if (!empty($options["n"])) {
    $n = intval($options["n"]);
    if (0 >= $n) {
        die("Invalid number of accounts (must be > 0): " . $n . PHP_EOL);
    }
    Conf::$numberOfAccounts = $n;
}

if (isset($options["o"])) {
    Conf::$appendOutput = false;
}

if (!empty($options["p"])) {
    if (strlen($options["p"]) > 7) {
        die("Account prefix must be shorter than 8 characters (PTC allows only 10 characters)" . PHP_EOL);
    }
    Conf::$accountPrefix = $options["p"];
}


/** @var PTCAccount[] $accounts */
$accounts = array();
$registration = new \CB\PTCRegister();

$countries = explode(",", Conf::$countries);
$countries = array_map(function ($value) {
    return strtoupper(trim($value));
}, $countries);

$file = fopen(Conf::$outputFile, "a");
if (empty(file_get_contents(Conf::$outputFile))) {
    fputcsv($file, array("Username", "Password", "Date of birth", "Country", "E-Mail"));
}

while (true) {
    //Maximum length of 10 chars allowed
    $username = PTCAccount::generateUsername(Conf::$accountPrefix);
    if (isset($accounts[$username])) {
        //Try again
        continue;
    }
    $password = $username;

    //Use mt_rand instead of array_rand: http://php.net/manual/de/function.array-rand.php#112227

    $country = $countries[mt_rand(0, count($countries) - 1)];

    $eMail = new Fyii($username);
    $dateOfBirth = PTCAccount::generateDateOfBirth();

    $account = new PTCAccount($username, $password, $dateOfBirth, $country, $eMail);

    if ($registration->registerAccount($account)) {
        $accounts[$account->username] = $account;
        //Fill CSV
        fputcsv($file, $account->toArray());
        Logger::get()->addInfo("Created account", $account->toArray());
        Logger::get()->addNotice("Created account " . count($accounts) . " of " . Conf::$numberOfAccounts);
    } else {
        Logger::get()->addDebug("Couldn't create account", $account->toArray());
    }

    if (count($accounts) >= Conf::$numberOfAccounts) {
        break;
    }
    Logger::get()->addNotice("Sleep before creating another account");
    sleep(rand(1, 5));
}

echo "Finished account creation (" . count($accounts) . " Accounts)" . PHP_EOL;
echo "Path to CSV file: " . realpath(Conf::$outputFile) . PHP_EOL;