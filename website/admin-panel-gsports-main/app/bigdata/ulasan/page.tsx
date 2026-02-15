'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Search, Trash2 } from 'lucide-react';
import { db } from '@/lib/firebase';
import {
  collection,
  deleteDoc,
  doc,
  getDocs,
  orderBy,
  query,
} from 'firebase/firestore';
import { toast } from 'sonner';

interface Review {
  id: string;
  userName: string;
  date: string;
  rating: 'angry' | 'neutral' | 'happy' | 'very_happy' | 'love' | number;
  feedback: string;
  facilityName?: string;
  bookingDate?: string;
  createdAt?: string;
  ratingType?: 'emote' | 'star';
}

export default function BigDataUlasanPage() {
  const [items, setItems] = useState<Review[]>([]);
  const [loading, setLoading] = useState(false);

  const [searchTerm, setSearchTerm] = useState('');
  const [filterRating, setFilterRating] = useState('all');

  const fetchReviews = useCallback(async () => {
    setLoading(true);
    try {
      const colRef = collection(db, 'reviews');
      const q = query(colRef, orderBy('createdAt', 'desc'));
      const snap = await getDocs(q);
      const data = snap.docs.map((d) => ({ ...(d.data() as any), id: d.id })) as Review[];
      setItems(data);
    } catch (e) {
      console.error('Fetch reviews error:', e);
      toast.error('Gagal mengambil data ulasan');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchReviews();
  }, [fetchReviews]);

  const filtered = useMemo(() => {
    return items.filter((review) => {
      const matchSearch = (review.userName || '').toLowerCase().includes(searchTerm.toLowerCase());
      const matchRating = filterRating === 'all' || review.rating === filterRating;
      return matchSearch && matchRating;
    });
  }, [filterRating, items, searchTerm]);

  const getRatingEmote = (rating: string | number) => {
    if (typeof rating === 'number') {
      // Convert numeric rating (1-5) to stars
      const stars = '⭐'.repeat(rating) + '☆'.repeat(5 - rating);
      return stars;
    }
    
    switch (rating) {
      case 'angry': return '😡';
      case 'neutral': return '😑';
      case 'happy': return '☺️';
      case 'very_happy': return '😁';
      case 'love': return '😍';
      default: return rating;
    }
  };

  const getRatingText = (rating: string | number) => {
    if (typeof rating === 'number') {
      switch (rating) {
        case 1: return 'Sangat Buruk';
        case 2: return 'Buruk';
        case 3: return 'Cukup Baik';
        case 4: return 'Baik';
        case 5: return 'Sangat Baik';
        default: return `${rating} bintang`;
      }
    }
    
    switch (rating) {
      case 'angry': return 'Sangat Buruk';
      case 'neutral': return 'Cukup Baik';
      case 'happy': return 'Baik';
      case 'very_happy': return 'Sangat Baik';
      case 'love': return 'Luar Biasa';
      default: return rating;
    }
  };

  const deleteOne = async (id: string) => {
    if (!confirm('Hapus ulasan ini secara permanen?')) return;
    try {
      await deleteDoc(doc(db, 'reviews', id));
      toast.success('Ulasan berhasil dihapus');
      setItems((prev) => prev.filter((x) => x.id !== id));
    } catch (e) {
      console.error('Delete review error:', e);
      toast.error('Gagal menghapus ulasan');
    }
  };

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-white mb-2">Ulasan</h1>
          <p className="text-gray-400">Lihat ulasan dari pengguna</p>
        </div>
      </div>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
              <Input
                placeholder="Cari user..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10 bg-black/20 border-red-500/30 text-white"
              />
            </div>
            <Select value={filterRating} onValueChange={setFilterRating}>
              <SelectTrigger className="w-full md:w-48 bg-black/20 border-red-500/30 text-white">
                <SelectValue />
              </SelectTrigger>
              <SelectContent className="bg-black border-red-500/30">
                <SelectItem value="all" className="text-white hover:bg-red-500/20">Semua Rating</SelectItem>
                <SelectItem value="love" className="text-white hover:bg-red-500/20">😍 Luar Biasa</SelectItem>
                <SelectItem value="very_happy" className="text-white hover:bg-red-500/20">😁 Sangat Baik</SelectItem>
                <SelectItem value="happy" className="text-white hover:bg-red-500/20">☺️ Baik</SelectItem>
                <SelectItem value="neutral" className="text-white hover:bg-red-500/20">😑 Cukup Baik</SelectItem>
                <SelectItem value="angry" className="text-white hover:bg-red-500/20">😡 Sangat Buruk</SelectItem>
                <SelectItem value="5" className="text-white hover:bg-red-500/20">⭐⭐⭐⭐⭐ Sangat Baik</SelectItem>
                <SelectItem value="4" className="text-white hover:bg-red-500/20">⭐⭐⭐⭐ Baik</SelectItem>
                <SelectItem value="3" className="text-white hover:bg-red-500/20">⭐⭐⭐ Cukup Baik</SelectItem>
                <SelectItem value="2" className="text-white hover:bg-red-500/20">⭐⭐ Buruk</SelectItem>
                <SelectItem value="1" className="text-white hover:bg-red-500/20">⭐ Sangat Buruk</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardHeader>
        <CardContent>
          <div className="flex items-center justify-between mb-3">
            <div className="text-xs text-gray-400">
              {loading ? 'Memuat...' : `Total: ${filtered.length} ulasan`}
            </div>
            <Button
              variant="outline"
              className="border-red-500/30 text-white hover:bg-red-500/20"
              onClick={fetchReviews}
              disabled={loading}
            >
              Refresh
            </Button>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-red-500/20">
                  <th className="text-left py-3 px-4 font-semibold text-white">Nama User</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Fasilitas</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Tanggal Booking</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Rating</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Ulasan</th>
                  <th className="text-right py-3 px-4 font-semibold text-white">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {filtered.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="text-center py-12 text-gray-400">
                      Belum ada ulasan
                    </td>
                  </tr>
                ) : (
                  filtered.map((review) => (
                    <tr key={review.id} className="border-b border-red-500/10 hover:bg-red-500/5">
                      <td className="py-4 px-4">
                        <p className="font-medium text-white">{review.userName}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{review.facilityName || '-'}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{review.bookingDate || review.date || '-'}</p>
                      </td>
                      <td className="py-4 px-4">
                        <div className="flex flex-col">
                          <p className="text-2xl">{getRatingEmote(review.rating)}</p>
                          <p className="text-xs text-gray-400">{getRatingText(review.rating)}</p>
                        </div>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{review.feedback || review.review || '-'}</p>
                      </td>
                      <td className="py-4 px-4 text-right">
                        <Button
                          variant="outline"
                          className="border-red-500/30 text-white hover:bg-red-500/20 gap-2"
                          onClick={() => deleteOne(review.id)}
                        >
                          <Trash2 size={16} />
                          Hapus
                        </Button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
