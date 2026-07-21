import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import Dashboard from './Dashboard';

vi.mock('@/layouts/AppLayout', () => ({
    default: ({ children }: { children: React.ReactNode }) => (
        <div data-testid="layout-shell">{children}</div>
    ),
}));

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
}));

describe('Dashboard status rendering', () => {
    it('renders fallback badge style for unknown activity statuses', () => {
        // MAD-142 AC-A03/AC-A05
        render(
            <Dashboard
                stats={[
                    {
                        key: 'records',
                        label: 'Records',
                        value: '1250',
                        hint: 'Up 5%',
                    },
                ]}
                activity={[
                    {
                        name: 'Nightly Run',
                        phone: '+1-212-555-0100',
                        module: 'Regression',
                        status: 'unknown',
                        region: 'US-East',
                        updated: '1m ago',
                    },
                ]}
                breakdown={[
                    {
                        label: 'Active',
                        count: 80,
                        percent: 80,
                        color: '#16a34a',
                    },
                ]}
                regions={[
                    {
                        region: 'US-East',
                        records: 1000,
                    },
                ]}
            />,
        );

        expect(screen.getByTestId('layout-shell')).toBeInTheDocument();

        const statusBadge = screen.getByText('unknown');
        expect(statusBadge).toHaveClass('bg-muted');
        expect(statusBadge).toHaveClass('text-muted-foreground');
    });
});
