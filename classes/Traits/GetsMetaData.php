<?php

namespace Inggo\CF1971\Contests\Traits;

trait GetsMetaData
{
    private $workouts = null;
    private $teams = null;
    private $team_scores = null;

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
}
