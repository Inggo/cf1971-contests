<?php

namespace Inggo\CF1971\Contests;

class Loader
{
    public function __construct($plugin_dir)
    {
        if (!class_exists('Inggo\CF1971\Contests\Shortcodes\Contest')) {
            require_once($plugin_dir . 'classes/Shortcodes/Contest.php');
        }

        \add_action('init', [$this, 'registerContestCPT']);
        \add_action('init', [$this, 'registerTeamCPT']);
    }

    public function registerContestCPT()
    {
        \register_post_type('cf1971_contests', [
            'labels' => [
                'name' => \__('Contests', 'cf1971-contests'),
                'singular_name' => \__('Contest', 'cf1971-contests'),
            ],
            'public' => false,
            'has_archive' => false,
            'capability_type' => 'page',
            'supports' => ['title', 'editor', 'thumbnail', 'revisions'],
        ]);
    }

    public function registerTeamCPT()
    {
        \register_post_type('cf1971_contestants', [
            'labels' => [
                'name' => \__('Contestants', 'cf1971-contests'),
                'singular_name' => \__('Contestant', 'cf1971-contests'),
            ],
            'public' => false,
            'has_archive' => false,
            'capability_type' => 'page',
            'supports' => ['title', 'thumbnail', 'revisions'],
        ]);
    }
}
