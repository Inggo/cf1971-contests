<?php
/*
 * Plugin Name: Contests for CrossFit 1971
 * Plugin URI: https://github.com/Inggo/cf1971-contests
 * Description: Enable contests for CrossFit 1971 website.
 * Author: Inggo Espinosa
 * Author URI: https://inggo.xyz/
 * Version: 1.0.2
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

define('CF1971_CONTESTS_DIR', plugin_dir_path(__FILE__));
define('CF1971_CONTESTS_URL', plugin_dir_url(__FILE__));
define('CF1971_CONTEST_VERSION', '1.0.2');

if (!class_exists('Inggo\CF1971\Contests\Loader')) {
    require_once(CF1971_CONTESTS_DIR . 'classes/Loader.php');
}

new Inggo\CF1971\Contests\Loader(CF1971_CONTESTS_DIR);
