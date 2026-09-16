import { ReactNode } from 'react';
import { Sidebar } from './sidebar';

export function DashboardLayout({ children }: { children: ReactNode }) {
  return (
    <div className="flex">
      <Sidebar />
      <main className="pl-64 flex-1 md:ml-0 pt-16 md:pt-0 min-h-screen bg-background">
        <div className="p-4 md:p-8 max-w-7xl mx-auto">
          {children}
        </div>
      </main>
    </div>
  );
}
