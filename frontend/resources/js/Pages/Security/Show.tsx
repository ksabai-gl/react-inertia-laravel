import { Button } from '@/components/ui/button';
import ErrorFeedback from '@/components/ui/error-feedback';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';
import { toast } from 'sonner';
import { TwoFactorAuthenticationForm } from './Partials/TwoFactorAuthenticationForm';

type BrowserSession = {
    id: string;
    ip_address: string | null;
    user_agent: string | null;
    last_activity: string | null;
    last_active_ago: string | null;
    is_current_device: boolean;
};

export default function Show({
    isTwoFactorAuthenticationFeatureEnabled,
    sessions = [],
}: PageProps<{
    isTwoFactorAuthenticationFeatureEnabled: boolean;
    sessions?: BrowserSession[];
}>) {
    const [selectedSessionId, setSelectedSessionId] = useState<string | null>(
        null,
    );

    const {
        data,
        setData,
        delete: destroy,
        processing,
        errors,
        reset,
    } = useForm({
        password: '',
    });

    const terminateOthers: FormEventHandler = (e) => {
        e.preventDefault();
        destroy(route('sessions.destroy-others'), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Other sessions terminated');
                reset('password');
            },
            onError: () => {
                toast.error('Unable to terminate other sessions');
            },
        });
    };

    const terminateSession = (sessionId: string) => {
        setSelectedSessionId(sessionId);
        destroy(route('sessions.destroy', sessionId), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Session terminated');
                reset('password');
                setSelectedSessionId(null);
            },
            onError: () => {
                toast.error('Unable to terminate session');
                setSelectedSessionId(null);
            },
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Security" />

            <div className="flex flex-col">
                {isTwoFactorAuthenticationFeatureEnabled && (
                    <div className="p-4 sm:p-8 sm:pt-4">
                        <TwoFactorAuthenticationForm />
                    </div>
                )}

                <div className="p-4 sm:p-8">
                    <section className="flex max-w-2xl flex-col gap-6">
                        <header className="flex flex-col gap-2">
                            <h2 className="text-lg font-medium">
                                Browser sessions
                            </h2>
                            <p className="text-muted-foreground text-sm">
                                Manage and terminate your active sessions across
                                devices. Confirm with your password to continue.
                            </p>
                        </header>

                        <form
                            onSubmit={terminateOthers}
                            className="flex flex-col gap-4"
                        >
                            <div className="flex flex-col gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    className="max-w-lg"
                                    value={data.password}
                                    autoComplete="current-password"
                                    onChange={(e) =>
                                        setData('password', e.target.value)
                                    }
                                    required
                                />
                                <ErrorFeedback message={errors.password} />
                                <ErrorFeedback message={errors.session} />
                            </div>

                            <div className="flex flex-wrap gap-3">
                                <Button
                                    type="submit"
                                    variant="destructive"
                                    disabled={processing || !data.password}
                                >
                                    Log out other sessions
                                </Button>
                            </div>
                        </form>

                        <ul className="divide-y rounded-md border">
                            {sessions.length === 0 && (
                                <li className="text-muted-foreground p-4 text-sm">
                                    No active sessions found.
                                </li>
                            )}
                            {sessions.map((session) => (
                                <li
                                    key={session.id}
                                    className="flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div className="flex flex-col gap-1 text-sm">
                                        <div className="font-medium">
                                            {session.ip_address ||
                                                'Unknown IP'}
                                            {session.is_current_device && (
                                                <span className="text-muted-foreground ml-2 text-xs">
                                                    (This device)
                                                </span>
                                            )}
                                        </div>
                                        <div className="text-muted-foreground break-all">
                                            {session.user_agent ||
                                                'Unknown agent'}
                                        </div>
                                        <div className="text-muted-foreground text-xs">
                                            Last active{' '}
                                            {session.last_active_ago ||
                                                'recently'}
                                        </div>
                                    </div>
                                    {!session.is_current_device && (
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            disabled={
                                                processing || !data.password
                                            }
                                            onClick={() =>
                                                terminateSession(session.id)
                                            }
                                        >
                                            {selectedSessionId === session.id
                                                ? 'Terminating…'
                                                : 'Terminate'}
                                        </Button>
                                    )}
                                </li>
                            ))}
                        </ul>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
