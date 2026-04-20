<?php

return [
    'brand' => 'Mosul Boulevard',
    'greeting' => 'Hello :name,',
    'view_request' => 'View Request',
    'footer' => 'Sent from the Mosul Boulevard construction management system.',

    'assigned' => [
        'subject' => 'Inspection request assigned: :title',
        'line_1' => 'You have been assigned to an inspection request.',
        'line_type' => 'Type: :type',
        'line_severity' => 'Severity: :severity',
        'line_subject' => 'Subject: :subject',
        'line_due' => 'Due date: :date',
    ],

    'replied' => [
        'subject' => 'New reply on inspection request: :title',
        'line_1' => ':actor replied to an inspection request you are involved in.',
        'line_preview' => 'Reply: ":preview"',
    ],

    'transitioned' => [
        'subject' => 'Inspection request status changed: :title',
        'line_1' => ':actor moved the request from :from to :to.',
        'line_note' => 'Note: ":note"',
    ],

    'overdue' => [
        'subject' => 'Overdue inspection request: :title',
        'line_1' => 'This inspection request is past its due date.',
        'line_due' => 'Due date was: :date',
    ],

    'additional' => [
        'subject' => 'Inspection request alert: :title',
        'line_1' => 'An inspection request matches a rule configured for you.',
        'line_type' => 'Type: :type',
        'line_severity' => 'Severity: :severity',
    ],
];
