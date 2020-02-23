<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */

namespace ThinCore\Controllers;

use Alxarafe\Base\Controller;
use Alxarafe\Helpers\Config;
use Alxarafe\Helpers\Skin;
use ThinCore\Views\ChangeSkinView;

/**
 * Class ChangeSkin
 *
 * @package ThinCore\Controllers
 */
class ChangeSkin extends Controller
{

    /**
     * The constructor creates the view
     *
     * @throws \DebugBar\DebugBarException
     */
    public function __construct()
    {
        parent::__construct();

        Skin::setView(new ChangeSkinView($this));
    }

    /**
     * Save the form changes in the configuration file
     *
     * @return void
     */
    public function doSave()
    {
        parent::doSave();

        Config::setVar('templaterender', 'main', 'skin', $_POST['skin'] ?? '');
        if (Config::saveConfigFile()) {
            header('Location: ' . constant('BASE_URI') . '/index.php?' . constant('CALL_CONTROLLER') . '=ChangeSkin');
        }
    }

}
