<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */

namespace Alxarafe\Controllers;

use Alxarafe\Base\Controller;
use Alxarafe\Helpers\Config;
use Alxarafe\Helpers\Skin;
use Alxarafe\Views\ConfigView;
use DebugBar\DebugBarException;

/**
 * Controller for editing database and skin settings
 *
 * @package Alxarafe\Controllers
 */
class EditConfig extends Controller
{

    /**
     * The constructor creates the view
     *
     * @throws \DebugBar\DebugBarException
     */
    public function __construct()
    {
        parent::__construct();

        Skin::setView(new ConfigView($this));
    }

    /**
     * Save the form changes in the configuration file
     *
     * @return void
     */
    public function doSave()
    {
        Config::setVar('templaterender', 'main', 'skin', $_POST['skin'] ?? '');
        Config::setVar('database', 'main', 'dbEngineName', $_POST['dbEngineName'] ?? '');
        Config::setVar('database', 'main', 'dbUser', $_POST['dbUser'] ?? '');
        Config::setVar('database', 'main', 'dbPass', $_POST['dbPass'] ?? '');
        Config::setVar('database', 'main', 'dbName', $_POST['dbName'] ?? '');
        Config::setVar('database', 'main', 'dbHost', $_POST['dbHost'] ?? '');
        Config::setVar('database', 'main', 'dbPort', $_POST['dbPort'] ?? '');

        Config::saveConfigFile();

        $this->doExit();
    }
}
