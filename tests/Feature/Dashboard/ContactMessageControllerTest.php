<?php

use App\Models\ContactMessage;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

function createContactMessage(array $overrides = []): ContactMessage
{
    return ContactMessage::create(array_merge([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'Inquiry',
        'message' => 'Hello, I have a question.',
    ], $overrides));
}

test('index page loads for authenticated user', function () {
    createContactMessage();

    $response = $this->get(route('dashboard.messages.index'));

    $response->assertOk();
});

test('show page loads and marks message as read', function () {
    $message = createContactMessage(['is_read' => false]);

    expect($message->fresh()->is_read)->toBeFalse();

    $response = $this->get(route('dashboard.messages.show', $message));

    $response->assertOk();
    expect($message->fresh()->is_read)->toBeTrue();
});

test('reply saves admin reply with timestamp', function () {
    $message = createContactMessage();

    $response = $this->post(route('dashboard.messages.reply', $message), [
        'admin_reply' => 'Thank you for reaching out. We will follow up shortly.',
    ]);

    $response->assertRedirect(route('dashboard.messages.show', $message));

    $fresh = $message->fresh();
    expect($fresh->admin_reply)->toBe('Thank you for reaching out. We will follow up shortly.')
        ->and($fresh->replied_at)->not->toBeNull();
});

test('destroy deletes message', function () {
    $message = createContactMessage();

    $response = $this->delete(route('dashboard.messages.destroy', $message));

    $response->assertRedirect(route('dashboard.messages.index'));
    $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
});
