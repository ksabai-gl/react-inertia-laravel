import { createContext, useContext, useEffect, useState } from 'react';

type Theme = 'dark' | 'light';

const THEMES = new Set<Theme>(['dark', 'light']);

const ThemeContext = createContext<{
    theme: Theme;
    setTheme: (theme: Theme) => void;
}>({ theme: 'light', setTheme: () => undefined });

function getInitialTheme(storageKey: string, defaultTheme: Theme): Theme {
    if (typeof window === 'undefined') {
        return defaultTheme;
    }

    const storedTheme = window.localStorage.getItem(storageKey);
    if (storedTheme && THEMES.has(storedTheme as Theme)) {
        return storedTheme as Theme;
    }

    return defaultTheme;
}

export function ThemeProvider({
    children,
    defaultTheme = 'light',
    storageKey = 'vite-ui-theme',
}: {
    children: React.ReactNode;
    defaultTheme?: Theme;
    storageKey?: string;
}) {
    const [theme, setThemeState] = useState<Theme>(() =>
        getInitialTheme(storageKey, defaultTheme),
    );

    useEffect(() => {
        document.documentElement.classList.toggle('dark', theme === 'dark');
    }, [theme]);

    const setTheme = (next: Theme) => {
        if (typeof window !== 'undefined') {
            window.localStorage.setItem(storageKey, next);
        }

        setThemeState(next);
    };

    return (
        <ThemeContext.Provider value={{ theme, setTheme }}>
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme() {
    return useContext(ThemeContext);
}
