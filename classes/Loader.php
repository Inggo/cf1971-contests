<?php

namespace Inggo\CF1971\Contests;

class Loader
{
    public function __construct($plugin_dir)
    {
        if (!class_exists('Inggo\CF1971\Contests\Installer')) {
            require_once($plugin_dir . 'classes/Installer.php');
        }

        if (!class_exists('Inggo\CF1971\Contests\Shortcodes\Contest')) {
            require_once($plugin_dir . 'classes/Shortcodes/Contest.php');
        }
    }
}
