
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- CSS files -->
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pos-theme.css') }}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>

    <!-- Custom CSS for specific page.  -->
    @stack('page-styles')
    @livewireStyles
</head>
    <body>

        <div class="page">

            @include('layouts.body.header')

            @include('layouts.body.navbar')

            <div class="page-wrapper">
                <div>
                    @yield('content')
                </div>

                @include('layouts.body.footer')
            </div>
        </div>

        <!-- Tabler Core -->
        <script src="{{ asset('dist/js/tabler.min.js') }}" defer></script>
        {{--- Page Scripts ---}}
        @stack('page-scripts')
        
        <!-- Load Livewire scripts (includes Alpine.js) -->
        @livewireScripts
        
        <!-- Load app.js -->
        @vite(['resources/js/app.js'])
        
        <!-- Initialize Livewire -->
        <script>
            // Wait for Livewire to be fully loaded
            document.addEventListener('livewire:init', function() {
                // Handle refresh event
                Livewire.on('refresh', () => {
                    window.location.reload();
                });

                // Initialize any global Alpine.js components here
                document.addEventListener('alpine:init', () => {
                    // Example of a global component
                    window.Alpine.data('dropdown', () => ({
                        open: false,
                        toggle() {
                            this.open = !this.open;
                        },
                        close() {
                            this.open = false;
                        }
                    }));
                });
            });
        </script>

    </body>
</html>
