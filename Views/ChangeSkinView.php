<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */

namespace ThinCore\Views;

use Alxarafe\Base\View;
use Alxarafe\Helpers\Skin;
use Alxarafe\Helpers\Config;
use Alxarafe\Database\Engine;

/**
 * Class
 *
 * @package Alxarafe\Views
 */
class ChangeSkinView extends View
{

    public $skins;
    public $skin;

    /**
     * Login constructor.
     *
     * @param $ctrl
     *
     * @throws \DebugBar\DebugBarException
     */
    public function __construct($ctrl)
    {
        Skin::setTemplate('changeskin');

        parent::__construct($ctrl);

        $vars = Config::configFileExists() ? Config::loadConfigurationFile() : [];

        $this->skins = Skin::getSkins();
        $this->skin = $vars['templaterender']['main']['skin'] ?? $this->skins[0] ?? '';
    }

}
