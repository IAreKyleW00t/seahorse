CIS 434 - Software Engineering
------------------------------
###### Professor: Dr. Yongjian Yu; Fall 2016

This is a repository for all projects and assingments done in CIS 434.

> Topics in software engineering and performance engineering, including comparison between structured and object-oriented software development, verification and testing, software design for concurrent and real-time systems, and system re-engineering for increased performance.

##inc/config.php
The secure configuration file we use is not provided to the public, but an example is provided below. These are globally defined for ease-of-use with other pages. Any page that needs these values (typically only those in `/inc`) can call `require_once 'inc/config.php';` but most functions that need these values have their own scripts to include (ie: `sql_connection.php`, `sendmail.php`, etc.)

```php
/* Google reCAPTCHA secret */
define('RECAPTCHA_SECRET',  'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX_XXXXXXXXX');

/* MySQL Database Settings */
define('SQL_TYPE',          'mysql');
define('SQL_HOST',          'localhost');
define('SQL_PORT',          '3306');
define('SQL_DB',            'database');
define('SQL_USER',          'username');
define('SQL_PASSWD',        'c00lPassw0rd!');
```

##Credits
Please see [humans.txt](humans.txt) for all contributors.