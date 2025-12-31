<?php

namespace Raseldev99\FilamentMessages\Support;

class FilamentCompat
{
    /**
     * Register icons in a way that works across Filament versions.
     *
     * @param array<string, string> $icons
     * @return void
     */
    public static function registerIcons(array $icons): void
    {
        // Preferred: FilamentIcon facade
        if (class_exists('\Filament\Support\Facades\FilamentIcon') && method_exists('\Filament\Support\Facades\FilamentIcon', 'register')) {
            try {
                \Filament\Support\Facades\FilamentIcon::register($icons);
            } catch (\Throwable $e) {
                // ignore when running outside Laravel application
            }

            return;
        }

        // Alternate: Filament facade may provide an icon registration method
        if (class_exists('\Filament\Facades\Filament')) {
            if (method_exists('\Filament\Facades\Filament', 'registerIcons')) {
                try {
                    \Filament\Facades\Filament::registerIcons($icons);
                } catch (\Throwable $e) {
                    // ignore
                }

                return;
            }

            if (method_exists('\\Filament\\Facades\\Filament', 'registerIcon')) {
                foreach ($icons as $name => $path) {
                    try {
                        \Filament\Facades\Filament::registerIcon($name, $path);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                return;
            }
        }

        // If we reach here, no compatible API found — silently ignore to remain compatible.
    }

    /**
     * Register a named Livewire component in a way that is tolerant across environments.
     *
     * @param string $alias
     * @param class-string $class
     * @return void
     */
    public static function registerLivewireComponent(string $alias, string $class): void
    {
        // Livewire facade/static class
        if (class_exists('\Livewire\Livewire') && method_exists('\Livewire\Livewire', 'component')) {
            \Livewire\Livewire::component($alias, $class);

            return;
        }

        // Fallback: Livewire helper function
        if (function_exists('app') && app()->bound('livewire.components')) {
            try {
                \Livewire\Livewire::component($alias, $class);
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    /**
     * Try to register assets using common Filament facades/methods if available.
     *
     * @param array $assets
     * @return void
     */
    public static function registerAssets(array $assets): void
    {
        if (empty($assets)) {
            return;
        }

        // Try Filament facade with different candidate methods
        if (class_exists('\Filament\Facades\Filament')) {
            $filament = '\Filament\Facades\Filament';

            foreach ($assets as $asset) {
                // Candidate: registerScript, registerStyles, registerAsset, registerTheme
                if (method_exists($filament, 'registerAsset')) {
                    try {
                        $filament::registerAsset($asset);
                        continue;
                    } catch (\Throwable $e) {
                        // ignore and try other methods
                    }
                }

                if (method_exists($filament, 'registerScript') && is_string($asset)) {
                    try {
                        $filament::registerScript($asset);
                        continue;
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                if (method_exists($filament, 'registerStyles') && is_string($asset)) {
                    try {
                        $filament::registerStyles($asset);
                        continue;
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        }

        // Best-effort only: do not throw if unknown.
    }
}
