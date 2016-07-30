# Pokemon GO - PTC account creator

###Features:

 * Create as many PTC accounts as you need
 * Activates each created account for you (Opens the activation link)
 * Outputs the created accounts into a simple CSV file  

###Requirements:
* PHP CLI

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