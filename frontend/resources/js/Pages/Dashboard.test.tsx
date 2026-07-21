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
            label: 'Records',
            value: '4',
            hint: 'Current activity rows',
        },
    ],
    activity: [
        {
            name: 'North Checkout Flow',
            phone: '+1 555 0100',
            module: 'Checkout',
            status: 'active' as const,
            region: 'North America',
            updated: '2m ago',
        },
        {
            name: 'EU Billing Regression',
            phone: '+44 20 5555 0111',
            module: 'Billing',
            status: 'paused' as const,
            region: 'Europe',
            updated: '8m ago',
        },
        {
            name: 'APAC Checkout Alert',
            phone: '+65 5555 0122',
            module: 'Checkout',
            status: 'failed' as const,
            region: 'APAC',
            updated: '12m ago',
        },
        {
            name: 'EU Support Scan',
            phone: '+49 30 5555 0133',
            module: 'Support',
            status: 'active' as const,
            region: 'Europe',
            updated: '15m ago',
        },
    ],
    breakdown: [
        { label: 'Active', count: 2, percent: 50, color: '#16a34a' },
        { label: 'Paused', count: 1, percent: 25, color: '#64748b' },
        { label: 'Failed', count: 1, percent: 25, color: '#dc2626' },
    ],
    regions: [
        { region: 'Europe', records: 2 },
        { region: 'North America', records: 1 },
        { region: 'APAC', records: 1 },
    ],
};

function renderDashboard() {
    return render(<Dashboard {...dashboardProps} />);
}

describe('Dashboard Recent Activity filters', () => {
    it('MAD-dashboard-filters-AC1 renders module, status, and region controls above recent activity', () => {
        renderDashboard();

        const recentActivity = screen
            .getByRole('heading', { name: /recent activity/i })
            .closest('div')?.parentElement;

        expect(recentActivity).toBeInTheDocument();
        expect(screen.getByLabelText('Module')).toHaveValue('all');
        expect(screen.getByLabelText('Status')).toHaveValue('all');
        expect(screen.getByLabelText('Region')).toHaveValue('all');
        expect(screen.getByText('4 of 4 records')).toBeInTheDocument();
        expect(screen.getByRole('option', { name: 'Billing' })).toBeInTheDocument();
        expect(screen.getByRole('option', { name: 'Checkout' })).toBeInTheDocument();
        expect(screen.getByRole('option', { name: 'Support' })).toBeInTheDocument();
    });

    it('MAD-dashboard-filters-AC2 applies selected filters with AND semantics and updates the count', async () => {
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Module'), 'Checkout');
        await user.selectOptions(screen.getByLabelText('Status'), 'failed');
        await user.selectOptions(screen.getByLabelText('Region'), 'APAC');

        expect(screen.getByText('1 of 4 records')).toBeInTheDocument();
        expect(screen.getByText('APAC Checkout Alert')).toBeInTheDocument();
        expect(screen.queryByText('North Checkout Flow')).not.toBeInTheDocument();
        expect(screen.queryByText('EU Billing Regression')).not.toBeInTheDocument();
        expect(screen.queryByText('EU Support Scan')).not.toBeInTheDocument();
    });

    it('MAD-dashboard-filters-AC3 restores all rows when filters return to all', async () => {
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Region'), 'Europe');
        expect(screen.getByText('2 of 4 records')).toBeInTheDocument();

        await user.selectOptions(screen.getByLabelText('Region'), 'all');

        expect(screen.getByText('4 of 4 records')).toBeInTheDocument();
        expect(screen.getByText('North Checkout Flow')).toBeInTheDocument();
        expect(screen.getByText('EU Billing Regression')).toBeInTheDocument();
        expect(screen.getByText('APAC Checkout Alert')).toBeInTheDocument();
        expect(screen.getByText('EU Support Scan')).toBeInTheDocument();
    });

    it('MAD-dashboard-filters-AC4 shows a no-results row when no activity matches', async () => {
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Module'), 'Billing');
        await user.selectOptions(screen.getByLabelText('Status'), 'failed');

        expect(screen.getByText('0 of 4 records')).toBeInTheDocument();
        expect(
            screen.getByText('No activity matches the selected filters.'),
        ).toBeInTheDocument();

        const table = screen.getByRole('table');
        expect(
            within(table).queryByText('EU Billing Regression'),
        ).not.toBeInTheDocument();
    });
});
