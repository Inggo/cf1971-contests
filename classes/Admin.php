<?php

namespace Inggo\CF1971\Contests;

class Admin
{
    public function __construct($plugin_dir)
    {
        \add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
        \add_action('save_post', [$this, 'saveMetaData'], 10, 3);
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
            <input type="text" name="cf1971-workout-new"> <button class="cf1971-workout-add">Add Workout</button>
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
