<?php

use Cavesman\Config;
use Cavesman\Console;
use Cavesman\Fs;
use Cavesman\Launcher;

if (!is_dir(Fs::SRC_DIR))
    mkdir(Fs::SRC_DIR);
if (!is_dir(Fs::APP_DIR))
    mkdir(Fs::APP_DIR);
if (!is_dir(Fs::PUBLIC_DIR))
    mkdir(Fs::PUBLIC_DIR);
if (!is_dir(Fs::VIEWS_DIR . "/public"))
    mkdir(Fs::VIEWS_DIR . "/public", 0777, true);
if (!is_dir(Fs::APP_DIR . "/config"))
    mkdir(Fs::APP_DIR . "/config");
if (!is_dir(Fs::SRC_DIR . "/Controller"))
    mkdir(Fs::SRC_DIR . "/Controller");
if (!is_dir(Fs::SRC_DIR . "/Routes"))
    mkdir(Fs::SRC_DIR . "/Routes");
if (!is_dir(Fs::SRC_DIR . "/Test"))
    mkdir(Fs::SRC_DIR . "/Test");
if (!file_exists(Fs::PUBLIC_DIR . "/.htaccess")) {
    $fp = fopen(Fs::PUBLIC_DIR . "/.htaccess", "w+");
    $htaccess = 'RewriteEngine On' . PHP_EOL
        . 'RewriteCond %{REQUEST_FILENAME} !-f' . PHP_EOL
        . 'RewriteCond %{REQUEST_FILENAME} !-d' . PHP_EOL
        . 'RewriteRule . index.php [L]';
    fwrite($fp, $htaccess);
    fclose($fp);
}
if (!file_exists(Fs::SRC_DIR . "/Routes/Base.php")) {
    $fp = fopen(Fs::SRC_DIR . "/Routes/Base.php", "w+");
    $htaccess = '<?php' . PHP_EOL;
    $htaccess .= 'Cavesman\\Router::get(\'/\', fn() => new Cavesman\Http\JsonResponse([\'message\' => \'Welcome to Launcher Framework!\']));';
    fwrite($fp, $htaccess);
    fclose($fp);
}
if (!file_exists(Fs::APP_DIR . "/config/main.json")) {
    $fp = fopen(Fs::APP_DIR . "/config/main.json", "w+");
    fwrite($fp, json_encode(["env" => "dev"], JSON_PRETTY_PRINT));
    fclose($fp);
} else {
    Config::get("main.env", "dev");
}

if (!file_exists(Fs::APP_DIR . "/config/params.json")) {
    $fp = fopen(Fs::APP_DIR . "/config/params.json", "w+");
    fwrite($fp, json_encode(["debug" => true, "theme" => "public"], JSON_PRETTY_PRINT));
    fclose($fp);
}

if (!file_exists(Fs::VIEWS_DIR . "/public/index.php")) {
    $fp = fopen(Fs::VIEWS_DIR . "/public/index.php", "w+");
    $indexphp = '<?php' . PHP_EOL
        . 'Cavesman\Router::run();';
    fwrite($fp, $indexphp);
    fclose($fp);
}

if (!file_exists(Fs::PUBLIC_DIR . "/index.php")) {
    $fp = fopen(Fs::PUBLIC_DIR . "/index.php", "w+");
    $routesphp = '<?php' . PHP_EOL
        . 'require __DIR__ . \'/../vendor/autoload.php\';' . PHP_EOL
        . 'Cavesman\Launcher::run();' . PHP_EOL
        . '?>';
    fwrite($fp, $routesphp);
    fclose($fp);
}

// Ruta al archivo composer.json
$composerFile = Fs::getRootDir() . '/composer.json';

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

if (!is_dir(Fs::VIEWS_DIR))
    throw new Exception("Imposible crear el directorio de temas", 1);
if (!is_dir(Fs::VIEWS_DIR . "/public"))
    throw new Exception("Imposible crear el directorio de temas default", 1);
if (!is_dir(Fs::APP_DIR . "/config"))
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
