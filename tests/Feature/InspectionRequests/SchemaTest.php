<?php

use Illuminate\Support\Facades\Schema;

test('all inspection-request tables exist after migration', function () {
    expect(Schema::hasTable('request_types'))->toBeTrue()
        ->and(Schema::hasTable('inspection_requests'))->toBeTrue()
        ->and(Schema::hasTable('request_replies'))->toBeTrue()
        ->and(Schema::hasTable('request_media'))->toBeTrue();
});

test('request_types columns', function () {
    expect(Schema::hasColumns('request_types', [
        'id', 'name', 'category', 'color', 'is_active', 'sort_order',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});

test('inspection_requests columns', function () {
    expect(Schema::hasColumns('inspection_requests', [
        'id', 'requester_id', 'assignee_id', 'subject_type', 'subject_id',
        'request_type_id', 'title', 'description', 'location_detail',
        'severity', 'status', 'due_date', 'resolved_at', 'verified_at',
        'closed_at', 'verified_by', 'created_at', 'updated_at', 'deleted_at',
    ]))->toBeTrue();
});

test('request_replies columns', function () {
    expect(Schema::hasColumns('request_replies', [
        'id', 'inspection_request_id', 'author_id', 'body',
        'triggers_status', 'created_at', 'updated_at',
    ]))->toBeTrue();
});

test('request_media columns', function () {
    expect(Schema::hasColumns('request_media', [
        'id', 'mediable_type', 'mediable_id', 'path', 'disk', 'mime_type',
        'media_type', 'size_bytes', 'original_name', 'uploaded_by',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});

test('expected indexes exist on inspection_requests', function () {
    $indexes = collect(Schema::getIndexes('inspection_requests'))
        ->flatMap(fn ($idx) => [implode(',', $idx['columns'])])
        ->all();

    expect($indexes)->toContain('subject_type,subject_id')
        ->and($indexes)->toContain('assignee_id,status')
        ->and($indexes)->toContain('requester_id')
        ->and($indexes)->toContain('status,due_date');
});

test('expected index exists on request_media', function () {
    $indexes = collect(Schema::getIndexes('request_media'))
        ->flatMap(fn ($idx) => [implode(',', $idx['columns'])])
        ->all();

    expect($indexes)->toContain('mediable_type,mediable_id');
});

test('expected index exists on request_replies', function () {
    $indexes = collect(Schema::getIndexes('request_replies'))
        ->flatMap(fn ($idx) => [implode(',', $idx['columns'])])
        ->all();

    expect($indexes)->toContain('inspection_request_id');
});
