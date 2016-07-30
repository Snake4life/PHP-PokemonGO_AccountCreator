<?php
use CB\Mail\Fyii;
use CB\PTCAccount;

$options = getopt("ac:d:f:hn:p:");

//Print help message
if (isset($options["h"])) {
    $usageMessage = "Usage: php index.php [-p prefix] [-n number] [-f filename] [-c countries] [-h]" . PHP_EOL;
    $usageMessage .= "Options:" . PHP_EOL;
    $usageMessage .= "-c    : comma seperated list of possible countries ISO2 shortcode" . PHP_EOL;
    $usageMessage .= "-d    : debug level. Possible modes: debug, info, notice, warning, error, critical, alert, emergency" . PHP_EOL;
    $usageMessage .= "-f    : absolute path to the CSV output file" . PHP_EOL;
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
    define("COUNTRIES", $options["c"]);
}

if (!empty($options["f"])) {
    $filepath = trim($options["f"]);
    //http://stackoverflow.com/a/10473026 and http://thedailywtf.com/articles/mysterious-mysteries-of-strange-mystery
    if (strrpos($filepath, "/", -strlen($filepath)) !== false) {
        define("CSV_OUTPUT_FILE", $filepath);
    } else {
        die("Invalid filename - absolute path needed" . PHP_EOL);
    }
}

if (!empty($options["n"])) {
    $n = intval($options["n"]);
    if (0 >= $n) {
        die("Invalid number of accounts (must be > 0): " . $n . PHP_EOL);
    }
    define("NUMBER_OF_ACCOUNTS", $n);
}

if (isset($options["o"])) {
    define("APPEND_OUTPUT", false);
}

if (!empty($options["p"])) {
    if (strlen($options["p"]) > 7) {
        die("Account prefix must be shorter than 8 characters (PTC allows only 10 characters)" . PHP_EOL);
    }
    define("ACCOUNT_PREFIX", $options["p"]);
}


require_once("../conf.php");

/** @var PTCAccount[] $accounts */
$accounts = array();
$registration = new \CB\PTCRegister();

$countries = explode(",", COUNTRIES);
$countries = array_map(function ($value) {
    return strtoupper(trim($value));
}, $countries);

while (true) {
    //Maximum length of 10 chars allowed
    $username = PTCAccount::generateUsername(ACCOUNT_PREFIX);
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
    }

    if (count($accounts) >= NUMBER_OF_ACCOUNTS) {
        break;
    }
    sleep(rand(1, 5));
}

//Create CSV
$file = fopen(CSV_OUTPUT_FILE, "a");
if (empty(file_get_contents(CSV_OUTPUT_FILE))) {
    fputcsv($file, array("Username", "Password", "Date of birth", "Country", "E-Mail"));
}
foreach ($accounts as $account) {
    fputcsv($file, $account->toArray());
}