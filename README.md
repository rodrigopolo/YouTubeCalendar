# YouTube Calendar

A calendar view of a YouTube channel's video archive, powered by the YouTube Data API v3 and a local MySQL database.

## Requirements

* PHP 8.0+
* MySQL 5.7+ (or MariaDB 10.3+)
* A [YouTube Data API v3](https://console.cloud.google.com/) key

## Installation

1. **Clone the repo**
   ```
   git clone https://github.com/RodrigoPolo/YouTubeCalendar.git
   cd YouTubeCalendar
   ```

2. **Configure the app**
   Copy the template and fill in your values:
   ```
   cp app/config-test.php app/config.php
   ```
   Edit `app/config.php` and set `DB_HOST`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`, `YOUTUBE_KEY`, `YOUTUBE_USER`, and `START_DATE`.

3. **Create the database**
   ```
   mysql -h host -u user -pPass dbname < db.sql
   ```

4. **Set up the cron job**
   Run the sync script periodically to pull new videos from YouTube:
   ```
   0 */6 * * * /usr/bin/php /path/to/cron.php >> /var/log/ytcalendar.log 2>&1
   ```

## Usage

* Open `index.php` in a browser to view the calendar.
* `cron.php` fetches new uploads and stores them in the database. It stops paging as soon as it encounters a video already in the database, so incremental syncs are fast.

---

### Donations
[PayPal](https://paypal.me/rodrigopolo)
