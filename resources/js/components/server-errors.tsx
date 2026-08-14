import { usePage } from '@inertiajs/react';
import AlertError from '@/components/alert-error';

/**
 * Renders the validation errors Laravel sent back for the last submission.
 *
 * Laravel returns them on Inertia's shared `errors` prop, which is separate
 * from react-hook-form's `errors` object — that one only ever holds
 * client-side zod failures. A form that renders only the latter silently
 * re-renders on a rejected submit and looks like the button did nothing.
 *
 * Forms also push field messages into react-hook-form via setError so the
 * offending input is marked inline. Repeating them in this summary is
 * deliberate: it is what screen-reader and keyboard users rely on, and it is
 * the only place errors on fields without an inline slot are visible at all.
 */
export default function ServerErrors({ title }: { title?: string }) {
    const { errors } = usePage().props as unknown as {
        errors?: Record<string, string>;
    };

    const messages = Object.values(errors ?? {});

    if (messages.length === 0) {
        return null;
    }

    return <AlertError errors={messages} title={title} />;
}
