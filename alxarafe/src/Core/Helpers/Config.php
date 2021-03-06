<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */
namespace Alxarafe\Helpers;

use Alxarafe\Controllers\EditConfig;
use Alxarafe\Database\Engine;
use Alxarafe\Database\SqlHelper;
use Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * All variables and global functions are centralized through the static class Config.
 * The Config class can be instantiated and passed to the class that needs it,
 * sharing the data and methods that are in them.
 *
 * @package Alxarafe\Helpers
 */
class Config
{

    /**
     * Contains an array with the variables defined in the configuration file.
     * Use setVar to assign or getVar to access the variables of the array.
     *
     * @var array
     */
    static private $global;

    /**
     * Contains error messages.
     *
     * @var array
     */
    static private $errors;

    /**
     * Contains the instance to the database engine (or null)
     *
     * @var Engine
     */
    static $dbEngine;

    /**
     * Contains the instance to the specific SQL engine helper (or null)
     *
     * @var sqlHelper
     */
    static $sqlHelper;

    /**
     * Contains the database structure data.
     * Each table is a index of the associative array.
     *
     * @var array
     */
    static $bbddStructure;

    /**
     * It is a static instance of the Auth class that contains the data of the
     * currently identified user.
     *
     * @var Auth
     */
    static $user;

    /**
     * Contains the user's name or null
     *
     * @var string|null
     */
    static $username;

    /**
     * Contains the full name of the configuration file or null
     *
     * @var string::null
     */
    static $configFilename;

    /**
     * Returns the name of the configuration file. By default, create the config
     * folder and enter the config.yaml file inside it.
     * If you want to use another folder for the configuration, you will have to
     * define it in the constant CONFIGURATION_PATH before invoking this method,
     * this folder must exist.
     *
     * @return string|null
     */
    public static function getConfigFileName()// : ?string - NetBeans only supports up to php7.0, for this you need php7.1
    {
        if (isset(self::$configFilename)) {
            return self::$configFilename;
        }
        $filename = CONFIGURATION_PATH . '/config.yaml';
        if (file_exists($filename) || is_dir(CONFIGURATION_PATH) || mkdir(CONFIGURATION_PATH, 0777, true)) {
            self::$configFilename = $filename;
        }
        return self::$configFilename;
    }

    /**
     * Return true y the config file exists
     *
     * @return bool
     */
    public static function configFileExists(): bool
    {
        return (file_exists(self::getConfigFileName()));
    }

    /**
     * Returns an array with the configuration defined in the configuration file.
     * If the configuration file does not exist, take us to the application
     * configuration form to create it
     *
     * @return array
     */
    public static function loadConfigurationFile(): array
    {
        $filename = self::getConfigFileName();
        if (isset($filename)) {
            /*
              // TODO: Duplicate? It is done in Dispatcher->getConfiguration()
              if (!self::configFileExists()) {
              (new EditConfig())->run();
              }
             */
            $yaml = file_get_contents($filename);
            if ($yaml) {
                return YAML::parse($yaml);
            }
        }
        return null;
    }

    /**
     * Set the display settings.
     *
     * @return void
     */
    public static function loadViewsConfig()
    {
        Skin::setTemplatesEngine(self::getVar('templaterender', 'main', 'engine') ?? 'twig');
        Skin::setSkin(self::getVar('templaterender', 'main', 'skin') ?? 'default');
        Skin::setTemplate(self::getVar('templaterender', 'main', 'skin') ?? 'default');
        Skin::setCommonTemplatesFolder(self::getVar('templaterender', 'main', 'commonTemplatesFolder') ?? Skin::COMMON_FOLDER);
    }

    /**
     * Initializes the global variable with the configuration, connects with
     * the database and authenticates the user.
     *
     * @return void
     */
    public static function loadConfig()
    {
        self::$global = self::loadConfigurationFile();
        if (isset(self::$global['templaterender']['main']['skin'])) {
            $templatesFolder = BASE_PATH . Skin::SKINS_FOLDER;
            $skinFolder = $templatesFolder . '/' . self::$global['templaterender']['main']['skin'];
            if (is_dir($templatesFolder) && !is_dir($skinFolder)) {
                Config::setError("Skin folder '$skinFolder' does not exists!");
                //(new EditConfig())->run();
                new EditConfig();
                return;
            }
            Skin::setSkin(self::getVar('templaterender', 'main', 'skin'));
        }
        if (!self::connectToDataBase()) {
            self::setError('Database Connection error...');
            //(new EditConfig())->run();
            new EditConfig();
            return;
        }
        if (self::$user === null) {
            self::$user = new Auth();
            self::$username = self::$user->getUser();
            if (self::$username == null) {
                self::$user->login();
            }
        }
    }

    /**
     * Stores all the variables in a permanent file so that they can be loaded
     * later with loadConfigFile()
     * Returns true if there is no error when saving the file.
     *
     * @return bool
     */
    public static function saveConfigFile(): bool
    {
        $configFile = self::getConfigFileName();
        if (!isset($configFile)) {
            return false;
        }

        return file_put_contents($configFile, YAML::dump(self::$global, 3)) !== FALSE;
    }

    /**
     * Register a new error message
     *
     * @param string $error
     */
    public static function setError(string $error)
    {
        self::$errors[] = $error;
    }

    /**
     * Returns an array with the pending error messages, and empties the list.
     *
     * @return array
     */
    public static function getErrors()
    {
        $errors = self::$errors;
        self::$errors = [];
        return $errors;
    }

    /**
     * Stores a variable.
     *
     * @param string $name
     * @param string $value
     */
    public static function setVar(string $module, string $section, string $name, string $value)
    {
        self::$global[$module][$section][$name] = $value;
    }

    /**
     * Gets the contents of a variable. If the variable does not exist, return null.
     *
     * @param string $module
     * @param string $section
     * @param string $name
     *
     * @return string|null ?string
     */
    public static function getVar(string $module, string $section, string $name): ?string
    {
        return self::$global[$module][$section][$name] ?? null;
    }

    /**
     * If Config::$dbEngine contain null, create an Engine instance with the
     * database connection and assigns it to Config::$dbEngine.
     *
     * @return bool
     *
     * @throws \DebugBar\DebugBarException
     */
    public static function connectToDatabase($db='main'): bool
    {
        if (self::$dbEngine == null) {
            $dbEngineName = self::$global['database'][$db]['dbEngineName'] ?? 'PdoMySql';
            $helperName = 'Sql' . substr($dbEngineName, 3);

            Debug::addMessage('SQL', "Using '$dbEngineName' engine.");
            Debug::addMessage('SQL', "Using '$helperName' SQL helper engine.");

            $sqlEngine = '\\Alxarafe\\Database\\SqlHelpers\\' . $helperName;
            $engine = '\\Alxarafe\\Database\\Engines\\' . $dbEngineName;
            try {
                Config::$sqlHelper = new $sqlEngine();
                Config::$dbEngine = new $engine([
                    'dbUser' => self::$global['database'][$db]['dbUser'],
                    'dbPass' => self::$global['database'][$db]['dbPass'],
                    'dbName' => self::$global['database'][$db]['dbName'],
                    'dbHost' => self::$global['database'][$db]['dbHost'],
                    'dbPort' => self::$global['database'][$db]['dbPort'],
                ]);
                return isset(self::$dbEngine) && self::$dbEngine->connect() && Config::$dbEngine->checkConnection();
            } catch (Exception $e) {
                Debug::addException($e);
                return false;
            }
        }
    }
}
