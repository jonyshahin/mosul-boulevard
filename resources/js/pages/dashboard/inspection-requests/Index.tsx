import ComingSoonPage from '@/components/dashboard/coming-soon-page';

type Props = {
    translations: {
        title: string;
        coming_soon: string;
    };
};

export default function InspectionRequestsIndex({ translations }: Props) {
    return (
        <ComingSoonPage
            title={translations.title}
            comingSoon={translations.coming_soon}
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: translations.title, href: '/dashboard/inspection-requests' },
            ]}
        />
    );
}
