<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\IssueType;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramNotificationTest extends TestCase
{
    public function test_it_sends_a_telegram_notification_when_a_work_order_is_created(): void
    {
        Http::fake([
            '*' => Http::response(['ok' => true], 200),
        ]);
        config()->set('services.telegram.bot_token', 'test-token');

        Department::firstOrCreate(['name' => 'IT']);
        IssueType::firstOrCreate(['name' => 'Hardware']);
        User::firstOrCreate(
            ['email' => 'telegram-admin@example.com'],
            [
                'name' => 'Admin Telegram',
                'password' => bcrypt('password123'),
                'phone_number' => '123456789',
                'is_wa_active' => true,
            ]
        );

        $response = $this->post('/add', [
            'department' => 'IT',
            'issue_type' => 'Hardware',
            'location' => 'Main Office',
            'description' => 'Printer tidak bisa dipakai',
        ]);

        $response->assertRedirect('/');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org')
                && str_contains($request->body(), 'chat_id')
                && str_contains($request->body(), 'New work order created');
        });
    }
}
