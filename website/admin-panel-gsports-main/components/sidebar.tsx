'use client';

import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { useAuth } from '@/lib/auth-context';
import {
  LayoutDashboard,
  Calendar,
  CheckSquare,
  Database,
  LogOut,
  Menu,
  X,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';

const adminMenuItems = [
  { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { href: '/bookings', label: 'Bookings', icon: CheckSquare },
  { href: '/classes', label: 'Classes', icon: Calendar },
  { href: '/bigdata', label: 'Big Data', icon: Database },
];

const instructorMenuItems = [
  { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { href: '/classes', label: 'My Classes', icon: Calendar },
];

export function Sidebar() {
  const pathname = usePathname();
  const { userData, logout } = useAuth();
  const [isOpen, setIsOpen] = useState(false);

  const menuItems = userData?.role === 'admin' ? adminMenuItems : instructorMenuItems;

  const handleLogout = async () => {
    await logout();
  };

  return (
    <>
      {/* Mobile Menu Toggle */}
      <button
        className="md:hidden fixed top-4 left-4 z-50 p-2 bg-sidebar text-sidebar-foreground rounded-lg"
        onClick={() => setIsOpen(!isOpen)}
      >
        {isOpen ? <X size={24} /> : <Menu size={24} />}
      </button>

      {/* Sidebar */}
      <aside
        className={`fixed top-0 left-0 h-screen w-64 bg-sidebar text-sidebar-foreground border-r border-sidebar-border transition-transform duration-300 z-40 ${
          isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
        }`}
      >
        <div className="p-6 border-b border-sidebar-border">
          <div className="flex items-center gap-3">
            <div className="relative w-24 h-24 flex-shrink-0">
              <Image
                src="/logogsc.png"
                alt="G Sports Center Logo"
                width={180}
                height={180}
                className="object-contain"
              />
            </div>
            <div>
             
              
            </div>
          </div>
        </div>

        <nav className="flex-1 p-4 space-y-2">
          {menuItems.map((item) => {
            const Icon = item.icon;
            const isActive = pathname === item.href || pathname.startsWith(item.href + '/');
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`flex items-center gap-3 px-4 py-3 rounded-lg transition ${
                  isActive
                    ? 'bg-sidebar-primary text-sidebar-primary-foreground'
                    : 'text-sidebar-foreground hover:bg-sidebar-accent'
                }`}
                onClick={() => setIsOpen(false)}
              >
                <Icon size={20} />
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>

        <div className="p-4 border-t border-sidebar-border space-y-2">
          <div className="px-4 py-2 text-sm">
            <p className="">Logged in as</p>
            <p className="font-semibold truncate">{userData?.name}</p>
            <p className="text-xs ">{userData?.email}</p>
          </div>
          <Button
            variant="outline"
            className="w-full justify-start gap-2 bg-transparent"
            onClick={handleLogout}
          >
            <LogOut size={16} />
            Logout
          </Button>
        </div>
      </aside>

      {/* Mobile Overlay */}
      {isOpen && (
        <div
          className="fixed inset-0 bg-black/50 md:hidden z-30"
          onClick={() => setIsOpen(false)}
        />
      )}
    </>
  );
}
