<?php

namespace Tests\Feature;

use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_login_shows_the_flag_dropdown_with_the_current_language(): void
    {
        $this->get('/auth/login')
            ->assertOk()
            ->assertSee('data-dropdown', false)
            ->assertSee('fi fi-gb', false)
            ->assertSee('fi fi-fr', false)
            ->assertSee('href="' . url('language/fr') . '"', false)
            ->assertSeeInOrder(['hreflang="en"', 'aria-current="true"'], false);
    }

    public function test_switching_language_marks_french_as_current(): void
    {
        $this->get('/language/fr')->assertRedirect();

        $this->get('/auth/login')
            ->assertOk()
            ->assertSee('aria-label="Langue: Français"', false)
            ->assertSeeInOrder(['hreflang="fr"', 'aria-current="true"'], false);
    }
}
