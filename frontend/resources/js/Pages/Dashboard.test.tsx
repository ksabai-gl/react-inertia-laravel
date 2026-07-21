import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import Dashboard from './Dashboard';

vi.mock('@/layouts/AppLayout', () => ({
    default: ({ children }: { children: React.ReactNode }) => <>{children}</>,
}));

vi.mock('@inertiajs/react', () => ({
    Head: ({ title }: { title: string }) => <title>{title}</title>,
}));

const dashboardProps = {
    stats: [
        {
            key: 'records',
            label: 'Records',
            value: '3',
            hint: 'Total records',
        },
    ],
    activity: [
        {
            name: 'Alpha regression',
            phone: '+1 555 0001',
            module: 'Regression',
            status: 'active' as const,
            region: 'US',
            updated: '2026-07-21 09:00',
        },
        {
            name: 'Bravo discovery',
            phone: '+44 555 0002',
            module: 'Discovery',
            status: 'failed' as const,
            region: 'UK',
            updated: '2026-07-21 10:00',
        },
        {
            name: 'Charlie regression',
            phone: '+91 555 0003',
            module: 'Regression',
            status: 'paused' as const,
            region: 'IN',
            updated: '2026-07-21 11:00',
        },
    ],
    breakdown: [
        {
            label: 'Active',
            count: 1,
            percent: 33,
            color: '#22c55e',
        },
    ],
    regions: [
        {
            region: 'US',
            records: 1,
        },
    ],
};

function renderDashboard() {
    return render(<Dashboard {...dashboardProps} />);
}

describe('Dashboard recent activity filters', () => {
    it('renders sorted module and region filter options above Recent Activity', () => {
        renderDashboard();

        // AC-DASH-FILTER-01: filter controls are available before the activity table.
        expect(screen.getByRole('heading', { name: 'Recent Activity' })).toBeInTheDocument();

        expect(
            within(screen.getByLabelText('Module')).getAllByRole('option').map((option) => option.textContent),
        ).toEqual(['All modules', 'Discovery', 'Regression']);
        expect(
            within(screen.getByLabelText('Region')).getAllByRole('option').map((option) => option.textContent),
        ).toEqual(['All regions', 'IN', 'UK', 'US']);
        expect(screen.getByText('3 of 3 records')).toBeInTheDocument();
    });

    it('filters activity by combined module, status, and region selections', async () => {
        const user = userEvent.setup();
        renderDashboard();

        // AC-DASH-FILTER-02: filters can be combined without a server round trip.
        await user.selectOptions(screen.getByLabelText('Module'), 'Regression');
        await user.selectOptions(screen.getByLabelText('Status'), 'paused');
        await user.selectOptions(screen.getByLabelText('Region'), 'IN');

        expect(screen.getByText('1 of 3 records')).toBeInTheDocument();
        expect(screen.getByText('Charlie regression')).toBeInTheDocument();
        expect(screen.queryByText('Alpha regression')).not.toBeInTheDocument();
        expect(screen.queryByText('Bravo discovery')).not.toBeInTheDocument();
    });

    it('shows an empty state when no activity matches selected filters', async () => {
        const user = userEvent.setup();
        renderDashboard();

        // AC-DASH-FILTER-03: no-match filters preserve the table with a clear empty state.
        await user.selectOptions(screen.getByLabelText('Module'), 'Discovery');
        await user.selectOptions(screen.getByLabelText('Status'), 'paused');

        expect(screen.getByText('0 of 3 records')).toBeInTheDocument();
        expect(screen.getByText('No activity matches the selected filters.')).toBeInTheDocument();
        expect(screen.queryByText('Bravo discovery')).not.toBeInTheDocument();
    });

    it('clears active filters and restores all activity rows', async () => {
        const user = userEvent.setup();
        renderDashboard();

        // AC-DASH-FILTER-04: clear action resets all filters to the full activity list.
        await user.selectOptions(screen.getByLabelText('Module'), 'Discovery');
        expect(screen.getByText('1 of 3 records')).toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Clear filters' }));

        expect(screen.getByLabelText('Module')).toHaveValue('all');
        expect(screen.getByLabelText('Status')).toHaveValue('all');
        expect(screen.getByLabelText('Region')).toHaveValue('all');
        expect(screen.getByText('3 of 3 records')).toBeInTheDocument();
        expect(screen.getByText('Alpha regression')).toBeInTheDocument();
        expect(screen.getByText('Bravo discovery')).toBeInTheDocument();
        expect(screen.getByText('Charlie regression')).toBeInTheDocument();
    });
});
