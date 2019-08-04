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
     * Main is invoked if method is not specified.
     * Check if you have to save changes or just exit
     *
     * @return void
     */
    public function main()
    {
        if (isset($_POST['cancel'])) {
            header('Location: ' . BASE_URI);
        }

        if (isset($_POST['submit'])) {
            $this->save();
            header('Location: ' . constant('BASE_URI') . '/index.php?' . constant('CALL_CONTROLLER') . '=ChangeSkin');
        }
    }

    /**
     * Save the form changes in the configuration file
     *
     * @return void
     */
    private function save()
    {
        Config::setVar('templaterender', 'main', 'skin', $_POST['skin'] ?? '');
        Config::saveConfigFile();
    }

}
