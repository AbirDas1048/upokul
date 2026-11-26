<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LandingPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{0: string, 1: string}> */
    public static function pages(): array
    {
        return [
            'demo1 (aurora)' => ['/demo1', 'theme-aurora'],
            'demo2 (noir)' => ['/demo2', 'theme-noir'],
            'demo3 (glass)' => ['/demo3', 'theme-glass'],
        ];
    }

    #[DataProvider('pages')]
    public function test_each_landing_page_renders_every_section_and_its_theme(string $url, string $themeClass): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertStringContainsString($themeClass, $html);

        foreach (['hero', 'about', 'services', 'journeys', 'process', 'trusted', 'gallery', 'reviews', 'faq', 'contact', 'main'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html, "Missing #{$id} on {$url}");
        }
    }

    #[DataProvider('pages')]
    public function test_each_landing_page_has_seo_metadata(string $url): void
    {
        $this->get($url)
            ->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('"@type":"TravelAgency"', false);
    }

    public function test_whatsapp_links_use_digits_only_number(): void
    {
        config(['custom.whatsapp_digits' => '8801712345678']);

        $this->get('/demo1')
            ->assertOk()
            ->assertSee('https://wa.me/8801712345678', false)
            ->assertDontSee('wa.me/+', false);
    }

    public function test_welcome_page_links_to_all_three_demos(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('demo1'), false)
            ->assertSee(route('demo2'), false)
            ->assertSee(route('demo3'), false);
    }
}
