<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */

namespace Alxarafe\Helpers;

use Alxarafe\Base\View;
use Alxarafe\Controllers\EditConfig;

/**
 * Class Dispatcher
 *
 * @package Alxarafe\Helpers
 */
class Dispatcher
{

    /**
     * Array that contains the paths to find the Controllers folder that
     * contains the controllers
     *
     * @var array
     */
    public $searchDir;

    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->getConfiguration();

        // Search controllers in BASE_PATH/Controllers and ALXARAFE_FOLDER/Controllers
        $this->searchDir['Alxarafe'] = ALXARAFE_FOLDER;
        $this->searchDir['ThinCore'] = '';
    }

    /**
     * Load the constants and the configuration file.
     * If the configuration file does not exist, it takes us to the form for its creation.
     */
    private function getConfiguration()
    {
        $this->defineConstants();
        // First set the display options to be able to show the possible warnings and errors.
        Config::loadViewsConfig();
        $configFile = Config::getConfigFileName();
        if (file_exists($configFile)) {
            Config::loadConfig();
        } else {
            Config::setError("Creating '$configFile' file...");
            (new EditConfig())->main();
            die;
        }
    }

    /**
     * Define the constants of the application
     */
    public function defineConstants()
    {
        define('APP_URI', pathinfo(filter_input(INPUT_SERVER, 'SCRIPT_NAME'), PATHINFO_DIRNAME));

        define('SERVER_NAME', filter_input(INPUT_SERVER, 'SERVER_NAME'));
        define('APP_PROTOCOL', filter_input(INPUT_SERVER, 'REQUEST_SCHEME'));
        define('SITE_URL', APP_PROTOCOL . '://' . SERVER_NAME);
        define('BASE_URI', SITE_URL . APP_URI);

        define('CALL_CONTROLLER', 'call');
        define('METHOD_CONTROLLER', 'run');
        define('DEFAULT_CALL_CONTROLLER', 'index');
        define('DEFAULT_METHOD_CONTROLLER', 'main');

        /**
         * It is recommended to define BASE_PATH as the first line of the
         * index.php file of the application.
         *
         * define('BASE_PATH', __DIR__);
         */
        Utils::defineIfNotExists('BASE_PATH', __DIR__ . '/../../../..');
        Utils::defineIfNotExists('DEBUG', false);
        Utils::defineIfNotExists('VENDOR_FOLDER', BASE_PATH . '/vendor');
        Utils::defineIfNotExists('ALXARAFE_DIR', '/vendor/alxarafe/alxarafe');
        Utils::defineIfNotExists('ALXARAFE_BASE_FOLDER', BASE_PATH . ALXARAFE_DIR);
        Utils::defineIfNotExists('ALXARAFE_BASE_URI', BASE_URI . ALXARAFE_DIR);
        Utils::defineIfNotExists('ALXARAFE_FOLDER', ALXARAFE_BASE_FOLDER . '/src/Core');
        Utils::defineIfNotExists('ALXARAFE_URI', ALXARAFE_BASE_URI . '/src/Core');

        /**
         * Must be defined in main index.php file
         */
        Utils::defineIfNotExists('VENDOR_URI', BASE_URI . '/vendor');
        Utils::defineIfNotExists('ALXARAFE_TEMPLATES_FOLDER', ALXARAFE_BASE_FOLDER . '/templates');
        Utils::defineIfNotExists('ALXARAFE_TEMPLATES_URI', ALXARAFE_BASE_URI . '/templates');
        Utils::defineIfNotExists('DEFAULT_TEMPLATES_FOLDER', BASE_PATH . '/html');
        Utils::defineIfNotExists('DEFAULT_TEMPLATES_URI', BASE_URI . '/html');

        define('CONFIGURATION_PATH', BASE_PATH . '/config');
        define('DEFAULT_STRING_LENGTH', 50);
        define('DEFAULT_INTEGER_SIZE', 10);
    }

    /**
     * Try to locate the $call class in $path, and execute the $method.
     * Returns true if it locates the class and the method exists,
     * executing it.
     *
     * @param string $key
     * @param string $call
     * @param string $method
     * @return bool
     */
    public function processFolder(string $key, string $call, string $method): bool
    {
        $className = $key . '\\Controllers\\' . $call;
        if (class_exists($className)) {
            Debug::addMessage('messages', "$className exists!");
            (new $className())->{$method}();
            return true;
        }
        return false;
    }

    /**
     * Walk the paths specified in $this->searchDir, trying to find the
     * controller and method to execute.
     * Returns true if the method is found, and executes it.
     *
     * @return bool
     */
    public function process()
    {
        foreach ($this->searchDir as $key => $dir) {
            //$path = $dir . '/Controllers';
            $call = $_GET[constant('CALL_CONTROLLER')] ?? constant('DEFAULT_CALL_CONTROLLER');
            $method = $_GET[constant('METHOD_CONTROLLER')] ?? constant('DEFAULT_METHOD_CONTROLLER');
            Debug::addMessage('messages', "Dispatcher::process() trying for '$key': {$call}->{$method}()");
            if ($this->processFolder($key, $call, $method)) {
                Debug::addMessage('messages', "Dispatcher::process(): Ok");
                return true;
            }
        }
        Debug::addMessage('messages', 'Process fault!');
        return false;
    }

    /**
     * Run the application.
     *
     * @throws \DebugBar\DebugBarException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        if (!$this->process()) {
            // Execute '404 page not found'
            if (Skin::$view == null) {
                Skin::$view = new View();
            }
        }
    }
}
