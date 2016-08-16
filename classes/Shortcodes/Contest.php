<?php

namespace Inggo\CF1971\Contests\Shortcodes;

use Inggo\CF1971\Contests\Traits\GetsMetaData;

class Contest
{
    use GetsMetaData;

    private $id = null;

    public $initialized = false;

    public function __construct()
    {
        \add_shortcode('cf1971_contest', [$this, 'shortcode']);
        \add_action('wp_enqueue_scripts', [$this, 'registerScriptAndStyle'], 50);
        \add_action('wp_footer', [$this, 'printScript'], 50);
    }

    public function registerScriptAndStyle()
    {
        \wp_register_script('cf1971-contests', CF1971_CONTESTS_URL . 'js/script.js', ['jquery'], CF1971_CONTEST_VERSION, true);
        \wp_register_style('cf1971-contests', CF1971_CONTESTS_URL . 'css/style.css', [], CF1971_CONTEST_VERSION);

        global $post;

        if (has_shortcode($post->post_content, 'cf1971_contest')) {
            wp_enqueue_style('cf1971-contests');
        }
    }

    public function printScript()
    {
        if (!$this->initialized) {
            return;
        }

        wp_print_scripts('cf1971-contests');
    }

    public function shortcode($atts)
    {
        // Get contest
        $contest = get_post($atts['id']);

        if (!$contest || $contest->post_type !== 'cf1971_contests') {
            return $this->showError();
        }

        $this->initialized = true;
        $this->id = $contest->ID;

        return $this->renderContest($contest);
    }

    public function showError()
    {
        ?>
        <div class="cf1971-contest-not_found">
            Unable to find the contest.
        </div>
        <?php
    }

    public function renderContest($contest)
    {
        global $post;

        $post = $contest;

        \setup_postdata($post);

        ?>
        <div class="cf1971-contest-container">
        <?php
        $this->renderContent();
        $this->renderForm();
        $this->renderLeaderboards();
        ?>
        </div>
        <?php
        \wp_reset_postdata();
    }

    public function renderContent()
    {
        ?>
        <div class="cf1971-contest-content">
            <?php the_content(); ?>
        </div>
        <?php
    }

    public function renderForm()
    {
        if (!$this->getSetting('show_form')) {
            return;
        }

        ?>
        <div class="cf1971-contest-form">
            <h2>Please sign up below to register your team!</h2>
            <form id="cf1971-contest-registration-form" method="POST" action="<?= admin_url('admin-ajax.php'); ?>">
                <input type="hidden" name="action" value="cf1971-submit-form">
                <input type="text" name="first_name" placeholder="Your First Name">
                <input type="text" name="last_name" placeholder="Your Last Name">
                <input type="email" name="email_address" placeholder="Your Email Address">
                <input type="text" name="affiliate_name" placeholder="Your Affiliate Name">
                <input type="text" name="team_name" placeholder="Your Team Name">
                <button type="submit">Sign Up</button>
                <?php if ($this->getSetting('paypal_email')): ?>
                <p>Note: You will be redirected to PayPal after completing the form below</p>
                <?php endif; ?>
            </form>
        </div>
        <?php
    }

    public function renderLeaderboards()
    {
        if (!$this->getSetting('show_leaderboards')) {
            return;
        }

        ?>
        <div class="cf1971-contest-leaderboards">
            Leaderboards Goes Here
        </div>
        <?php
    }
}
