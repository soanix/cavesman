![Alt text](/trunk/cdn/img/cavesman.jpg?raw=true "Title")

# Cave's Man

PHP modular framework

Version: Alpha 0.1

## Features

- [x] Multi-Language
- [x] Translate Support
- [x] Use multiple templates at same time
- [x] Administration Panel
- [x] CMS
- [x] URL Friendly
- [x] Modular
- [ ] Module Hook in templates
- [ ] GeoIP

## Requeriments

- Web Server
- PHP >= 5.6

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

## Default data

**Super admin**

User: admin

Password: 1234


## DOCUMENTATION

**SQL**

CM use Easy MYSQLi library. You can get all documentation in https://github.com/soanix/Easy-MySQLi

**HOW TO CREATE MODULE**

1. Create a folder in /modules/yourmodule
2. Create php /modules/yourmodule/yourmodule.php
3. Start your file with:

```php

<?php
class yourmodule extends modules{
    function __construct(){
        $this->db = new db();
        $this->config = array(
            "active" => 1,
            "name" => "yourmodule",
            "directory" => dirname(__FILE__)."/tpl",
            "title" => "Your Module",
            "description" => "Module description"
        );
        self::install();
        parent::loadSmarty();
    }
    function install(){
        /* Something to do on module load */
    }
    public function foo(){
      $var = "Hello World!";
      return $var;
    }
}
?>
```

5. Now you can test your module
```php
  $this->modules->yourmodule->foo();
  //returns "Hello World!"
```

**HOW TO CREATE TEMPLATE**

1. Create folder /themes/yourtheme
2. Create file /themes/yourtheme/index.php
3. Create file and folder /themes/yourtheme/**tpl**/filename.tpl
4. Add this line to index.php:

```php
<?php
$this->smarty->display("filename.tpl");
?>

```
5. Change default theme in /config/setup.inc.php

```php
define("DEFAULT_THEME", "yourtheme");
```
5. Now you can add html content to filename.tpl
