'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { Loader2 } from 'lucide-react';

export default function KPIPage() {
  const router = useRouter();

  useEffect(() => {
    router.push('/bigdata');
  }, [router]);

  return (
    <div className="flex items-center justify-center h-screen bg-black">
      <div className="flex flex-col items-center gap-4">
        <Loader2 className="w-12 h-12 text-red-500 animate-spin" />
        <p className="text-gray-400">Redirecting to Big Data...</p>
      </div>
    </div>
  );
}
