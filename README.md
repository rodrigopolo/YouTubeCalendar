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


####Update
I added cookies in order to mark watched videos, but the storage wasn't enough, so I switched to `localstorage`, sorry.

-------

### Donations
[PayPal](http://paypal.me/rodrigopolo)