import { render, screen, waitFor } from '@testing-library/react';
import React, { type ReactNode } from 'react';
import { renderToString } from 'react-dom/server';
import { beforeEach, describe, expect, it } from 'vitest';

import { ThemeProvider, useTheme } from '@/components/theme-provider';

const STORAGE_KEY = 'mad-141-theme';

function ThemeProbe() {
    const { theme, setTheme } = useTheme();

    return (
        <>
            <span data-testid="theme-value">{theme}</span>
            <button type="button" onClick={() => setTheme('dark')}>
                Set dark
            </button>
        </>
    );
}

function renderWithProvider(children: ReactNode) {
    return render(
        <ThemeProvider defaultTheme="light" storageKey={STORAGE_KEY}>
            {children}
        </ThemeProvider>,
    );
}

describe('ThemeProvider', () => {
    beforeEach(() => {
        window.localStorage.clear();
        document.documentElement.classList.remove('dark');
    });

    it('MAD-141 AC-A06 falls back to default theme when storage value is invalid', async () => {
        window.localStorage.setItem(STORAGE_KEY, 'system');

        renderWithProvider(<ThemeProbe />);

        expect(screen.getByTestId('theme-value')).toHaveTextContent('light');

        await waitFor(() => {
            expect(document.documentElement).not.toHaveClass('dark');
        });
    });

    it('MAD-141 AC-A06 restores a valid stored theme and toggles html class', async () => {
        window.localStorage.setItem(STORAGE_KEY, 'dark');

        renderWithProvider(<ThemeProbe />);

        expect(screen.getByTestId('theme-value')).toHaveTextContent('dark');

        await waitFor(() => {
            expect(document.documentElement).toHaveClass('dark');
        });
    });

    it('MAD-141 AC-A06 persists next theme choice into localStorage', async () => {
        renderWithProvider(<ThemeProbe />);

        screen.getByRole('button', { name: 'Set dark' }).click();

        expect(window.localStorage.getItem(STORAGE_KEY)).toBe('dark');

        await waitFor(() => {
            expect(document.documentElement).toHaveClass('dark');
        });
    });

    it('MAD-141 AC-A06 does not access browser storage during server rendering', () => {
        const originalWindow = globalThis.window;

        Object.defineProperty(globalThis, 'window', {
            value: undefined,
            writable: true,
            configurable: true,
        });

        expect(() =>
            renderToString(
                <ThemeProvider defaultTheme="light" storageKey={STORAGE_KEY}>
                    <div>SSR safe</div>
                </ThemeProvider>,
            ),
        ).not.toThrow();

        Object.defineProperty(globalThis, 'window', {
            value: originalWindow,
            writable: true,
            configurable: true,
        });
    });
});
