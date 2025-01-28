<?php

use Cavesman\Config;
use Cavesman\Console;
use Cavesman\FileSystem;
use Cavesman\Launcher;

if (!is_dir(FileSystem::srcDir()))
    mkdir(FileSystem::srcDir());
if (!is_dir(FileSystem::appDir()))
    mkdir(FileSystem::appDir());
if (!is_dir(FileSystem::publicDir()))
    mkdir(FileSystem::publicDir());
if (!is_dir(FileSystem::viewsDir() . "/public"))
    mkdir(FileSystem::viewsDir() . "/public", 0777, true);
if (!is_dir(FileSystem::appDir() . "/config"))
    mkdir(FileSystem::appDir() . "/config");
if (!is_dir(FileSystem::srcDir() . "/Controller"))
    mkdir(FileSystem::srcDir() . "/Controller");
if (!is_dir(FileSystem::srcDir() . "/Routes"))
    mkdir(FileSystem::srcDir() . "/Routes");
if (!is_dir(FileSystem::srcDir() . "/Test"))
    mkdir(FileSystem::srcDir() . "/Test");
if (!file_exists(FileSystem::publicDir() . "/.htaccess")) {
    $fp = fopen(FileSystem::publicDir() . "/.htaccess", "w+");
    $htaccess = 'RewriteEngine On' . PHP_EOL
        . 'RewriteCond %{REQUEST_FILENAME} !-f' . PHP_EOL
        . 'RewriteCond %{REQUEST_FILENAME} !-d' . PHP_EOL
        . 'RewriteRule . index.php [L]';
    fwrite($fp, $htaccess);
    fclose($fp);
}
if (!file_exists(FileSystem::srcDir() . "/Routes/Base.php")) {
    $fp = fopen(FileSystem::srcDir() . "/Routes/Base.php", "w+");
    $htaccess = '<?php' . PHP_EOL;
    $htaccess .= 'Cavesman\\Router::get(\'/\', fn() => new Cavesman\Http\JsonResponse([\'message\' => \'Welcome to Cavesman Framework!\']));';
    fwrite($fp, $htaccess);
    fclose($fp);
}
if (!file_exists(FileSystem::appDir() . "/config/main.json")) {
    $fp = fopen(FileSystem::appDir() . "/config/main.json", "w+");
    fwrite($fp, json_encode(["env" => "dev"], JSON_PRETTY_PRINT));
    fclose($fp);
} else {
    Config::get("main.env", "dev");
}

if (!file_exists(FileSystem::appDir() . "/config/params.json")) {
    $fp = fopen(FileSystem::appDir() . "/config/params.json", "w+");
    fwrite($fp, json_encode(["debug" => true, "theme" => "public"], JSON_PRETTY_PRINT));
    fclose($fp);
}

if (!file_exists(FileSystem::viewsDir() . "/public/index.php")) {
    $fp = fopen(FileSystem::viewsDir() . "/public/index.php", "w+");
    $indexphp = '<?php' . PHP_EOL
        . 'Cavesman\Router::run();';
    fwrite($fp, $indexphp);
    fclose($fp);
}

if (!file_exists(FileSystem::publicDir() . "/index.php")) {
    $fp = fopen(FileSystem::publicDir() . "/index.php", "w+");
    $routesphp = '<?php' . PHP_EOL
        . 'require __DIR__ . \'/../vendor/autoload.php\';' . PHP_EOL
        . 'Cavesman\Launcher::run();' . PHP_EOL
        . '?>';
    fwrite($fp, $routesphp);
    fclose($fp);
}

// Ruta al archivo composer.json
$composerFile = FileSystem::documentRoot() . '/composer.json';

// Leer el contenido del archivo composer.json
$composerContent = file_get_contents($composerFile);

// Decodificar el contenido JSON en un array
$composerData = json_decode(file_get_contents($composerFile), true);

if (!isset($composerData['autoload']['psr-4']))
    $composerData['autoload']['psr-4'] = [
        "App\\" => "src/"
    ];
if (!isset($composerData['config']['bin-dir']))
    $composerData['config']['bin-dir'] = 'bin';
// Codificar los cambios de vuelta a JSON
$newComposerContent = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Guardar el contenido actualizado en composer.json
file_put_contents($composerFile, $newComposerContent);

if (!is_dir(FileSystem::viewsDir()))
    throw new Exception("Imposible crear el directorio de temas", 1);
if (!is_dir(FileSystem::viewsDir() . "/public"))
    throw new Exception("Imposible crear el directorio de temas default", 1);
if (!is_dir(FileSystem::appDir() . "/config"))
    throw new Exception("Imposible crear el directorio de configuracion", 1);

if (Launcher::isCli()) {
    Console::show('Install successfully', Console::SUCCESS);
    Console::show('php -S localhost:80 -t public', Console::SUCCESS);

} else {
    ?>
    <h1>Instalación correcta</h1>
    <p>Todos los archivos y directorios creados</p>
    <?php
}
exit();
