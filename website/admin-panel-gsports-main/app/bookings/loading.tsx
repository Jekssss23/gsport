import { Loader2 } from 'lucide-react';
import { DashboardLayout } from '@/components/dashboard-layout';

export default function Loading() {
  return (
    <DashboardLayout>
      <div className="space-y-6">
        <div className="h-10 bg-muted rounded-lg w-1/3" />
        <div className="h-6 bg-muted rounded-lg w-1/4" />

        <div className="border rounded-lg p-6">
          <div className="h-8 bg-muted rounded-lg w-1/4 mb-4" />
          <div className="space-y-2">
            <div className="h-10 bg-muted rounded-lg" />
            <div className="h-10 bg-muted rounded-lg" />
            <div className="h-10 bg-muted rounded-lg" />
          </div>
        </div>

        <div className="flex items-center justify-center py-12">
          <Loader2 className="h-8 w-8 animate-spin text-primary" />
        </div>
      </div>
    </DashboardLayout>
  );
}
