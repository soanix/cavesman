![Alt text](cavesman.jpg?raw=true "Title")

# Cave's Man

Framework modular para PHP

Versión: Alpha 0.2

## Features

- [x] Multi-template
- [x] Modular
- [x] Hooks en Modulos
- [x] Soporte multi-idioma

## How to Install

1. Clonar repositorio de cavesman en el directorio elegido

```bash
git clone --recursive https://github.com/soanix/cavesman.git .
```

2. Script de instalación en proceso


## DOCUMENTATION

**HOW TO CREATE MODULE**

1. Create a folder in /modules/yourmodule
2. Create php /modules/yourmodule/yourmodule.php
3. Start your file with:

```php

<?php
class test extends modules{
    function __construct(){
        self::install();
        parent::loadSmarty();
    }

	/**
	 * Used to do before use module like create directories or db tables
	 */

    private function install(){
        /* Something to do on module load */
    }

	/**
	 *
	 * Route url method to call function
	 *
	 * Like /test/foo
	 *
	 * @return  array
	 *
	 */

    public function actionFoo(){
     	return "Hello World!";
    }

	/**
	 *
	 * Static method to use in framework
	 *
	 * Like Test::HellowWorld();
	 *
	 * @return  array
	 *
	 */
	public static function HelloWorld(){
		return "Hello World!";
	}

    /*
    * HOOK HEADER
    */
    public function hookTest(){
        return $this->smarty->fetch(dirname(__FILE__)."/tpl/header.tpl");
    }

}
?>
```

5. Now you can test your module
```php
  echo $this->modules->test->HelloWorld();
  //returns "Hello World!"
```

**HOW TO CREATE TEMPLATE**

1. Create folder app/themes/yourtheme
2. Create file app/themes/yourtheme/index.php
3. Create file and folder app/themes/yourtheme/**tpl**/filename.tpl
4. Add this line to app/themes/yourtheme/index.php:

```php
<?php
$this->smarty->display("filename.tpl");
?>

```
5. Change default theme in app/config/settings.inc.php

```php
define("DEFAULT_THEME", "yourtheme");
```
5. Now you can add html content to filename.tpl
