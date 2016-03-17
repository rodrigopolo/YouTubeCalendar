#YouTube Calendar

To install:

* Clone the repo
* Setup the config.php file

* Install libraries
```
composer install
```

* Restore DB
```
mysql -h host -u user -pPass sb < db.sql --default-character-set=utf8
```


* Setup a cronjob
```
0 */6 * * * /usr/bin/php /path/to/cron.php >/dev/null
```



