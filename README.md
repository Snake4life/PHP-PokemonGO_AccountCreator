# Pokemon GO - PTC account creator

###Features:
 * Create as many PTC accounts as you need
 * Activates each created account for you (Opens the activation link)
 * Outputs the created accounts into a simple CSV file  
 * Generate one or multiple [PokemonGo-Map](https://github.com/AHAAAAAAA/PokemonGo-Map) credential strings
 * Simple PHP CLI script

###Requirements:
* PHP CLI

###Installation:
 1. Clone repository:
   ```
   git clone https://github.com/SchwarzwaldFalke/PHP-PokemonGO_AccountCreator.git
   ```
 2. Rename conf_example.php to conf.php
 
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

 
###Countries:
The country option expects ISO2 codes: https://en.wikipedia.org/wiki/ISO_3166-2#Current_codes

**Example usage:**
```bash
#Go to cli directory
cd PHP-PokemonGO_AccountCreator/cli
#Create 5 accounts with prefix "test". Possible countries are US,UK,DE,AT.
php index.php -p "test" -n 5 -c US,UK,DE,AT
```

**Example [PokemonGo-Map](https://github.com/AHAAAAAAA/PokemonGo-Map) credential string generation:**
 ```
 php5 cli/generatePogoMapCmd.php -f output/accounts.csv -s 3
 ```
 Output:
 ```
 USER1="-u testaccc2b8 -p testaccc2b8 -u testacc5a20 -p testacc5a20 -u testacc93b4 -p testacc93b4"
 USER2="-u testacc2422 -p testacc2422 -u testacce9d5 -p testacce9d5 -u testaccd860 -p testaccd860"
 USER3="-u testacc3dbd -p testacc3dbd -u testacc76eb -p testacc76eb -u testacc9637 -p testacc9637"
 ```