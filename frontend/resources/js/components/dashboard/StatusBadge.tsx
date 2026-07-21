import type { DashboardStatus } from '@/types/dashboard';

const badgeClass: Record<DashboardStatus, string> = {
    active: 'bg-primary text-primary-foreground',
    failed: 'bg-destructive text-white',
    paused: 'bg-secondary text-secondary-foreground',
};

export function StatusBadge({ status }: { status: DashboardStatus }) {
    return (
        <span
            className={`inline-flex rounded-full px-2.5 py-0.5 text-xs capitalize ${badgeClass[status]}`}
        >
            {status}
        </span>
    );
}
