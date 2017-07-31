# Cave's Man
PHP modular framework


## Features

- [x] Use multiple templates at same time
- [x] Administration Panel
- [x] CMS
- [x] URL Friendly
- [x] Modular
- [ ] Module Hook in templates
- [ ] GeoIP

## Requeriments

- Web Server
- PHP <= 5.6
- MYSQL <= 5.5
- PHP Imagick

## How to Install

1. Copy this repo to your host

```bash
git clone --recursive https://github.com/soanix/cavesman.git .
```

2. Create a new database
3. Import install.sql in your database
4. Edit trunk/config/setup.inc.php with correct data

```php
define("DB_HOST", "#edit#"); 		// Datebase Host
define("DB_USER", "#edit#"); 		// Database User
define("DB_PASSWORD", "#edit#"); 	// DataBase Password
define("DB_NAME", "#edit#"); 		// Datebase Name
```
5. Test your setup
