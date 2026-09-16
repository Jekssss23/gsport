'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Lock } from 'lucide-react';

const CORRECT_PIN = '67854645';

export default function BigDataPinPage() {
  const [pin, setPin] = useState('');
  const [error, setError] = useState('');
  const router = useRouter();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (pin === CORRECT_PIN) {
      sessionStorage.setItem('bigdata_access', 'true');
      router.push('/bigdata/kpi');
    } else {
      setError('PIN salah!');
      setPin('');
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-black">
      <Card className="w-full max-w-md bg-black/40 border-red-500/20">
        <CardHeader className="text-center">
          <div className="flex justify-center mb-4">
            <div className="p-4 bg-red-500/20 rounded-full">
              <Lock className="w-12 h-12 text-red-500" />
            </div>
          </div>
          <CardTitle className="text-2xl text-white">Big Data Access</CardTitle>
          <p className="text-gray-400 text-sm">Masukkan PIN untuk mengakses</p>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <Input
              type="password"
              placeholder="Masukkan PIN"
              value={pin}
              onChange={(e) => {
                setPin(e.target.value);
                setError('');
              }}
              className="bg-black/20 border-red-500/30 text-white text-center text-2xl tracking-widest"
              maxLength={8}
            />
            {error && <p className="text-red-500 text-sm text-center">{error}</p>}
            <Button type="submit" className="w-full bg-red-500 hover:bg-red-600 text-white">
              Akses
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
