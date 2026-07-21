<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'React Inertia Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    <script>
        (() => {
            const key = 'ivr-dashboard-theme';
            const root = document.documentElement;
            let theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

            try {
                const saved = window.localStorage.getItem(key);
                if (saved === 'light' || saved === 'dark') {
                    theme = saved;
                }
            } catch {
                // Ignore storage access errors and keep fallback theme.
            }

            root.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    @viteReactRefresh
    @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
