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

    'nav' => [
        'label' => 'Inspections',
    ],

    'pages' => [
        'coming_soon' => 'Coming soon — this page is being built.',

        'index' => [
            'title' => 'Inspection Requests',
        ],

        'create' => [
            'title' => 'New Inspection Request',
        ],

        'show' => [
            'title' => 'Inspection Request #:id',
        ],

        'edit' => [
            'title' => 'Edit Inspection Request',
        ],
    ],

    'list' => [
        'empty_state' => 'No inspection requests match these filters.',
        'create_via_api_link' => 'Create a new one',
        'overdue_badge' => 'Overdue',
        'replies_count' => ':count replies',
        'filters' => [
            'status' => 'Status',
            'severity' => 'Severity',
            'subject_type' => 'Subject',
            'subject_all' => 'All',
            'subject_villa' => 'Villas',
            'subject_tower_unit' => 'Tower units',
            'assigned_to_me' => 'Assigned to me',
            'overdue_only' => 'Overdue only',
            'sort_by' => 'Sort by',
            'sort_newest' => 'Newest first',
            'sort_oldest' => 'Oldest first',
            'sort_severity' => 'Severity high to low',
            'sort_due' => 'Due date soonest',
            'clear' => 'Clear filters',
        ],
        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'subject' => 'Subject',
            'type' => 'Type',
            'severity' => 'Severity',
            'status' => 'Status',
            'assignee' => 'Assignee',
            'due_date' => 'Due',
            'created' => 'Created',
        ],
        'pagination' => [
            'showing' => 'Showing :from to :to of :total',
            'previous' => 'Previous',
            'next' => 'Next',
            'page' => 'Page :current of :last',
        ],
    ],

    'detail' => [
        'back' => 'Back to Inspection Requests',
        'requester' => 'Requester',
        'assignee' => 'Assignee',
        'verified_by' => 'Verified by',
        'created' => 'Created',
        'due_date' => 'Due date',
        'no_due_date' => 'No due date',
        'location' => 'Location',
        'description' => 'Description',
        'unassigned' => 'Unassigned',
        'subject' => [
            'label' => 'Subject',
            'link_villa' => 'View villa',
            'link_tower_unit' => 'View tower unit',
        ],
        'media_placeholder' => 'Media gallery — coming in 4C (:count attachments)',
        'replies' => [
            'timeline_title' => 'Replies',
            'empty' => 'No replies yet.',
            'by' => 'by :name',
            'status_changed_to' => 'Status changed to :status',
            'media_count' => ':count media',
        ],
        'reply_form' => [
            'title' => 'Post a reply',
            'placeholder' => 'Add your reply…',
            'submit_button' => 'Post reply',
            'submitting' => 'Posting…',
            'status_change_optional' => 'Also change status (optional)',
            'status_change_none' => 'Leave unchanged',
            'success_toast' => 'Reply posted.',
        ],
        'transitions' => [
            'button' => 'Change status',
            'modal_title' => 'Change status to :status',
            'modal_description' => 'This updates the request and is visible to the requester and assignee.',
            'note_label' => 'Add a note (optional)',
            'note_placeholder' => 'e.g. Defect confirmed after site visit.',
            'confirm_button' => 'Confirm',
            'cancel_button' => 'Cancel',
            'success_toast' => 'Status updated.',
            'error_toast' => 'Unable to change status.',
            'no_options' => 'No status changes available from the current state.',
        ],
    ],

    'shared' => [
        'severity' => [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ],
        'status' => [
            'open' => 'Open',
            'in_progress' => 'In progress',
            'resolved' => 'Resolved',
            'verified' => 'Verified',
            'closed' => 'Closed',
            'reopened' => 'Reopened',
        ],
        'category' => [
            'qaqc' => 'QA/QC',
            'safety' => 'Safety',
            'materials' => 'Materials',
            'other' => 'Other',
        ],
        'errors' => [
            'forbidden' => 'You do not have permission to perform this action.',
            'not_found' => 'Inspection request not found.',
            'validation_failed' => 'Please check the form and try again.',
        ],
    ],
];
