<?php

namespace Inggo\CF1971\Contests;

class Loader
{
    public function __construct($plugin_dir)
    {
        if (!trait_exists('Inggo\CF1971\Contests\Traits\GetsMetaData')) {
            require_once($plugin_dir . 'classes/Traits/GetsMetaData.php');
        }

        if (!class_exists('Inggo\CF1971\Contests\Admin')) {
            require_once($plugin_dir . 'classes/Admin.php');
        }

        if (!class_exists('Inggo\CF1971\Contests\Shortcodes\Contest')) {
            require_once($plugin_dir . 'classes/Shortcodes/Contest.php');
        }

        \add_action('init', [$this, 'registerContestsCPT']);

        $this->admin = new Admin;
        $this->shortcodes[] = new Shortcodes\Contest;
    }

    public function registerContestsCPT()
    {
        \register_post_type('cf1971_contests', [
            'labels' => [
                'name'               => \__('Contests', 'cf1971-contests'),
                'singular_name'      => \__('Contest', 'cf1971-contests'),
                'add_new_item'       => \__('Add New Contest', 'cf1971-contests'),
                'edit_item'          => \__('Edit Contest', 'cf1971-contests'),
                'new_item'           => \__('New Contest', 'cf1971-contests'),
                'view_item'          => \__('View Contest', 'cf1971-contests'),
                'search_items'       => \__('Search Contest', 'cf1971-contests'),
                'not_found'          => \__('No contests found', 'cf1971-contests'),
                'not_found_in_trash' => \__('No contests found in Trash', 'cf1971-contests'),
                'all_items'          => \__('All Contests', 'cf1971-contests'),
            ],
            'public' => true,
            'has_archive' => false,
            'capability_type' => 'page',
            'supports' => ['title', 'editor', 'thumbnail', 'revisions'],
            'rewrite' => [
                'slug' => 'contests',
            ],
        ]);
    }
}
