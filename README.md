# Pokemon GO - PTC account creator

###Features:

 * Create as many PTC accounts as you need
 * Activates each created account for you (Opens the activation link)
 * Outputs the created accounts into a simple CSV file  

###Requirements:
* PHP CLI

###Installation:
 * Rename conf_example.php to conf.php

###Usage:

```
Usage: php index.php [-p prefix] [-n number] [-f filename] [-c countries] [-h]
Options:
-c    : comma seperated list of possible countries ISO2 shortcode
-d    : debug level. Possible modes: debug, info, notice, warning, error, critical, alert, emergency
-f    : absolute path to the CSV output file
-h    : list available command line options (this page)
-n    : number of accounts. Use this with care, too many accounts could cause a ban.
-o    : If CSV output file already exists it gets overwritten (otherwise the accounts are appended)
-p    : account prefix (maximum 7 characters long)
```
**Example usage:**
```bash
#Go to cli directory
cd cli
#Create 5 accounts with prefix "test". Possible countries are US,UK,DE,AT.
php index.php -p "test" -n 5 -c US,UK,DE,AT
```

###Countries:
The country option expects ISO2 codes: https://en.wikipedia.org/wiki/ISO_3166-2#Current_codes