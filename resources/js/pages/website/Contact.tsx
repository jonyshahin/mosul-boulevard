import { Head } from '@inertiajs/react';
import { Mail, MapPin, Phone } from 'lucide-react';
import { useState } from 'react';
import { toast, Toaster } from 'sonner';
import WebsiteLayout from '@/layouts/website-layout';

export default function Contact() {
    const [form, setForm] = useState({ name: '', email: '', subject: '', message: '' });
    const [sending, setSending] = useState(false);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setSending(true);
        setTimeout(() => {
            toast.success('Message sent! We will get back to you soon.');
            setForm({ name: '', email: '', subject: '', message: '' });
            setSending(false);
        }, 800);
    }

    return (
        <WebsiteLayout>
            <Head title="Contact Us | Mosul Boulevard" />
            <Toaster position="top-right" theme="dark" />

            {/* Hero */}
            <section className="border-b border-white/10 bg-gradient-to-b from-[#1B1B1B] to-[#1B1B1B]/95 px-4 py-20">
                <div className="mx-auto max-w-7xl text-center">
                    <Mail className="mx-auto mb-4 h-10 w-10 text-[#B8860B]" />
                    <h1 className="mb-2 text-4xl font-bold">Contact Us</h1>
                    <p className="text-lg text-gray-400">Get in touch with our team</p>
                </div>
            </section>

            <section className="px-4 py-16">
                <div className="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2">
                    {/* Contact Info */}
                    <div>
                        <h2 className="mb-6 text-2xl font-bold">Get In Touch</h2>
                        <p className="mb-8 text-gray-400">
                            Have questions about our properties? We&apos;d love to hear from you. Reach out to us
                            through any of the channels below.
                        </p>

                        <div className="space-y-6">
                            <div className="flex items-start gap-4">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#B8860B]/10">
                                    <Phone className="h-5 w-5 text-[#B8860B]" />
                                </div>
                                <div>
                                    <h3 className="font-medium">Phone</h3>
                                    <p className="text-gray-400">+964 770 000 0000</p>
                                </div>
                            </div>

                            <div className="flex items-start gap-4">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#B8860B]/10">
                                    <Mail className="h-5 w-5 text-[#B8860B]" />
                                </div>
                                <div>
                                    <h3 className="font-medium">Email</h3>
                                    <p className="text-gray-400">info@mosulboulevard.com</p>
                                </div>
                            </div>

                            <div className="flex items-start gap-4">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#B8860B]/10">
                                    <MapPin className="h-5 w-5 text-[#B8860B]" />
                                </div>
                                <div>
                                    <h3 className="font-medium">Address</h3>
                                    <p className="text-gray-400">
                                        Mosul Boulevard Project<br />
                                        Mosul, Nineveh Governorate<br />
                                        Iraq
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Contact Form */}
                    <div className="rounded-xl border border-white/10 bg-white/5 p-8">
                        <h2 className="mb-6 text-xl font-bold">Send a Message</h2>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-300">Name</label>
                                    <input
                                        type="text"
                                        required
                                        value={form.name}
                                        onChange={(e) => setForm({ ...form, name: e.target.value })}
                                        className="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition-colors focus:border-[#B8860B]"
                                        placeholder="Your name"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-300">Email</label>
                                    <input
                                        type="email"
                                        required
                                        value={form.email}
                                        onChange={(e) => setForm({ ...form, email: e.target.value })}
                                        className="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition-colors focus:border-[#B8860B]"
                                        placeholder="you@example.com"
                                    />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-medium text-gray-300">Subject</label>
                                <input
                                    type="text"
                                    required
                                    value={form.subject}
                                    onChange={(e) => setForm({ ...form, subject: e.target.value })}
                                    className="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition-colors focus:border-[#B8860B]"
                                    placeholder="How can we help?"
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-medium text-gray-300">Message</label>
                                <textarea
                                    required
                                    rows={5}
                                    value={form.message}
                                    onChange={(e) => setForm({ ...form, message: e.target.value })}
                                    className="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition-colors focus:border-[#B8860B]"
                                    placeholder="Tell us more..."
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={sending}
                                className="w-full rounded-lg bg-[#B8860B] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#B8860B]/80 disabled:opacity-50"
                            >
                                {sending ? 'Sending...' : 'Send Message'}
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            {/* Map Placeholder */}
            <section className="border-t border-white/10 px-4 py-16">
                <div className="mx-auto max-w-7xl">
                    <div className="flex h-64 items-center justify-center rounded-xl border border-white/10 bg-white/5">
                        <div className="text-center">
                            <MapPin className="mx-auto mb-2 h-8 w-8 text-gray-500" />
                            <p className="text-gray-500">Map coming soon</p>
                        </div>
                    </div>
                </div>
            </section>
        </WebsiteLayout>
    );
}
