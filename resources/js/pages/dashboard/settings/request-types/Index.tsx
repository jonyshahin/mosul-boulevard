import ComingSoonPage from '@/components/dashboard/coming-soon-page';

type Props = {
    translations: {
        title: string;
        coming_soon: string;
    };
};

export default function RequestTypesIndex({ translations }: Props) {
    return (
        <ComingSoonPage
            title={translations.title}
            comingSoon={translations.coming_soon}
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Settings', href: '/dashboard/settings' },
                { title: translations.title, href: '/dashboard/settings/request-types' },
            ]}
        />
    );
}
