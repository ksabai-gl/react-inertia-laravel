import type { DashboardBreakdownItem } from '@/types/dashboard';

export function StatusBreakdownCard({
    breakdown,
}: {
    breakdown: DashboardBreakdownItem[];
}) {
    return (
        <div className="bg-card rounded-xl border p-4 shadow-xs">
            <h2 className="font-semibold">Status Breakdown</h2>
            <p className="text-muted-foreground mt-1 text-xs">
                Share of current test records
            </p>
            <div className="mt-4 space-y-3">
                {breakdown.map((item) => (
                    <div key={item.label} className="space-y-1.5">
                        <div className="flex items-center justify-between text-sm">
                            <span>{item.label}</span>
                            <span className="text-muted-foreground">
                                {item.count} ({item.percent}%)
                            </span>
                        </div>
                        <div className="bg-muted h-2 overflow-hidden rounded-full">
                            <div
                                className="h-full rounded-full"
                                style={{
                                    width: `${item.percent}%`,
                                    backgroundColor: item.color,
                                }}
                            />
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
