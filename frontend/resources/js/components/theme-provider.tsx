import { createContext, useContext, useEffect, useState } from 'react';

type Theme = 'dark' | 'light';

const ThemeContext = createContext<{
    theme: Theme;
    setTheme: (theme: Theme) => void;
}>({ theme: 'light', setTheme: () => undefined });

function readStoredTheme(storageKey: string): Theme | null {
    if (typeof window === 'undefined') {
        return null;
    }

    const value = window.localStorage.getItem(storageKey);
    return value === 'dark' || value === 'light' ? value : null;
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
    const [theme, setThemeState] = useState<Theme>(
        () => readStoredTheme(storageKey) ?? defaultTheme,
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
