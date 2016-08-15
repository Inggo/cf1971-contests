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

        \add_action('init', [$this, 'registerContestCPT']);
    }

    public function registerContestCPT()
    {
        \register_post_type('contests', [
            'labels' => [
                'name' => \__('Contests', 'cf1971-contests'),
                'singular_name' => \__('Contest', 'cf1971-contests'),
            ],
            'public' => true,
            'has_archive' => false,
            'capability_type' => 'page',
            'supports' => ['title', 'editor', 'thumbnail', 'revisions', 'custom-fields'],
        ]);
    }
}
