import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { vi } from 'vitest';
import Dashboard from './Dashboard';

vi.mock('@inertiajs/react', () => ({
    Head: ({ title }: { title: string }) => <title>{title}</title>,
}));

vi.mock('@/layouts/AppLayout', () => ({
    default: ({ children }: { children: React.ReactNode }) => (
        <main>{children}</main>
    ),
}));

const dashboardProps = {
    stats: [
        {
            key: 'records',
            label: 'Total Records',
            value: '3',
            hint: 'Across all modules',
        },
    ],
    activity: [
        {
            name: 'Regression Smoke',
            phone: '+1 555 0100',
            module: 'Regression',
            status: 'active' as const,
            region: 'North America',
            updated: '2m ago',
        },
        {
            name: 'Discovery Sweep',
            phone: '+44 20 0100',
            module: 'Discovery',
            status: 'paused' as const,
            region: 'Europe',
            updated: '12m ago',
        },
        {
            name: 'Billing Alert',
            phone: '+91 80 0100',
            module: 'Billing',
            status: 'failed' as const,
            region: 'APAC',
            updated: '1h ago',
        },
    ],
    breakdown: [
        { label: 'Active', count: 1, percent: 34, color: '#16a34a' },
        { label: 'Paused', count: 1, percent: 33, color: '#64748b' },
        { label: 'Failed', count: 1, percent: 33, color: '#dc2626' },
    ],
    regions: [
        { region: 'North America', records: 1 },
        { region: 'Europe', records: 1 },
        { region: 'APAC', records: 1 },
    ],
};

function renderDashboard() {
    return render(<Dashboard {...dashboardProps} />);
}

describe('Dashboard recent activity filters', () => {
    it('renders filter controls with options derived from activity rows', () => {
        // AC-F01: users can filter Recent Activity by module, status, and region.
        renderDashboard();

        const moduleFilter = screen.getByLabelText('Module');
        const statusFilter = screen.getByLabelText('Status');
        const regionFilter = screen.getByLabelText('Region');

        expect(moduleFilter).toHaveDisplayValue('All modules');
        expect(statusFilter).toHaveDisplayValue('All statuses');
        expect(regionFilter).toHaveDisplayValue('All regions');
        expect(within(moduleFilter).getByRole('option', { name: 'Billing' })).toBeInTheDocument();
        expect(within(moduleFilter).getByRole('option', { name: 'Discovery' })).toBeInTheDocument();
        expect(within(moduleFilter).getByRole('option', { name: 'Regression' })).toBeInTheDocument();
        expect(within(regionFilter).getByRole('option', { name: 'APAC' })).toBeInTheDocument();
        expect(within(regionFilter).getByRole('option', { name: 'Europe' })).toBeInTheDocument();
        expect(within(regionFilter).getByRole('option', { name: 'North America' })).toBeInTheDocument();
    });

    it('filters recent activity by the selected module, status, and region together', async () => {
        // AC-F02: combined filters narrow the table and update the visible count.
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Module'), 'Billing');
        await user.selectOptions(screen.getByLabelText('Status'), 'failed');
        await user.selectOptions(screen.getByLabelText('Region'), 'APAC');

        expect(screen.getByText('1 of 3 records')).toBeInTheDocument();
        expect(screen.getByText('Billing Alert')).toBeInTheDocument();
        expect(screen.queryByText('Regression Smoke')).not.toBeInTheDocument();
        expect(screen.queryByText('Discovery Sweep')).not.toBeInTheDocument();
    });

    it('shows an empty state when no activity matches selected filters', async () => {
        // AC-F03: no-match filter combinations provide a clear table empty state.
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Module'), 'Regression');
        await user.selectOptions(screen.getByLabelText('Status'), 'failed');

        expect(screen.getByText('0 of 3 records')).toBeInTheDocument();
        expect(
            screen.getByText('No activity matches the selected filters.'),
        ).toBeInTheDocument();
        expect(screen.queryByText('Regression Smoke')).not.toBeInTheDocument();
    });
});
