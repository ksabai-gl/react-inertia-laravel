import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import AppLayout from './AppLayout';

vi.mock('@inertiajs/react', () => ({
    Link: ({ children, ...props }: React.ComponentProps<'a'>) => (
        <a {...props}>{children}</a>
    ),
}));

function mockMatchMedia(prefersDark: boolean) {
    Object.defineProperty(window, 'matchMedia', {
        writable: true,
        value: vi.fn().mockImplementation(() => ({
            matches: prefersDark,
            media: '(prefers-color-scheme: dark)',
            onchange: null,
            addListener: vi.fn(),
            removeListener: vi.fn(),
            addEventListener: vi.fn(),
            removeEventListener: vi.fn(),
            dispatchEvent: vi.fn(),
        })),
    });
}

describe('AppLayout theme behavior', () => {
    beforeEach(() => {
        window.localStorage.clear();
        document.documentElement.classList.remove('dark');
        mockMatchMedia(false);
    });

    it('renders an accessible theme toggle and persists user choice', async () => {
        // MAD-142 AC-A01/AC-A02
        render(
            <AppLayout>
                <div>Dashboard body</div>
            </AppLayout>,
        );

        const toggle = screen.getByRole('button', {
            name: /switch to dark theme/i,
        });
        expect(toggle).toHaveTextContent('Dark');

        fireEvent.click(toggle);

        await waitFor(() => {
            expect(document.documentElement).toHaveClass('dark');
            expect(window.localStorage.getItem('ivr-dashboard-theme')).toBe('dark');
        });

        expect(
            screen.getByRole('button', { name: /switch to light theme/i }),
        ).toHaveAttribute('aria-pressed', 'true');
    });

    it('uses stored theme when persisted value is valid', async () => {
        // MAD-142 AC-A02 / analysis field_validations.general[0]
        window.localStorage.setItem('ivr-dashboard-theme', 'dark');

        render(
            <AppLayout>
                <div>Dashboard body</div>
            </AppLayout>,
        );

        await waitFor(() => {
            expect(document.documentElement).toHaveClass('dark');
        });

        expect(screen.getByRole('button', { name: /switch to light theme/i })).toBeInTheDocument();
    });

    it('falls back to system preference for invalid stored values', async () => {
        // MAD-142 AC-A04 / analysis validation_classifications.input[0]
        window.localStorage.setItem('ivr-dashboard-theme', 'invalid-theme');
        mockMatchMedia(true);

        render(
            <AppLayout>
                <div>Dashboard body</div>
            </AppLayout>,
        );

        await waitFor(() => {
            expect(document.documentElement).toHaveClass('dark');
            expect(window.localStorage.getItem('ivr-dashboard-theme')).toBe('dark');
        });
    });
});
