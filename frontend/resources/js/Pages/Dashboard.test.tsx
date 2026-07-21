import { fireEvent, render, screen, within } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import Dashboard from './Dashboard';

vi.mock('@inertiajs/react', () => ({
    Head: ({ title }: { title: string }) => <title>{title}</title>,
}));

vi.mock('@/layouts/AppLayout', () => ({
    default: ({ children }: { children: React.ReactNode }) => (
        <main>{children}</main>
    ),
}));

const props = {
    stats: [],
    activity: [
        {
            name: 'Outbound Smoke Test',
            phone: '+1 555 0100',
            module: 'Regression',
            status: 'active' as const,
            region: 'US East',
            updated: '2 minutes ago',
        },
        {
            name: 'Menu Discovery',
            phone: '+44 20 0100',
            module: 'Discovery',
            status: 'paused' as const,
            region: 'Europe',
            updated: '12 minutes ago',
        },
        {
            name: 'Payment Path Alert',
            phone: '+61 2 0100',
            module: 'Regression',
            status: 'failed' as const,
            region: 'APAC',
            updated: '1 hour ago',
        },
    ],
    breakdown: [],
    regions: [],
};

const renderDashboard = () => render(<Dashboard {...props} />);

const recentActivity = () =>
    screen.getByRole('table', { name: '' }).closest('div')?.parentElement ??
    document.body;

describe('Dashboard recent activity filters', () => {
    it('renders all activity rows and sorted filter options by default', () => {
        renderDashboard();

        expect(screen.getByText('3 of 3 records')).toBeInTheDocument();
        expect(screen.getByText('Outbound Smoke Test')).toBeInTheDocument();
        expect(screen.getByText('Menu Discovery')).toBeInTheDocument();
        expect(screen.getByText('Payment Path Alert')).toBeInTheDocument();

        expect(screen.getByLabelText('Module')).toHaveTextContent(
            'All modulesDiscoveryRegression',
        );
        expect(screen.getByLabelText('Region')).toHaveTextContent(
            'All regionsAPACEuropeUS East',
        );
        expect(screen.getByRole('button', { name: 'Clear filters' })).toBeDisabled();
    });

    it('filters rows by module', () => {
        renderDashboard();

        fireEvent.change(screen.getByLabelText('Module'), {
            target: { value: 'Discovery' },
        });

        expect(screen.getByText('1 of 3 records')).toBeInTheDocument();
        expect(screen.getByText('Menu Discovery')).toBeInTheDocument();
        expect(screen.queryByText('Outbound Smoke Test')).not.toBeInTheDocument();
    });

    it('filters rows by status and region together', () => {
        renderDashboard();

        fireEvent.change(screen.getByLabelText('Status'), {
            target: { value: 'failed' },
        });
        fireEvent.change(screen.getByLabelText('Region'), {
            target: { value: 'APAC' },
        });

        const table = recentActivity();
        expect(screen.getByText('1 of 3 records')).toBeInTheDocument();
        expect(within(table).getByText('Payment Path Alert')).toBeInTheDocument();
        expect(within(table).queryByText('Menu Discovery')).not.toBeInTheDocument();
    });

    it('shows an empty state when no rows match and clears filters', () => {
        renderDashboard();

        fireEvent.change(screen.getByLabelText('Module'), {
            target: { value: 'Discovery' },
        });
        fireEvent.change(screen.getByLabelText('Status'), {
            target: { value: 'failed' },
        });

        expect(screen.getByText('0 of 3 records')).toBeInTheDocument();
        expect(
            screen.getByText('No recent activity matches the selected filters.'),
        ).toBeInTheDocument();

        fireEvent.click(screen.getByRole('button', { name: 'Clear filters' }));

        expect(screen.getByText('3 of 3 records')).toBeInTheDocument();
        expect(screen.getByText('Outbound Smoke Test')).toBeInTheDocument();
    });
});
