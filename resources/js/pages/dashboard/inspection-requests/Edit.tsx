import ComingSoonPage from '@/components/dashboard/coming-soon-page';

type Props = {
    id: number;
    translations: {
        title: string;
        coming_soon: string;
    };
};

export default function InspectionRequestsEdit({ id, translations }: Props) {
    return (
        <ComingSoonPage
            title={translations.title}
            comingSoon={translations.coming_soon}
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Inspections', href: '/dashboard/inspection-requests' },
                { title: `#${id}`, href: `/dashboard/inspection-requests/${id}` },
                { title: translations.title, href: `/dashboard/inspection-requests/${id}/edit` },
            ]}
        />
    );
}
