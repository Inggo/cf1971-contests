<?php

namespace Inggo\CF1971\Contests;

class Admin
{
    private $settings = [
        'show_form' => 'checkbox',
        'show_leaderboards' => 'checkbox',
        'paypal_email' => 'email',
    ];

    private $labels = [
        'show_form' => 'Show Registration Form?',
        'show_leaderboards' => 'Show Leaderboards?',
        'paypal_email' => 'PayPal Email Address',
    ];

    public function __construct()
    {
        \add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
        \add_action('save_post', [$this, 'saveMetaData'], 10, 3);
        \add_action('admin_print_scripts-post-new.php', [$this, 'enqueueScripts']);
        \add_action('admin_print_scripts-post.php', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts($hook)
    {
        \wp_register_script('cf1971-contests-admin', CF1971_CONTESTS_URL . 'js/admin.js', ['jquery', 'jquery-ui-sortable'], CF1971_CONTEST_VERSION, true);
        \wp_register_style('cf1971-contests-admin', CF1971_CONTESTS_URL . 'css/admin.css', [], CF1971_CONTEST_VERSION);

        global $post_type;

        if ($post_type === 'cf1971_contests' || $post_type === 'cf1971_contestants') {
            \wp_enqueue_script('cf1971-contests-admin');
            \wp_enqueue_style('cf1971-contests-admin');
        }
    }

    public function addMetaBoxes()
    {
        \add_meta_box('cf1971-contests-settings-meta', 'Contest Settings', [$this, 'settingsMetaBox'], 'cf1971_contests');
        \add_meta_box('cf1971-contests-workouts-meta', 'Workouts', [$this, 'workoutsMetaBox'], 'cf1971_contests');
        \add_meta_box('cf1971-contests-leaderboards-meta', 'Leaderboards', [$this, 'leaderboardsMetaBox'], 'cf1971_contests');
    }

    public function settingsMetaBox($object)
    {
        \wp_nonce_field('cf1971_contests_settings_meta', 'cf1971_contests_settings_meta_nonce');
        ?>

        <div class="cf1971-admin-settings">
            <?= $this->getSettingView('show_form', $object); ?>
            <?= $this->getSettingView('show_leaderboards', $object); ?>
            <?= $this->getSettingView('paypal_email', $object); ?>
        </div>

        <?php
    }

    private function getSettingView($setting, $object)
    {
        $this->settingsCommonPre($setting);

        switch ($this->settings[$setting]) {
            case 'checkbox':
            ?>
                <input type="checkbox" <?= $this->settingsCommonAttr($setting); ?> value="1" <?=
                    \get_post_meta($object->ID, 'cf1971_contests.' . $setting, true) ? 'checked' : '';
                ?>>
            <?php
                break;
            case 'email':
                ?>
                <input type="email" <?= $this->settingsCommonAttr($setting); ?> value="<?=
                    \esc_attr(\get_post_meta($object->ID, 'cf1971_contests.' . $setting, true));
                ?>">
                <?php
                break;
            default:
            ?>
                <input type="text" <?= $this->settingsCommonAttr($setting); ?> value="<?=
                    \esc_attr(\get_post_meta($object->ID, 'cf1971_contests.' . $setting, true));
                ?>">
            <?php
        }

        $this->settingsCommonPost();
    }

    private function settingsCommonPre($setting)
    {
        \wp_nonce_field('cf1971_contests_settings_meta', 'cf1971_contests_settings_meta_nonce');
        ?>
        <div class="cf1971-admin-settings-field cf1971-admin-settings-<?= $setting; ?>">
            <label class="cf1971-admin-settings-label" for="cf1971-settings-<?= $setting; ?>"><?=
                $this->labels[$setting];
            ?></label>
            <div class="cf1971-admin-settings-control">
        <?php
    }

    private function settingsCommonPost()
    {
        ?>
            </div>
        </div>
        <?php
    }

    private function settingsCommonAttr($setting)
    {
        return ' id="cf1971-settings-' . \esc_attr($setting) . '" name="' . \esc_attr($setting) . '" ';
    }

    public function workoutsMetaBox($object)
    {
        \wp_nonce_field('cf1971_contests_workouts_meta', 'cf1971_contests_workouts_meta_nonce');
        ?>

        <div class="cf1971-admin-workouts">
            <p>Publish or Update this Contest before editing the leaderboards below.</p>
            <ul class="cf1971-workouts-list">
                
            </ul>
            <div class="cf1971-workouts-create">
                <input type="text" name="cf1971-workout-new" placeholder="Workout Name">
                <button class="cf1971-workout-add" type="button">Add Workout</button>
            </div>
        </div>

        <?php
    }

    public function leaderboardsMetaBox($object)
    {
        \wp_nonce_field('cf1971_contests_leaderboards_meta', 'cf1971_contests_leaderboards_meta_nonce');
        ?>

        <div class="cf1971-admin-leaderboards">
            <p>Make sure the Workouts above are populated and finalised.</p>
            <div class="cf1971-team-create">
                <input type="text" name="cf1971-workout-new" placeholder="Team Name">
                <button class="cf1971-workout-add" type="button">Add Team</button>
            </div>
        </div>

        <?php
    }

    public function saveMetaData($post_id, $post, $update)
    {
        $this->saveSettingsData($post_id, $post, $update);
        $this->saveWorkoutsData($post_id, $post, $update);
        $this->saveLeaderboardsData($post_id, $post, $update);
    }

    private function saveSettingsData($post_id, $post, $update)
    {
        if (!$this->savePreChecks($post_id, $post, 'settings')) {
            return $post_id;
        }

        $show_form = isset($_POST['show_form']) && intval($_POST['show_form']) === 1;
        $show_leaderboards = isset($_POST['show_leaderboards']) && intval($_POST['show_leaderboards']) === 1;
        $paypal_email = isset($_POST['paypal_email']) ? \sanitize_email($_POST['paypal_email']) : '';

        \update_post_meta($post_id, 'cf1971_contests.show_form', $show_form);
        \update_post_meta($post_id, 'cf1971_contests.show_leaderboards', $show_leaderboards);
        \update_post_meta($post_id, 'cf1971_contests.paypal_email', $paypal_email);
    }

    private function saveWorkoutsData($post_id, $post, $update)
    {
        if (!$this->savePreChecks($post_id, $post, 'workouts')) {
            return $post_id;
        }
    }

    private function saveLeaderboardsData($post_id, $post, $update)
    {
        if (!$this->savePreChecks($post_id, $post, 'leaderboards')) {
            return $post_id;
        }
    }

    private function savePreChecks($post_id, $post, $box)
    {

        if (!isset($_POST['cf1971_contests_' . $box . '_meta_nonce']) ||
                !\wp_verify_nonce($_POST['cf1971_contests_' . $box . '_meta_nonce'], 'cf1971_contests_' . $box . '_meta')) {
            return false;
        }

        $post_type = \get_post_type_object($post->post_type);

        if (!\current_user_can($post_type->cap->edit_post, $post_id)) {
            return false;
        }

        return true;
    }
}
