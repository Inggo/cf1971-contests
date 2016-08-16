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

    private $workouts = null;
    private $teams = null;
    private $team_scores = null;

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

        \wp_localize_script('cf1971-contests-admin', 'cf1971_admin', [
            'workouts' => $this->getWorkouts(),
        ]);

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

    private function getWorkouts()
    {
        if (is_array($this->workouts)) {
            return $this->workouts;
        }

        global $post;

        $workouts_meta = \get_post_meta($post->ID, 'cf1971_contests.workouts', true);
        $this->workouts = $workouts_meta ? json_decode($workouts_meta) : [];

        return $this->workouts;
    }

    private function getTeams()
    {
        if (is_array($this->teams)) {
            return $this->teams;
        }

        global $post;

        $team_meta = \get_post_meta($post->ID, 'cf1971_contests.teams', true);
        $this->teams = $team_meta ? json_decode($team_meta) : [];

        return $this->teams;
    }

    private function getTeamScores()
    {
        if (is_array($this->team_scores)) {
            return $this->team_scores;
        }

        global $post;

        $scores_meta = \get_post_meta($post->ID, 'cf1971_contests.team_scores', true);
        $this->team_scores = $scores_meta ? json_decode($scores_meta) : [];

        return $this->team_scores;
    }

    public function workoutsMetaBox($object)
    {
        \wp_nonce_field('cf1971_contests_workouts_meta', 'cf1971_contests_workouts_meta_nonce');
        ?>

        <div class="cf1971-admin-workouts">
            <p>Publish or Update this Contest before editing the leaderboards below.</p>
            <ul class="cf1971-workouts-list">
                <?php foreach ($this->getWorkouts() as $workout): ?>
                    <li>
                        <input type="hidden" value="<?= $workout ?>" name="workouts[]">
                        <label><?= $workout; ?></label>
                        <?php $this->deleteButton(); ?>
                    </li>
                <?php endforeach; ?>
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
            <table class="cf1971-leaderboards">
                <thead>
                    <tr>
                        <th>Team Name</th>
                        <?php foreach ($this->getWorkouts() as $workout): ?>
                        <th><?= $workout; ?></th>
                        <?php endforeach; ?>
                        <th><!-- Heading for Delete --></th>
                    </tr>
                </thead>
                <tbody class="cf1971-leaderboards-body">
                    <?php foreach ($this->getTeams() as $i => $team): ?>
                    <tr>
                        <td>
                            <input type="hidden" value="<?= $team; ?>" name="teams[]">
                            <label><?= $team; ?></label>
                        </td>
                        <?php foreach ($this->getWorkouts() as $j => $workout): ?>
                        <td>
                            <input class="cf1971-team-score" placeholder="Enter Score" type="text" name="team_scores[<?=
                                $j;
                            ?>][]" value="<?= $this->getTeamScores()[$j][$i] ?>">
                        </td>
                        <?php endforeach; ?>
                        <td>
                            <?php $this->deleteButton(); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cf1971-team-create">
                <input type="text" name="cf1971-team-new" placeholder="Team Name">
                <button class="cf1971-team-add" type="button">Add Team</button>
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

        if (!isset($_POST['workouts'])) {
            \delete_post_meta($post_id, 'cf1971_contests.workouts');
            return $post_id;
        }

        $workouts = $_POST['workouts'];

        if (!is_array($_POST['workouts'])) {
            \delete_post_meta($post_id, 'cf1971_contests.workouts');
            return $post_id;
        }

        $workouts = json_encode($workouts);

        \update_post_meta($post_id, 'cf1971_contests.workouts', $workouts);
    }

    private function saveLeaderboardsData($post_id, $post, $update)
    {
        if (!$this->savePreChecks($post_id, $post, 'leaderboards')) {
            return $post_id;
        }

        if (!isset($_POST['teams'])) {
            \delete_post_meta($post_id, 'cf1971_contests.teams');
            \delete_post_meta($post_id, 'cf1971_contests.team_scores');
            return $post_id;
        }

        $teams = $_POST['teams'];
        $team_scores = $_POST['team_scores'];

        if (!is_array($_POST['teams'])) {
            \delete_post_meta($post_id, 'cf1971_contests.teams');
            \delete_post_meta($post_id, 'cf1971_contests.team_scores');
            return $post_id;
        }

        $teams = json_encode($teams);
        $team_scores = json_encode($team_scores);

        \update_post_meta($post_id, 'cf1971_contests.teams', $teams);
        \update_post_meta($post_id, 'cf1971_contests.team_scores', $team_scores);
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

    private function deleteButton()
    {
        ?>
        <span style="float: right;">
            [<a class="cf1971-delete" href="javascript:;">&times;</a>]
        </span>
        <?php
    }
}
