<?php

namespace Inggo\CF1971\Contests;

class Admin
{
    public function __construct()
    {
        \add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
        \add_action('save_post', [$this, 'saveMetaData'], 10, 3);
        \add_action('admin_print_scripts-post-new.php', [$this, 'enqueueScripts']);
        \add_action('admin_print_scripts-post.php', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts($hook)
    {
        \wp_register_script('cf1971-contests-admin', CF1971_CONTESTS_URL . 'js/admin.js', ['jquery'], CF1971_CONTEST_VERSION, true);
        \wp_register_style('cf1971-contests-admin', CF1971_CONTESTS_URL . 'css/admin.css', [], CF1971_CONTEST_VERSION, true);

        global $post_type;

        if ($post_type === 'cf1971_contests' || $post_type === 'cf1971_contestants') {
            \wp_enqueue_script('cf1971-contests-admin');
            \wp_enqueue_style('cf1971-contests-admin');
        }
    }

    public function addMetaBoxes()
    {
        \add_meta_box('cf1971-contests-meta-box', 'Workouts', [$this, 'workoutsMetaBox'], 'cf1971_contests');
    }

    public function workoutsMetaBox($object)
    {
        \wp_nonce_field('cf1971-contests-meta', 'cf1971_contests_meta_nonce');
        ?>

        <div class="cf1971-admin-workouts">
            <ul class="cf1971-workouts-list">
                
            </ul>
            <div class="cf1971-workouts-create">
                <input type="text" name="cf1971-workout-new" placeholder="Workout Name">
                <button class="cf1971-workout-add" type="button">Add Workout</button>
            </div>
        </div>

        <?php
    }

    public function saveMetaData($post_id, $post, $update)
    {
        $this->saveWorkoutsMetaData($post_id, $post, $update);
    }

    private function saveWorkoutsMetaData($post_id, $post, $update)
    {

    }
}
