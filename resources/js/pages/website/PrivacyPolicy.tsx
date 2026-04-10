import { Head } from '@inertiajs/react';
import { Shield } from 'lucide-react';
import WebsiteLayout from '@/layouts/website-layout';

function Section({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <div className="space-y-3">
            <h2 className="text-xl font-bold">{title}</h2>
            <div className="space-y-2 text-gray-400 leading-relaxed">{children}</div>
        </div>
    );
}

export default function PrivacyPolicy() {
    return (
        <WebsiteLayout>
            <Head title="Privacy Policy | Mosul Boulevard" />

            {/* Hero */}
            <section className="border-b border-white/10 bg-gradient-to-b from-[#1B1B1B] to-[#1B1B1B]/95 px-4 py-20">
                <div className="mx-auto max-w-7xl text-center">
                    <Shield className="mx-auto mb-4 h-10 w-10 text-[#B8860B]" />
                    <h1 className="mb-2 text-4xl font-bold">Privacy Policy</h1>
                    <p className="text-lg text-gray-400">
                        How we handle your data at Mosul Boulevard
                    </p>
                </div>
            </section>

            {/* Content */}
            <section className="px-4 py-16">
                <div className="mx-auto max-w-3xl space-y-10">
                    <Section title="Introduction">
                        <p>
                            Mosul Boulevard (&ldquo;we&rdquo;, &ldquo;our&rdquo;, or &ldquo;us&rdquo;) is developed
                            and operated by <strong className="text-white">Multi Projects Company MPC Group</strong>.
                            This Privacy Policy explains how we collect, use, and protect your information when you
                            use the Mosul Boulevard mobile application and website.
                        </p>
                        <p>
                            By using our services, you agree to the collection and use of information in accordance
                            with this policy.
                        </p>
                    </Section>

                    <Section title="Information We Collect">
                        <p>We collect only the minimum information necessary to provide our services:</p>
                        <ul className="list-inside list-disc space-y-1 pl-2">
                            <li>
                                <strong className="text-gray-300">Name</strong> &mdash; to identify your account
                            </li>
                            <li>
                                <strong className="text-gray-300">Email address</strong> &mdash; for account creation
                                and communication
                            </li>
                            <li>
                                <strong className="text-gray-300">Phone number</strong> &mdash; for authentication and
                                customer identification
                            </li>
                            <li>
                                <strong className="text-gray-300">Authentication tokens</strong> &mdash; to keep you
                                securely signed in
                            </li>
                        </ul>
                    </Section>

                    <Section title="How We Use Your Information">
                        <p>The information we collect is used solely for:</p>
                        <ul className="list-inside list-disc space-y-1 pl-2">
                            <li>Creating and managing your user account</li>
                            <li>Authenticating your identity when you sign in</li>
                            <li>Allowing you to track the progress of your property (villa or tower unit)</li>
                            <li>Communicating important updates about your property</li>
                        </ul>
                    </Section>

                    <Section title="Information We Do NOT Collect">
                        <p>We want to be transparent about what we do not do:</p>
                        <ul className="list-inside list-disc space-y-1 pl-2">
                            <li>We do <strong className="text-white">not</strong> collect location or GPS data</li>
                            <li>
                                We do <strong className="text-white">not</strong> collect device identifiers or
                                hardware information
                            </li>
                            <li>
                                We do <strong className="text-white">not</strong> use analytics or tracking tools
                            </li>
                            <li>We do <strong className="text-white">not</strong> display advertisements</li>
                        </ul>
                    </Section>

                    <Section title="Data Sharing">
                        <p>
                            We do <strong className="text-white">not</strong> sell, trade, or share your personal
                            information with any third parties. Your data is used exclusively within the Mosul
                            Boulevard platform to provide our services to you.
                        </p>
                    </Section>

                    <Section title="Data Security">
                        <p>
                            Your data is stored securely on encrypted servers hosted on{' '}
                            <strong className="text-gray-300">Laravel Cloud</strong>, a managed cloud
                            infrastructure platform. We employ industry-standard security practices including
                            encrypted data transmission (HTTPS/TLS), hashed passwords, and token-based
                            authentication to protect your information.
                        </p>
                    </Section>

                    <Section title="Data Deletion">
                        <p>
                            You may request the deletion of your account and all associated personal data at any
                            time by contacting us at{' '}
                            <a
                                href="mailto:itmanager.mpc@gmail.com"
                                className="text-[#B8860B] underline underline-offset-4 hover:text-[#B8860B]/80"
                            >
                                itmanager.mpc@gmail.com
                            </a>
                            . We will process your request within 30 days and confirm deletion via email.
                        </p>
                    </Section>

                    <Section title="Children&rsquo;s Privacy">
                        <p>
                            The Mosul Boulevard application is not intended for use by children under the age of
                            13. We do not knowingly collect personal information from children. If you believe a
                            child has provided us with personal data, please contact us and we will promptly
                            delete the information.
                        </p>
                    </Section>

                    <Section title="Changes to This Policy">
                        <p>
                            We may update this Privacy Policy from time to time. Any changes will be reflected on
                            this page with an updated revision date. We encourage you to review this page
                            periodically.
                        </p>
                    </Section>

                    <Section title="Contact Us">
                        <p>
                            If you have any questions or concerns about this Privacy Policy, please contact us:
                        </p>
                        <div className="rounded-xl border border-white/10 bg-white/5 p-6">
                            <dl className="space-y-3">
                                <div>
                                    <dt className="text-sm text-gray-500">Developer</dt>
                                    <dd className="font-medium text-white">Multi Projects Company MPC Group</dd>
                                </div>
                                <div>
                                    <dt className="text-sm text-gray-500">Email</dt>
                                    <dd>
                                        <a
                                            href="mailto:itmanager.mpc@gmail.com"
                                            className="text-[#B8860B] hover:text-[#B8860B]/80"
                                        >
                                            itmanager.mpc@gmail.com
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-sm text-gray-500">App Name</dt>
                                    <dd className="font-medium text-white">Mosul Boulevard</dd>
                                </div>
                            </dl>
                        </div>
                    </Section>

                    {/* Last Updated */}
                    <p className="border-t border-white/10 pt-6 text-sm text-gray-500">
                        Last updated: April 2026
                    </p>
                </div>
            </section>
        </WebsiteLayout>
    );
}
