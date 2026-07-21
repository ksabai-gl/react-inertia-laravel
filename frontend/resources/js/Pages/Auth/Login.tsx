import { Button } from '@/components/ui/button';
import ErrorFeedback from '@/components/ui/error-feedback';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthenticationLayout from '@/layouts/AuthenticationLayout';
import { cn } from '@/lib/utils';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('login.store'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <AuthenticationLayout>
            <Head title="Log in" />

            <form className={cn('flex flex-col gap-6')} onSubmit={submit}>
                <div className="flex flex-col items-center gap-2 text-center">
                    <h1 className="text-2xl font-bold">Log in to your account</h1>
                    <p className="text-muted-foreground text-sm text-balance">
                        Enter your email and password to continue
                    </p>
                </div>

                {errors.email && (
                    <div className="text-center text-sm text-red-600">
                        {errors.email}
                    </div>
                )}

                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            autoComplete="username"
                            placeholder="you@example.com"
                            required
                            autoFocus
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        <ErrorFeedback message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <div className="flex items-center justify-between">
                            <Label htmlFor="password">Password</Label>
                            <Link
                                href={route('auth.forgot-password')}
                                className="text-sm underline underline-offset-4"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            autoComplete="current-password"
                            placeholder="••••••••"
                            required
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        <ErrorFeedback message={errors.password} />
                    </div>

                    <div className="flex items-center gap-2">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                            checked={data.remember}
                            className="h-4 w-4 rounded border"
                            onChange={(e) =>
                                setData('remember', e.target.checked)
                            }
                        />
                        <Label htmlFor="remember" className="font-normal">
                            Remember me
                        </Label>
                    </div>

                    <Button
                        type="submit"
                        className="w-full"
                        disabled={processing}
                    >
                        Log in
                    </Button>
                </div>

                <div className="flex justify-center gap-1 text-sm">
                    Don&apos;t have an account?
                    <Link
                        href={route('register')}
                        className="underline underline-offset-4"
                    >
                        Register
                    </Link>
                </div>
            </form>
        </AuthenticationLayout>
    );
}
