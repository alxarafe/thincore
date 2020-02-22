<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */
namespace Alxarafe\Views;

use Alxarafe\Base\View;
use Alxarafe\Helpers\Skin;
use Alxarafe\Helpers\Config;
use Alxarafe\Database\Engine;

/**
 * Class Login
 *
 * @package Alxarafe\Views
 */
class ConfigView extends View
{

    public $dbEngines;
    public $dbEngineName;
    public $skins;
    public $skin;
    public $dbConfig;

    /**
     * Login constructor.
     *
     * @param $ctrl
     *
     * @throws \DebugBar\DebugBarException
     */
    public function __construct($ctrl)
    {
        Skin::setTemplate('config');

        parent::__construct($ctrl);

        $vars = Config::configFileExists() ? Config::loadConfigurationFile() : [];

        $this->dbEngines = Engine::getEngines();
        $this->skins = Skin::getSkins();

        $this->dbEngineName = $vars['database']['main']['dbEngineName'] ?? $this->dbEngines[0] ?? '';

        $this->skin = $vars['templaterender']['main']['skin'] ?? $this->skins[0] ?? '';

        $this->dbConfig['dbUser'] = $vars['database']['main']['dbUser'] ?? 'root';
        $this->dbConfig['dbPass'] = $vars['database']['main']['dbPass'] ?? '';
        $this->dbConfig['dbName'] = $vars['database']['main']['dbName'] ?? 'alxarafe';
        $this->dbConfig['dbHost'] = $vars['database']['main']['dbHost'] ?? 'localhost';
        $this->dbConfig['dbPrefix'] = $vars['database']['main']['dbPrefix'] ?? 'tc_';
        $this->dbConfig['dbPort'] = $vars['database']['main']['dbPort'] ?? '';
    }
}
