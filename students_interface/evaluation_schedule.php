<?php
function getEvaluationScheduleStatus($user_section) {
    date_default_timezone_set('Asia/Manila'); // Ensure the timezone is correct

    $start_date = new DateTime('2025-04-29'); // Start date for evaluation
    $now = new DateTime();
    $current_hour = (int)$now->format('G');

    // Ensure evaluation cannot happen before the start date
    if ($now < $start_date) {
        return [
            'allowed' => false,
            'message' => "⚠️ The evaluation period has not started yet. Please wait until the start date.",
            'schedule_date' => $start_date->format('F j, Y')
        ];
    }

    $fixed_schedule = [
        1 => ['BSIT-3A', 'BSIT-3B'], // Monday
        2 => ['BSIT-3C', 'BSIT-3D'], // Tuesday
        3 => ['BSIT-3E', 'BSIT-3F'], // Wednesday
        4 => ['BSIT-3G', 'BSIT-3H'], // Thursday
        5 => ['BSIT-3I', 'BSIT-3J'], // Friday
        6 => ['BSIT-3K'],            // Saturday (only BSIT-3K)
        7 => []                      // Sunday
    ];

    // Calculate how many days passed since start
    $days_since_start = (int)$start_date->diff($now)->format('%a');
    $day_offset = $days_since_start % 7;

    // Get the correct day number based on evaluation week
    $start_day_num = (int)$start_date->format('N'); // 1 = Monday, 7 = Sunday
    $current_day = ($start_day_num + $day_offset - 1) % 7 + 1;

    if ($current_day === 7) {
        return [
            'allowed' => false,
            'message' => "⛔ Faculty evaluation is closed today (Sunday).",
            'schedule_date' => null
        ];
    }

    // Determine time slot for evaluation
    if ($current_hour >= 7 && $current_hour <= 14) {
        $slot = 0; // Morning session
    } elseif ($current_hour >= 15 && $current_hour <= 22) {
        $slot = 1; // Afternoon session
    } else {
        return [
            'allowed' => false,
            'message' => "⏰ Faculty evaluation is only open from 7 AM to 10 PM.",
            'schedule_date' => $now->format('F j, Y')
        ];
    }

    $today_sections = $fixed_schedule[$current_day];

    // Check if the user's section matches the allowed session
    if (isset($today_sections[$slot]) && $user_section === $today_sections[$slot]) {
        return ['allowed' => true];
    }

    // Check when their section is allowed to evaluate next
    for ($offset = 1; $offset <= 7; $offset++) {
        $future_day = ($current_day + $offset - 1) % 7 + 1;
        if (in_array($user_section, $fixed_schedule[$future_day])) {
            $future_date = (clone $now)->modify("+$offset days");
            return [
                'allowed' => false,
                'message' => "⚠️ You are not scheduled to evaluate at this time.",
                'schedule_date' => $future_date->format('l, F j, Y')
            ];
        }
    }

    return [
        'allowed' => false,
        'message' => "⚠️ Your section is not scheduled for evaluation this week.",
        'schedule_date' => null
    ];
}
?>
