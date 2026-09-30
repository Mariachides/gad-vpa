<?php
/**
 * GAD Accomplishments Data - PURE PHP ONLY
 */
$reports = [
    [
        'title'   => 'GAD Planning and Budgeting Workshop',
        'date'    => '2024-03-15',
        'details' => 'A comprehensive workshop aimed at aligning the department goals with the GAD mandated budget for the fiscal year 2024. Attended by 50 department heads.'
    ],
    [
        'title'   => 'International Womens Day Celebration',
        'date'    => '2024-03-08',
        'details' => 'Celebrated with a seminar on womens empowerment and a recognition ceremony for outstanding female leaders in the community.'
    ],
    [
        'title'   => 'Gender Sensitivity Training (Batch 1)',
        'date'    => '2024-02-20',
        'details' => 'Conducted training for new employees to ensure a gender-fair environment within the workplace. Covered basic GAD concepts and laws.'
    ],
    [
        'title'   => 'Community Outreach: Health and Wellness',
        'date'    => '2024-01-25',
        'details' => 'Provided free health check-ups and seminars on reproductive health for underprivileged families in the local barangay.'
    ]
];

if (!is_array($reports)) {
    $reports = [];
}
?>