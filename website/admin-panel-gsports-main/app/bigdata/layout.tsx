'use client';

import { useEffect } from 'react';
import { useRouter, usePathname } from 'next/navigation';
import Link from 'next/link';
import { BarChart3, Eye, Users, ClipboardCheck, Archive, ListChecks, LogOut, Star } from 'lucide-react';
import { Button } from '@/components/ui/button';

const bigDataMenuItems = [
  { href: '/bigdata/kpi', label: 'KPI Analytics', icon: BarChart3 },
  { href: '/bigdata/kpi-penilaian', label: 'Rekap KPI', icon: ListChecks },
  { href: '/bigdata/monitoring', label: 'Monitoring', icon: Eye },
  { href: '/bigdata/karyawan', label: 'Manage Karyawan', icon: Users },
  { href: '/bigdata/absen', label: 'Manage Absen', icon: ClipboardCheck },
  { href: '/bigdata/absen-arsip', label: 'Arsip Absen', icon: Archive },
  { href: '/bigdata/bookings-arsip', label: 'Arsip Booking', icon: Archive },
  { href: '/bigdata/ulasan', label: 'Ulasan', icon: Star },
];

export default function BigDataLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();

  useEffect(() => {
    const hasAccess = sessionStorage.getItem('bigdata_access');
    if (!hasAccess && pathname !== '/bigdata') {
      router.push('/bigdata');
    }
  }, [pathname, router]);

  const handleLogout = () => {
    sessionStorage.removeItem('bigdata_access');
    router.push('/dashboard');
  };

  if (pathname === '/bigdata') {
    return <>{children}</>;
  }

  return (
    <div className="flex h-screen bg-black">
      {/* Sidebar */}
      <aside className="w-64 bg-black border-r border-red-500/20">
        <div className="p-6 border-b border-red-500/20">
          <h1 className="text-xl font-bold text-white">Big Data</h1>
          <p className="text-xs text-gray-400">Advanced Analytics</p>
        </div>

        <nav className="p-4 space-y-2">
          {bigDataMenuItems.map((item) => {
            const Icon = item.icon;
            const isActive = pathname === item.href;
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`flex items-center gap-3 px-4 py-3 rounded-lg transition ${
                  isActive
                    ? 'bg-red-500 text-white'
                    : 'text-gray-400 hover:bg-red-500/20 hover:text-white'
                }`}
              >
                <Icon size={20} />
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>

        <div className="absolute bottom-0 w-64 p-4 border-t border-red-500/20">
          <Button
            variant="outline"
            className="w-full justify-start gap-2 bg-transparent border-red-500/30 text-white hover:bg-red-500/20"
            onClick={handleLogout}
          >
            <LogOut size={16} />
            Keluar Big Data
          </Button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-auto">
        <div className="p-8">{children}</div>
      </main>
    </div>
  );
}
