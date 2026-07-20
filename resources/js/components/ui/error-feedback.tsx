import { HTMLAttributes } from 'react';

export default function ErrorFeedback({
    message,
    className = '',
    ...props
}: HTMLAttributes<HTMLParagraphElement> & { message?: string }) {
    return message ? (
        <p {...props} className={`text-destructive text-sm ${className}`.trim()}>
            {message}
        </p>
    ) : null;
}
