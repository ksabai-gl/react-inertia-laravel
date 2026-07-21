import type { DashboardRegion } from '@/types/dashboard';

export function TopRegionsCard({ regions }: { regions: DashboardRegion[] }) {
    return (
        <div className="bg-card rounded-xl border p-4 shadow-xs">
            <h2 className="font-semibold">Top Regions</h2>
            <p className="text-muted-foreground mt-1 text-xs">
                Highest record volume
            </p>
            <div className="mt-4 space-y-3">
                {regions.map((item) => (
                    <div
                        key={item.region}
                        className="flex items-center justify-between text-sm"
                    >
                        <span className="font-medium">{item.region}</span>
                        <span className="text-muted-foreground">
                            {item.records} records
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}
