import { Head, Link, usePage } from '@inertiajs/react';
import { Building, Building2, Home as HomeIcon, Landmark, Menu, Users, X } from 'lucide-react';
import { useState } from 'react';

interface Stats {
    total_villas: number;
    total_tower_units: number;
    villas_sold: number;
    tower_units_sold: number;
}

interface VillaType {
    id: number;
    name: string;
    total_count: number;
}

interface TowerDefinition {
    id: number;
    name: string;
}

interface HomeProps {
    stats: Stats;
    villaTypes: VillaType[];
    towerDefinitions: TowerDefinition[];
}

function SiteHeader() {
    const { auth } = usePage().props as unknown as { auth: { user: { id: number } | null } };
    const [menuOpen, setMenuOpen] = useState(false);

    const navLinks = [
        { title: 'Home', href: '/' },
        { title: 'Villas', href: '/villas' },
        { title: 'Towers', href: '/towers' },
        { title: 'Contact', href: '/contact' },
    ];

    return (
        <header className="fixed top-0 z-50 w-full border-b border-white/10 bg-[#1B1B1B]/95 backdrop-blur-sm">
            <nav className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" className="flex items-center gap-2">
                    <Landmark className="h-6 w-6 text-[#B8860B]" />
                    <span className="text-lg font-bold text-white">Mosul Boulevard</span>
                </Link>

                {/* Desktop Nav */}
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

                {/* Mobile Menu Button */}
                <button
                    type="button"
                    className="text-gray-300 md:hidden"
                    onClick={() => setMenuOpen(!menuOpen)}
                >
                    {menuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                </button>
            </nav>

            {/* Mobile Nav */}
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
                            <Link
                                href="/dashboard"
                                className="rounded-md bg-[#B8860B] px-4 py-2 text-center text-sm font-medium text-white"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <Link
                                href="/login"
                                className="rounded-md border border-[#B8860B] px-4 py-2 text-center text-sm font-medium text-[#B8860B]"
                            >
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

export default function HomePage({ stats, villaTypes, towerDefinitions }: HomeProps) {
    const totalVillas = villaTypes.reduce((sum, t) => sum + (t.total_count || 0), 0) || stats.total_villas;
    const totalTowers = towerDefinitions.length;
    const totalApartments = stats.total_tower_units;

    return (
        <>
            <Head title="Mosul Boulevard | Premium Real Estate in Mosul, Iraq" />
            <div className="min-h-screen bg-[#1B1B1B] text-white">
                <SiteHeader />

                {/* Hero Section */}
                <section className="relative flex min-h-screen items-center justify-center overflow-hidden px-4">
                    <div className="absolute inset-0 bg-gradient-to-b from-[#1B1B1B] via-[#1B1B1B]/95 to-[#1B4F72]/20" />
                    <div className="relative z-10 mx-auto max-w-4xl text-center">
                        <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-[#B8860B]/30 bg-[#B8860B]/10 px-4 py-1.5">
                            <Landmark className="h-4 w-4 text-[#B8860B]" />
                            <span className="text-sm font-medium text-[#B8860B]">Premium Real Estate</span>
                        </div>
                        <h1 className="mb-6 text-5xl font-bold tracking-tight sm:text-6xl lg:text-7xl">
                            <span className="text-[#B8860B]">Mosul</span> Boulevard
                        </h1>
                        <p className="mb-4 text-xl text-gray-300 sm:text-2xl">
                            Premium Real Estate Development in Mosul, Iraq
                        </p>
                        <p className="mb-10 text-lg text-gray-400">
                            {totalVillas} Villas &bull; {totalTowers} Towers &bull; {totalApartments} Apartments
                        </p>
                        <div className="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                            <Link
                                href="/villas"
                                className="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#B8860B] px-8 py-3 text-base font-semibold text-white transition-colors hover:bg-[#B8860B]/80 sm:w-auto"
                            >
                                <Building2 className="h-5 w-5" />
                                Explore Villas
                            </Link>
                            <Link
                                href="/towers"
                                className="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-[#1B4F72] bg-[#1B4F72]/20 px-8 py-3 text-base font-semibold text-white transition-colors hover:bg-[#1B4F72]/40 sm:w-auto"
                            >
                                <Building className="h-5 w-5" />
                                View Towers
                            </Link>
                        </div>
                    </div>
                </section>

                {/* Stats Section */}
                <section className="border-y border-white/10 bg-[#1B1B1B]/80 py-16">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-2 gap-6 lg:grid-cols-4">
                            {[
                                { label: 'Total Villas', value: totalVillas, icon: Building2 },
                                { label: 'Towers', value: totalTowers, icon: Building },
                                { label: 'Apartments', value: totalApartments, icon: HomeIcon },
                                { label: 'Total Units', value: totalVillas + totalApartments, icon: Users },
                            ].map((stat) => (
                                <div
                                    key={stat.label}
                                    className="rounded-xl border border-white/10 bg-white/5 p-6 text-center"
                                >
                                    <stat.icon className="mx-auto mb-3 h-8 w-8 text-[#B8860B]" />
                                    <p className="text-3xl font-bold text-[#B8860B]">{stat.value.toLocaleString()}</p>
                                    <p className="mt-1 text-sm text-gray-400">{stat.label}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Property Types Section */}
                <section className="py-20">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <h2 className="mb-4 text-center text-3xl font-bold">Our Properties</h2>
                        <p className="mx-auto mb-12 max-w-2xl text-center text-gray-400">
                            Discover premium living spaces designed for modern families
                        </p>
                        <div className="grid gap-6 md:grid-cols-2">
                            {/* Villas Card */}
                            <Link
                                href="/villas"
                                className="group rounded-2xl border border-white/10 bg-white/5 p-8 transition-all hover:border-[#B8860B]/30 hover:bg-white/10"
                            >
                                <Building2 className="mb-4 h-10 w-10 text-[#B8860B]" />
                                <h3 className="mb-2 text-2xl font-bold">Villas</h3>
                                <p className="mb-6 text-gray-400">
                                    Spacious villas with modern architecture and premium finishes
                                </p>
                                <div className="space-y-2">
                                    {villaTypes.map((vt) => (
                                        <div
                                            key={vt.id}
                                            className="flex items-center justify-between rounded-lg bg-white/5 px-4 py-2"
                                        >
                                            <span className="text-sm text-gray-300">{vt.name}</span>
                                            <span className="text-sm font-semibold text-[#B8860B]">
                                                {vt.total_count} units
                                            </span>
                                        </div>
                                    ))}
                                </div>
                                <p className="mt-4 text-sm font-medium text-[#B8860B] transition-colors group-hover:text-white">
                                    Explore Villas &rarr;
                                </p>
                            </Link>

                            {/* Towers Card */}
                            <Link
                                href="/towers"
                                className="group rounded-2xl border border-white/10 bg-white/5 p-8 transition-all hover:border-[#1B4F72]/50 hover:bg-white/10"
                            >
                                <Building className="mb-4 h-10 w-10 text-[#1B4F72]" />
                                <h3 className="mb-2 text-2xl font-bold">Towers</h3>
                                <p className="mb-6 text-gray-400">
                                    Modern high-rise apartments with panoramic city views
                                </p>
                                <div className="space-y-2">
                                    {towerDefinitions.map((td) => (
                                        <div
                                            key={td.id}
                                            className="flex items-center justify-between rounded-lg bg-white/5 px-4 py-2"
                                        >
                                            <span className="text-sm text-gray-300">{td.name}</span>
                                            <span className="text-sm font-semibold text-[#1B4F72]">
                                                80 units
                                            </span>
                                        </div>
                                    ))}
                                </div>
                                <p className="mt-4 text-sm font-medium text-[#1B4F72] transition-colors group-hover:text-white">
                                    View Towers &rarr;
                                </p>
                            </Link>
                        </div>
                    </div>
                </section>

                <SiteFooter />
            </div>
        </>
    );
}
