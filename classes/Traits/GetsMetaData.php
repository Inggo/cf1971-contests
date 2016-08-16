<?php

namespace Inggo\CF1971\Contests\Traits;

trait GetsMetaData
{
    private $workouts = null;
    private $teams = null;
    private $team_scores = null;

    private $setting_values = [];

    private function getSetting($setting)
    {
        if (array_key_exists($setting, $this->setting_values)) {
            return $this->setting_values[$setting];
        }

        global $post;

        $this->setting_values[$setting] = \get_post_meta($post->ID, 'cf1971_contests.' . $setting, true);

        return $this->setting_values[$setting];
    }

    private function getWorkouts()
    {
        if (is_array($this->workouts)) {
            return $this->workouts;
        }

        global $post;

        if (!$post) {
            return [];
        }

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

        if (!$post) {
            return [];
        }

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

        if (!$post) {
            return [];
        }

        $scores_meta = \get_post_meta($post->ID, 'cf1971_contests.team_scores', true);
        $this->team_scores = $scores_meta ? json_decode($scores_meta) : [];

        return $this->team_scores;
    }
}
