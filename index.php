<?php
/**
 * ThinCore is part of Alxarafe
 * Copyright (C) 2019 Alxarafe <info@alxarafe.com>
 */

define('BASE_PATH', __DIR__);
define('ALXARAFE_DIR', '/alxarafe');
define('DEBUG', true);

$autoloadFile = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    die('<h1>Composer autoload not found!</h1><p>You need to run: composer install</p>');
}
require_once $autoloadFile;

(new \Alxarafe\Helpers\Dispatcher())->run();

die(DEBUG ? 'End of execution!' : '');

use Xfs\base\fs_controller;
use Xfs\base\fs_log_manager;

$autoload_file = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload_file)) {
    die('<h1>COMPOSER ERROR</h1><p>You need to run: composer install</p>');
}
require_once $autoload_file;

if ((float)substr(phpversion(), 0, 3) < 5.6) {
    /// comprobamos la versión de PHP
    die('XFS.Cloud necesita PHP 5.6 o superior, y usted tiene PHP ' . phpversion());
}

if (!file_exists('config.php')) {
    /// si no hay config.php redirigimos al instalador
    header('Location: install.php');
    die('Redireccionando al instalador...');
}

/**
 *
 */
define('XFS_FOLDER', __DIR__);

/// ampliamos el límite de ejecución de PHP a 5 minutos
@set_time_limit(300);

/// cargamos las constantes de configuración
require_once 'config.php';
require_once 'base/config2.php';
require_once 'raintpl/rain.tpl.class.php';

/**
 * Registramos la función para capturar los fatal error.
 * Información importante a la hora de depurar errores.
 */
register_shutdown_function("fatal_handler");

/// ¿Qué controlador usar?
$pagename = '';
if (filter_input(INPUT_GET, 'page')) {
    $pagename = filter_input(INPUT_GET, 'page');
} elseif (defined('XFS_HOMEPAGE')) {
    $pagename = constant('XFS_HOMEPAGE');
}

$fsc_error = false;
if (empty($pagename)) {
    $fsc = new fs_controller();
} else {
    $class_path = find_controller_class($pagename);
    try {
        /// ¿No se ha encontrado el controlador?
        if ('base/fs_controller.php' === $class_path) {
            header("HTTP/1.0 404 Not Found");
            $fsc = new fs_controller();
        } else {
            $fsc = new $class_path();
        }
    } catch (Exception $exc) {
        echo "<h1>Error fatal</h1>"
            . "<ul>"
            . "<li><b>Archivo:</b> " . $exc->getFile() . "</li>"
            . "<li><b>Línea:</b> " . $exc->getLine() . "</li>"
            . "<li><b>Código de error:</b> " . $exc->getCode() . "</li>"
            . "<li><b>Mensaje:</b> " . $exc->getMessage() . "</li>"
            . "</ul>";
        $fsc_error = true;
    }
}

/// guardamos los errores en el log
$log_manager = new fs_log_manager();
$log_manager->save();

/// redireccionamos a la página definida por el usuario
if (filter_input(INPUT_GET, 'page') === null) {
    $fsc->select_default_page();
}

if ($fsc_error) {
    die();
}

if ($fsc->template) {
    /// configuramos rain.tpl
    raintpl::configure('base_url', null);
    raintpl::configure('tpl_dir', 'view/');
    raintpl::configure('path_replace', false);

    /// ¿Se puede escribir sobre la carpeta temporal?
    if (is_writable('tmp')) {
        raintpl::configure('cache_dir', 'tmp/' . constant('XFS_TMP_NAME'));
    } else {
        echo '<p class="text-center">'
            . '<h1>No se puede escribir sobre la carpeta tmp de XFS.Cloud</h1>'
            . '<p>Consulta la <a target="_blank" href="' . constant('XFS_COMMUNITY_URL') . 'index.php?page=community_item&id=351">documentaci&oacute;n</a>.</p>'
            . '</p>';
        die('<p class="text-center"><iframe src="' . constant('XFS_COMMUNITY_URL') . 'index.php?page=community_item&id=351" width="90%" height="800"></iframe></p>');
    }

    $tpl = new RainTPL();
    $tpl->assign('fsc', $fsc);

    if (filter_input(INPUT_POST, 'user')) {
        $tpl->assign('nlogin', filter_input(INPUT_POST, 'user'));
    } elseif (filter_input(INPUT_COOKIE, 'user')) {
        $tpl->assign('nlogin', filter_input(INPUT_COOKIE, 'user'));
    } else {
        $tpl->assign('nlogin', '');
    }

    $tpl->draw($fsc->template);
}

/// guardamos los errores en el log (los producidos durante la carga del template)
$log_manager->save();

/// cerramos las conexiones
$fsc->close();
