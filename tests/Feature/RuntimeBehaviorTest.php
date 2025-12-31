<?php

namespace Raseldev99\FilamentMessages\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Illuminate\Contracts\View\View as ViewContract;
use Raseldev99\FilamentMessages\FilamentMessagesServiceProvider;
use Raseldev99\FilamentMessages\Livewire\Messages\Inbox;
use Raseldev99\FilamentMessages\Livewire\Messages\Messages as MessagesComponent;
use Raseldev99\FilamentMessages\Livewire\Messages\Search;
use Raseldev99\FilamentMessages\Filament\Pages\Messages;

class RuntimeBehaviorTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FilamentMessagesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('filament.path', '/admin');
    }

    public function test_livewire_components_render_views()
    {
        $classes = [Inbox::class, MessagesComponent::class, Search::class];

        foreach ($classes as $class) {
            $component = new $class();

            if (method_exists($component, 'mount')) {
                try {
                    $component->mount();
                } catch (\Throwable $e) {
                    // If mount requires auth or other app state, provide minimal replacements.
                    if (property_exists($component, 'conversations')) {
                        $component->conversations = collect();
                    }

                    if (property_exists($component, 'messages')) {
                        $component->messages = collect();
                    }
                }
            }

            $view = $component->render();

            $this->assertInstanceOf(ViewContract::class, $view, "{$class} did not return a View instance");
        }
    }

    public function test_views_render_and_contain_expected_elements()
    {
        $base = realpath(__DIR__ . '/../../resources/views');

        $inboxPath = $base . '/livewire/messages/inbox.blade.php';
        $this->assertFileExists($inboxPath);
        $inboxContents = file_get_contents($inboxPath);
        $this->assertStringContainsString('Search messages', $inboxContents);

        $pagePath = $base . '/filament/pages/messages.blade.php';
        $this->assertFileExists($pagePath);
        $pageContents = file_get_contents($pagePath);
        $this->assertStringContainsString('<livewire:fm-inbox', $pageContents);
        $this->assertStringContainsString('<livewire:fm-messages', $pageContents);
    }

    public function test_get_url_helper_returns_expected_path()
    {
        $url = Messages::getPageUrl(['id' => 123]);
        $expectedBase = rtrim(config('filament.path', '/admin'), '/');
        $this->assertStringContainsString($expectedBase . '/messages/123', $url);
    }

    public function test_get_max_content_width_returns_config_value()
    {
        $page = new Messages();
        $this->assertEquals(config('filament-messages.max_content_width'), $page->getMaxContentWidth());
    }
}
