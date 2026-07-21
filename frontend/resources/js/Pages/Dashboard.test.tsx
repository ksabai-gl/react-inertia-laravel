import '@testing-library/jest-dom/vitest';
import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
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

type DashboardProps = React.ComponentProps<typeof Dashboard>;

const baseProps: DashboardProps = {
    stats: [
        {
            key: 'records',
            label: 'Total Records',
            value: '4',
            hint: 'Across all modules',
        },
    ],
    activity: [
        {
            name: 'Zulu Payment Flow',
            phone: '+1 555 0101',
            module: 'Payments',
            status: 'active',
            region: 'US',
            updated: '10 minutes ago',
        },
        {
            name: 'Alpha Claims Flow',
            phone: '+44 20 0101',
            module: 'Claims',
            status: 'failed',
            region: 'UK',
            updated: '20 minutes ago',
        },
        {
            name: 'Benefits Spanish Flow',
            phone: '+34 91 0101',
            module: 'Benefits',
            status: 'paused',
            region: 'ES',
            updated: '30 minutes ago',
        },
        {
            name: 'Claims Escalation Flow',
            phone: '+1 555 0102',
            module: 'Claims',
            status: 'active',
            region: 'US',
            updated: '40 minutes ago',
        },
    ],
    breakdown: [
        { label: 'Active', count: 2, percent: 50, color: '#16a34a' },
        { label: 'Paused', count: 1, percent: 25, color: '#f59e0b' },
        { label: 'Failed', count: 1, percent: 25, color: '#dc2626' },
    ],
    regions: [
        { region: 'US', records: 2 },
        { region: 'UK', records: 1 },
    ],
};

const renderDashboard = (props: Partial<DashboardProps> = {}) =>
    render(<Dashboard {...baseProps} {...props} />);

const activityBody = () =>
    within(screen.getByRole('table')).getAllByRole('rowgroup')[1];

const expectVisibleRows = (names: string[]) => {
    const body = activityBody();

    names.forEach((name) => {
        expect(within(body).getByText(name)).toBeInTheDocument();
    });

    baseProps.activity
        .map((row) => row.name)
        .filter((name) => !names.includes(name))
        .forEach((name) => {
            expect(within(body).queryByText(name)).not.toBeInTheDocument();
        });
};

describe('Dashboard Recent Activity filters', () => {
    it('MAD-DASH-FILTER-01 renders filter controls above Recent Activity with all rows selected by default', () => {
        renderDashboard();

        expect(screen.getByRole('heading', { name: 'Recent Activity' })).toBeInTheDocument();
        expect(screen.getByLabelText('Module')).toHaveValue('all');
        expect(screen.getByLabelText('Status')).toHaveValue('all');
        expect(screen.getByLabelText('Region')).toHaveValue('all');
        expect(screen.getByText('4 of 4 records')).toBeInTheDocument();
        expect(screen.queryByRole('button', { name: 'Clear filters' })).not.toBeInTheDocument();
        expectVisibleRows(baseProps.activity.map((row) => row.name));
    });

    it('MAD-DASH-FILTER-02 lists module and region options once in alphabetical order', () => {
        renderDashboard();

        expect(
            within(screen.getByLabelText('Module')).getAllByRole('option').map((option) => ({
                label: option.textContent,
                value: option.getAttribute('value'),
            })),
        ).toEqual([
            { label: 'All modules', value: 'all' },
            { label: 'Benefits', value: 'Benefits' },
            { label: 'Claims', value: 'Claims' },
            { label: 'Payments', value: 'Payments' },
        ]);

        expect(
            within(screen.getByLabelText('Region')).getAllByRole('option').map((option) => ({
                label: option.textContent,
                value: option.getAttribute('value'),
            })),
        ).toEqual([
            { label: 'All regions', value: 'all' },
            { label: 'ES', value: 'ES' },
            { label: 'UK', value: 'UK' },
            { label: 'US', value: 'US' },
        ]);
    });

    it('MAD-DASH-FILTER-03 filters activity rows by selected module', async () => {
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Module'), 'Claims');

        expect(screen.getByText('2 of 4 records')).toBeInTheDocument();
        expectVisibleRows(['Alpha Claims Flow', 'Claims Escalation Flow']);
        expect(screen.getByRole('button', { name: 'Clear filters' })).toBeInTheDocument();
    });

    it('MAD-DASH-FILTER-04 filters activity rows by selected status', async () => {
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Status'), 'failed');

        expect(screen.getByText('1 of 4 records')).toBeInTheDocument();
        expectVisibleRows(['Alpha Claims Flow']);
    });

    it('MAD-DASH-FILTER-05 filters activity rows by selected region', async () => {
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Region'), 'US');

        expect(screen.getByText('2 of 4 records')).toBeInTheDocument();
        expectVisibleRows(['Zulu Payment Flow', 'Claims Escalation Flow']);
    });

    it('MAD-DASH-FILTER-06 combines filters, shows an empty state, and clears back to defaults', async () => {
        const user = userEvent.setup();
        renderDashboard();

        await user.selectOptions(screen.getByLabelText('Module'), 'Payments');
        await user.selectOptions(screen.getByLabelText('Status'), 'failed');
        await user.selectOptions(screen.getByLabelText('Region'), 'UK');

        expect(screen.getByText('0 of 4 records')).toBeInTheDocument();
        expect(screen.getByText('No activity matches the selected filters.')).toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Clear filters' }));

        expect(screen.getByLabelText('Module')).toHaveValue('all');
        expect(screen.getByLabelText('Status')).toHaveValue('all');
        expect(screen.getByLabelText('Region')).toHaveValue('all');
        expect(screen.getByText('4 of 4 records')).toBeInTheDocument();
        expect(screen.queryByText('No activity matches the selected filters.')).not.toBeInTheDocument();
        expectVisibleRows(baseProps.activity.map((row) => row.name));
    });
});
