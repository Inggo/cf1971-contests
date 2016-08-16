<?php

namespace Inggo\CF1971\Contests;

use Inggo\CF1971\Contests\Traits\GetsMetaData;

class FormProcessor
{
    use GetsMetaData;

    public function __construct()
    {
        add_action('wp_ajax_cf1971_submit_form', [$this, 'process']);
    }

    public function process()
    {
        if (\check_ajax_referer('wp_ajax_cf1971_submit_form', false, false)) {
            return $this->respondError();
        }

        $this->validateForm();

        if (!$this->sendEmail()) {
            return $this->respondError();
        }

        $message = 'Your registration has been submitted.';

        if ($this->getSetting('paypal_email')) {
            $message .=  'You will now be redirected to PayPal shortly.';
        }

        return $this->respond([
            'success' => $message,
        ]);
    }

    private function sendEmail()
    {
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $message  = '<p>A user has registered to the <b>' . $this->contest->post_title;
        $message .= '</b> contest. Please find their details below:</p>';
        $message .= '<p>First Name: <b>' . $this->first_name . '</b><br/>';
        $message .= '<p>Last Name: <b>' . $this->last_name . '</b><br/>';
        $message .= '<p>Email Address: <b>' . $this->email_address . '</b><br/>';
        $message .= '<p>Affiliate Name: <b>' . $this->affiliate_name . '</b><br/>';
        $message .= '<p>Team Name: <b>' . $this->team_name . '</b></p>';

        return \wp_mail(
            $this->getSetting('contact_email') ?: \get_option('admin_email'),
            '[' . \get_option('blogname') . '] Registration for Contest: ' . $this->contest->post_title,
            $message,
            $headers
        );
    }

    private function validateForm()
    {
        if (!isset($_POST['contest_id']) || !isset($_POST['first_name']) ||
                !isset($_POST['last_name']) || !isset($_POST['email_address']) ||
                !isset($_POST['affiliate_name']) || !isset($_POST['team_name'])) {
            return $this->respondError('Please fill in the required fields.');
        }

        $contest_id = intval($_POST['contest_id']);

        $this->contest = \get_post($contest_id);

        if (!$this->contest || $this->contest->post_type !== 'cf1971_contests') {
            return $this->respondError('Cannot find the competition you signed up for. Please try again.');
        }

        if (!is_email($_POST['email_address'])) {
            return $this->respondError('Please enter a valid email address.');
        }

        $this->first_name = \sanitize_text_field($_POST['first_name']);
        $this->last_name = \sanitize_text_field($_POST['last_name']);
        $this->email_address = \sanitize_email($_POST['email_address']);
        $this->affiliate_name = \sanitize_text_field($_POST['affiliate_name']);
        $this->team_name = \sanitize_text_field($_POST['team_name']);

        return true;
    }

    private function respondError($message = 'Sorry, something went wrong. Please try again.')
    {
        return $this->respond(['error' => $message], 400);
    }

    private function respond($data = [], $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        die();
    }
}
