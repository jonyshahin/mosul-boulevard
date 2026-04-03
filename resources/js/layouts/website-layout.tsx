import { Link, usePage } from '@inertiajs/react';
import { Landmark, Menu, X } from 'lucide-react';
import { useState } from 'react';

const navLinks = [
    { title: 'Home', href: '/' },
    { title: 'Villas', href: '/villas' },
    { title: 'Towers', href: '/towers' },
    { title: 'Progress', href: '/progress' },
    { title: 'Contact', href: '/contact' },
];

function SiteHeader() {
    const { auth } = usePage().props as unknown as { auth: { user: { id: number } | null } };
    const [menuOpen, setMenuOpen] = useState(false);

    return (
        <header className="fixed top-0 z-50 w-full border-b border-white/10 bg-[#1B1B1B]/95 backdrop-blur-sm">
            <nav className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" className="flex items-center gap-2">
                    <Landmark className="h-6 w-6 text-[#B8860B]" />
                    <span className="text-lg font-bold text-white">Mosul Boulevard</span>
                </Link>

                <div className="hidden items-center gap-6 md:flex">
                    {navLinks.map((link) => (
                        <Link
                            key={link.href}
                            href={link.href}
                            className="text-sm font-medium text-gray-300 transition-colors hover:text-[#B8860B]"
                        >
                            {link.title}
                        </Link>
                    ))}
                    {auth?.user ? (
                        <Link
                            href="/dashboard"
                            className="rounded-md bg-[#B8860B] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#B8860B]/80"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <Link
                            href="/login"
                            className="rounded-md border border-[#B8860B] px-4 py-2 text-sm font-medium text-[#B8860B] transition-colors hover:bg-[#B8860B] hover:text-white"
                        >
                            Login
                        </Link>
                    )}
                </div>

                <button
                    type="button"
                    className="text-gray-300 md:hidden"
                    onClick={() => setMenuOpen(!menuOpen)}
                >
                    {menuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                </button>
            </nav>

            {menuOpen && (
                <div className="border-t border-white/10 bg-[#1B1B1B] px-4 py-4 md:hidden">
                    <div className="flex flex-col gap-3">
                        {navLinks.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className="text-sm font-medium text-gray-300 transition-colors hover:text-[#B8860B]"
                                onClick={() => setMenuOpen(false)}
                            >
                                {link.title}
                            </Link>
                        ))}
                        {auth?.user ? (
                            <Link href="/dashboard" className="rounded-md bg-[#B8860B] px-4 py-2 text-center text-sm font-medium text-white">
                                Dashboard
                            </Link>
                        ) : (
                            <Link href="/login" className="rounded-md border border-[#B8860B] px-4 py-2 text-center text-sm font-medium text-[#B8860B]">
                                Login
                            </Link>
                        )}
                    </div>
                </div>
            )}
        </header>
    );
}

function SiteFooter() {
    return (
        <footer className="border-t border-white/10 bg-[#1B1B1B] py-8">
            <p className="text-center text-sm text-gray-500">
                &copy; 2026 Mosul Boulevard. All rights reserved.
            </p>
        </footer>
    );
}

export default function WebsiteLayout({ children }: { children: React.ReactNode }) {
    return (
        <div className="min-h-screen bg-[#1B1B1B] text-white">
            <SiteHeader />
            <main className="pt-16">{children}</main>
            <SiteFooter />
        </div>
    );
}
