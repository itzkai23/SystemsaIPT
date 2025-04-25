<?php
// includes/evaluation_schedule.php

function getEvaluationScheduleStatus($user_section) {
    // Fixed Schedule
    $fixed_schedule = [
        ['BSIT-3A', 'BSIT-3B'],
        ['BSIT-3C', 'BSIT-3D'],
        ['BSIT-3E', 'BSIT-3F'],
        ['BSIT-3G', 'BSIT-3H'],
        ['BSIT-3I', 'BSIT-3J'],
        ['BSIT-3K'],
        [] // Sunday - No Evaluation
    ];

    $start_date = new DateTime("2025-04-25"); // Sunday
    $now = new DateTime();
    $current_hour = (int) $now->format('G');
    $days_diff = $start_date->diff($now)->days;
    $day_index = $days_diff % 7;

    // Handle rest day
    if ($day_index === 6) {
        return [
            'allowed' => false,
            'message' => "⛔ Faculty evaluation is closed today. Please come back tomorrow.",
            'schedule_date' => null
        ];
    }

    $slot = ($current_hour >= 7 && $current_hour < 15) ? 0 : (($current_hour >= 15 && $current_hour < 22) ? 1 : -1);

    // Outside evaluation hours
    if ($slot === -1) {
        $schedule_date = clone $start_date;
        $schedule_date->modify("+$days_diff days");
        return [
            'allowed' => false,
            'message' => "⏰ Faculty evaluation hasn’t started yet. Please come back between 7 AM and 10 PM.",
            'schedule_date' => $schedule_date->format('F j, Y')
        ];
    }

    $today_sections = $fixed_schedule[$day_index];
    $allowed_section = count($today_sections) === 1 ? $today_sections[0] : $today_sections[$slot];

    if ($user_section === $allowed_section) {
        return ['allowed' => true];
    } else {
        $schedule_date = null;
        // Search when the user's section is allowed
        foreach ($fixed_schedule as $i => $pair) {
            foreach ($pair as $pos => $section) {
                if ($section === $user_section) {
                    $target_date = clone $start_date;
                    $target_date->modify("+$i days");
                    $schedule_date = $target_date->format('F j, Y');
                    break 2;
                }
            }
        }
        return [
            'allowed' => false,
            'message' => "⚠️ You are not scheduled to evaluate at this time.",
            'schedule_date' => $schedule_date
        ];
    }
}

function getTodayScheduledSections() {
    $fixed_schedule = [
        ['BSIT-3A', 'BSIT-3B'],
        ['BSIT-3C', 'BSIT-3D'],
        ['BSIT-3E', 'BSIT-3F'],
        ['BSIT-3G', 'BSIT-3H'],
        ['BSIT-3I', 'BSIT-3J'],
        ['BSIT-3K'],
        [] // Sunday - No Evaluation
    ];

    $start_date = new DateTime("2025-04-25"); // Sunday
    $now = new DateTime();
    $current_hour = (int) $now->format('G');
    $days_diff = $start_date->diff($now)->days;
    $day_index = $days_diff % 7;

    // Return full pair if evaluation is active hours
    if ($day_index === 6) {
        return ["Evaluation is closed today (Sunday)."];
    }

    $slot = ($current_hour >= 7 && $current_hour < 15) ? 0 : (($current_hour >= 15 && $current_hour < 22) ? 1 : -1);

    if ($slot === -1) {
        return ["Evaluation has not started yet. (Allowed between 7 AM and 10 PM)"];
    }

    $today_sections = $fixed_schedule[$day_index];
    if (empty($today_sections)) {
        return ["No sections scheduled today."];
    }

    if (count($today_sections) === 1) {
        return [$today_sections[0]];
    }

    return [$today_sections[$slot]];
}
