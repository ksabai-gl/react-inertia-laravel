import { StatusBadge } from '@/components/dashboard/StatusBadge';
import type { DashboardActivity } from '@/types/dashboard';

export function RecentActivityTable({
    activity,
}: {
    activity: DashboardActivity[];
}) {
    return (
        <div className="bg-card overflow-hidden rounded-xl border shadow-xs">
            <div className="flex items-center justify-between gap-3 border-b px-4 py-3">
                <div>
                    <h2 className="font-semibold">Recent Activity</h2>
                    <p className="text-muted-foreground text-xs">
                        Latest regression and discovery runs
                    </p>
                </div>
                <span className="bg-secondary text-secondary-foreground rounded-full px-2.5 py-0.5 text-xs">
                    {activity.length} records
                </span>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full min-w-[640px] text-left text-sm">
                    <thead className="bg-muted/40 text-muted-foreground">
                        <tr className="border-b">
                            <th className="px-4 py-2.5 font-medium">Name</th>
                            <th className="px-4 py-2.5 font-medium">Module</th>
                            <th className="px-4 py-2.5 font-medium">Status</th>
                            <th className="px-4 py-2.5 font-medium">Region</th>
                            <th className="px-4 py-2.5 font-medium">Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        {activity.map((row) => (
                            <tr key={row.name} className="border-b last:border-0">
                                <td className="px-4 py-3">
                                    <div className="font-medium">{row.name}</div>
                                    <div className="text-muted-foreground text-xs">
                                        {row.phone}
                                    </div>
                                </td>
                                <td className="text-muted-foreground px-4 py-3">
                                    {row.module}
                                </td>
                                <td className="px-4 py-3">
                                    <StatusBadge status={row.status} />
                                </td>
                                <td className="px-4 py-3 font-medium">
                                    {row.region}
                                </td>
                                <td className="text-muted-foreground px-4 py-3">
                                    {row.updated}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
