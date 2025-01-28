![Alt text](https://raw.githubusercontent.com/soanix/cavesman/master/cavesman.jpg "Title")

# Cave's Man

PHP framework

Version: 0.5

## Requeriments

- PHP >= 8.4

## How to Install

1. Create your project

    ```bash
    composer require soanix/cavesman
    ```
    or
    ```bash
    composer init
    ```
    Use `soanix/cavesman` as require dependency  
  

2. Execute install command from bin-dir

   ```bash
   vendor/bin/cavesman install
   ```
   or
   ```bash
   bin/cavesman install
   ```
   
  
3. Cavesman is installed

# CHANGE LOG

### 0.5 Major update

- Install is now a run command `bin/cavesman install`
- Display all command list running `bin/cavesman --help`
- Entity generator by running `bin/cavesman cavesman:doctrine:entity`
- Refactor all Classes namespace to Core
- Removed Smarty Class (deprecated in Cavesman 0.4)


### 0.4 MAJOR UPDATE

- Migrate from Doctrine 2 to Doctrine 3
- DB class now is named Db
- Smarty Class Deprecated


