<?php

if (!is_dir(_SRC_))
    mkdir(_SRC_);
if (!is_dir(\Cavesman\Fs::APP_DIR))
    mkdir(\Cavesman\Fs::APP_DIR);
if (!is_dir(_WEB_))
    mkdir(_WEB_);
if (!is_dir(_SRC_ . "/Views/public"))
    mkdir(_SRC_ . "/Views/public", 0777, true);
if (!is_dir(\Cavesman\Fs::APP_DIR . "/cache"))
    mkdir(\Cavesman\Fs::APP_DIR . "/cache");
if (!is_dir(\Cavesman\Fs::APP_DIR . "/config"))
    mkdir(\Cavesman\Fs::APP_DIR . "/config");
if (!is_dir(_SRC_ . "/Modules"))
    mkdir(_SRC_ . "/Modules");
if (!is_dir(_SRC_ . "/Controller"))
    mkdir(_SRC_ . "/Controller");
if (!is_dir(_SRC_ . "/Routes"))
    mkdir(_SRC_ . "/Routes");
if (!is_dir(_SRC_ . "/Test"))
    mkdir(_SRC_ . "/Test");
if (!file_exists(_WEB_ . "/.htaccess")) {
    $fp = fopen(_WEB_ . "/.htaccess", "w+");
    $htaccess = 'RewriteEngine On' . PHP_EOL
        . 'RewriteCond %{REQUEST_FILENAME} !-f' . PHP_EOL
        . 'RewriteCond %{REQUEST_FILENAME} !-d' . PHP_EOL
        . 'RewriteRule . index.php [L]';
    fwrite($fp, $htaccess);
    fclose($fp);
}
if (!file_exists(_SRC_ . "/Routes/Base.php")) {
    $fp = fopen(_SRC_ . "/Routes/Base.php", "w+");
    $htaccess = '<?php' . PHP_EOL;
    $htaccess = '\Launcher\Router::get(\'/\', fn() => new JsonResponse([\'message\' => \'Welcome to Launcher Framework!\']));';
    fwrite($fp, $htaccess);
    fclose($fp);
}
if (!file_exists(\Cavesman\Fs::APP_DIR . "/config/main.json")) {
    $fp = fopen(\Cavesman\Fs::APP_DIR . "/config/main.json", "w+");
    fwrite($fp, json_encode(["env" => "dev"], JSON_PRETTY_PRINT));
    fclose($fp);
} else {
    \Cavesman\Config::get("main.env", "dev");
}

if (!file_exists(\Cavesman\Fs::APP_DIR . "/config/params.json")) {
    $fp = fopen(\Cavesman\Fs::APP_DIR . "/config/params.json", "w+");
    fwrite($fp, json_encode(["debug" => true, "theme" => "public"], JSON_PRETTY_PRINT));
    fclose($fp);
}

if (!file_exists(_THEMES_ . "/public/index.php")) {
    $fp = fopen(_THEMES_ . "/public/index.php", "w+");
    $indexphp = '<?php' . PHP_EOL
        . 'Launcher\Router::run();';
    fwrite($fp, $indexphp);
    fclose($fp);
}

if (!file_exists(_WEB_ . "/index.php")) {
    $fp = fopen(_WEB_ . "/index.php", "w+");
    $routesphp = '<?php' . PHP_EOL
        . 'require __DIR__ . \'/../vendor/autoload.php\';' . PHP_EOL
        . 'Launcher\Launcher::run();' . PHP_EOL
        . '?>';
    fwrite($fp, $routesphp);
    fclose($fp);
}
if (!is_dir(_THEMES_))
    throw new Exception("Imposible crear el directorio de temas", 1);
if (!is_dir(\Cavesman\Display::VIEWS_DIR . "/public"))
    throw new Exception("Imposible crear el directorio de temas default", 1);
if (!is_dir(\Cavesman\Display::VIEWS_DIR . "/public/tpl"))
    throw new Exception("Imposible crear el directorio de temas tpl", 1);
if (!is_dir(\Cavesman\Config::APP_DIR . "/cache"))
    throw new Exception("Imposible crear el directorio cache", 1);
if (!is_dir(\Cavesman\Fs::APP_DIR . "/config"))
    throw new Exception("Imposible crear el directorio de configuracion", 1);
if (!is_dir(_SRC_ . "/Modules"))
    throw new Exception("Imposible crear el directorio de modulos", 1);

if (PHP_SAPI == 'cli') {
    echo "INSTALACIÓN FINALIZADA" . PHP_EOL;
    echo "Ejecuta:  php -s localhost:80 -t public";
} else {
    ?>
    <h1>Instalación correcta</h1>
    <p>Todos los archivos y directorios creados</p>
    <a href="/">Continuar</a>
    <?php
}
exit();
