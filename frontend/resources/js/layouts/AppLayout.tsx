import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import {
    LayoutDashboard,
    Menu,
    Moon,
    PhoneCall,
    Radar,
    Server,
    Sun,
    TestTube2,
    X,
} from 'lucide-react';
import { useEffect, useState } from 'react';

const nav = [
    { title: 'Dashboard', url: '/', icon: LayoutDashboard },
    { title: 'IVR Platform', url: '/', icon: Server },
    { title: 'Regression Tests', url: '/', icon: TestTube2 },
    { title: 'Discovery Scans', url: '/', icon: Radar },
];

const THEME_STORAGE_KEY = 'ivr-dashboard-theme';

type Theme = 'light' | 'dark';

function resolveInitialTheme(): Theme {
    if (typeof window === 'undefined') {
        return 'light';
    }

    try {
        const stored = window.localStorage.getItem(THEME_STORAGE_KEY);
        if (stored === 'light' || stored === 'dark') {
            return stored;
        }
    } catch {
        // Ignore storage access failures and use fallback logic.
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
}

function Sidebar({ onNavigate }: { onNavigate?: () => void }) {
    return (
        <div className="bg-muted/40 flex h-full flex-col border-r">
            <div className="flex items-center gap-2 border-b px-4 py-3">
                <div className="bg-primary text-primary-foreground flex size-8 items-center justify-center rounded-md">
                    <PhoneCall className="size-4" />
                </div>
                <div className="min-w-0">
                    <p className="truncate text-sm font-semibold">
                        IVR Testing Platform
                    </p>
                    <p className="text-muted-foreground truncate text-xs">
                        Regression · Discovery
                    </p>
                </div>
            </div>
            <nav className="flex flex-1 flex-col gap-1 p-2">
                {nav.map((item) => (
                    <Link
                        key={item.title}
                        href={item.url}
                        onClick={onNavigate}
                        className="hover:bg-muted flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium"
                    >
                        <item.icon className="size-4 shrink-0" />
                        {item.title}
                    </Link>
                ))}
            </nav>
        </div>
    );
}

export default function AppLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const [open, setOpen] = useState(false);
    const [theme, setTheme] = useState<Theme>(() => resolveInitialTheme());

    useEffect(() => {
        const root = document.documentElement;
        root.classList.toggle('dark', theme === 'dark');

        try {
            window.localStorage.setItem(THEME_STORAGE_KEY, theme);
        } catch {
            // Ignore storage access failures.
        }
    }, [theme]);

    const isDarkTheme = theme === 'dark';

    return (
        <div className="bg-background flex min-h-svh">
            <aside className="hidden w-60 shrink-0 md:block">
                <Sidebar />
            </aside>

            {open && (
                <div className="fixed inset-0 z-40 md:hidden">
                    <button
                        type="button"
                        className="absolute inset-0 bg-black/40"
                        aria-label="Close menu"
                        onClick={() => setOpen(false)}
                    />
                    <aside className="bg-background absolute inset-y-0 left-0 w-64 shadow-lg">
                        <Sidebar onNavigate={() => setOpen(false)} />
                    </aside>
                </div>
            )}

            <div className="flex min-w-0 flex-1 flex-col">
                <header className="flex h-14 items-center gap-2 border-b px-4">
                    <button
                        type="button"
                        className={cn(
                            'hover:bg-muted inline-flex size-8 items-center justify-center rounded-md md:hidden',
                        )}
                        onClick={() => setOpen((v) => !v)}
                        aria-label="Open menu"
                    >
                        {open ? (
                            <X className="size-4" />
                        ) : (
                            <Menu className="size-4" />
                        )}
                    </button>
                    <p className="text-sm font-medium">Dashboard</p>
                    <div className="ml-auto">
                        <button
                            type="button"
                            className="border-input bg-background hover:bg-muted inline-flex h-8 items-center gap-1.5 rounded-md border px-2.5 text-xs font-medium transition-colors"
                            onClick={() =>
                                setTheme((current) =>
                                    current === 'dark' ? 'light' : 'dark',
                                )
                            }
                            aria-label={`Switch to ${isDarkTheme ? 'light' : 'dark'} theme`}
                            aria-pressed={isDarkTheme}
                        >
                            {isDarkTheme ? (
                                <Sun className="size-3.5" />
                            ) : (
                                <Moon className="size-3.5" />
                            )}
                            {isDarkTheme ? 'Light' : 'Dark'}
                        </button>
                    </div>
                </header>
                <main className="flex flex-1 flex-col gap-4 p-4">
                    {children}
                </main>
            </div>
        </div>
    );
}
