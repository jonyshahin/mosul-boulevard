import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <rect width="32" height="32" rx="4" fill="#B8860B" />
            <text
                x="16"
                y="22"
                textAnchor="middle"
                fontFamily="Inter, Arial, sans-serif"
                fontSize="18"
                fontWeight="700"
                fill="white"
            >
                MB
            </text>
        </svg>
    );
}
