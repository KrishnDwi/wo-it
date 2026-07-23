<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminTelegramSettingsPageTest extends TestCase
{
    public function test_admin_telegram_settings_page_is_not_available()
    {
        $response = $this->get('/admin/settings/users');

        $response->assertNotFound();
    }
}
