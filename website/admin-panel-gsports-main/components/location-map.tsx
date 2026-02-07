'use client';

import { Button } from '@/components/ui/button';
import { MapPin, ExternalLink, Copy } from 'lucide-react';
import { toast } from 'sonner';

interface LocationMapProps {
  latitude: number;
  longitude: number;
  radius: number;
  onLocationSelect: (lat: number, lng: number) => void;
}

export function LocationMap({ latitude, longitude, radius, onLocationSelect }: LocationMapProps) {
  const copyCoordinates = () => {
    navigator.clipboard.writeText(`${latitude}, ${longitude}`);
    toast.success('Koordinat berhasil dicopy!');
  };

  const openGoogleMaps = () => {
    const url = `https://www.google.com/maps/@${latitude},${longitude},17z`;
    window.open(url, '_blank');
  };

  const getCurrentLocation = () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          onLocationSelect(position.coords.latitude, position.coords.longitude);
          toast.success('Lokasi berhasil diambil!');
        },
        (error) => {
          toast.error('Gagal mengambil lokasi');
        }
      );
    } else {
      toast.error('Browser tidak mendukung geolocation');
    }
  };

  return (
    <div className="space-y-4">
      {/* Map Preview */}
      <div className="relative rounded-lg overflow-hidden border-2 border-red-500/30">
        <iframe
          width="100%"
          height="400"
          frameBorder="0"
          style={{ border: 0 }}
          src={`https://www.google.com/maps?q=${latitude},${longitude}&z=17&output=embed`}
          allowFullScreen
        />
        <div className="absolute top-2 left-2 bg-black/90 text-white px-3 py-2 rounded-lg text-xs z-10">
          <p className="font-semibold">📍 Lokasi Absensi</p>
          <p className="text-gray-300 mt-1">Radius: {radius}m</p>
        </div>
      </div>

      {/* Coordinate Display */}
      <div className="grid grid-cols-2 gap-4">
        <div className="bg-black/40 p-3 rounded-lg border border-red-500/20">
          <p className="text-xs text-gray-400 mb-1">Latitude</p>
          <p className="text-sm font-mono text-white">{latitude.toFixed(6)}</p>
        </div>
        <div className="bg-black/40 p-3 rounded-lg border border-red-500/20">
          <p className="text-xs text-gray-400 mb-1">Longitude</p>
          <p className="text-sm font-mono text-white">{longitude.toFixed(6)}</p>
        </div>
      </div>

      {/* Action Buttons */}
      <div className="space-y-2">
        <Button
          type="button"
          variant="outline"
          onClick={getCurrentLocation}
          className="w-full border-red-500/30 text-white hover:bg-red-500/20"
        >
          <MapPin size={16} className="mr-2" />
          Gunakan Lokasi Saya Saat Ini
        </Button>
        
        <div className="grid grid-cols-2 gap-2">
          <Button
            type="button"
            variant="outline"
            onClick={openGoogleMaps}
            className="border-red-500/30 text-white hover:bg-red-500/20"
          >
            <ExternalLink size={16} className="mr-2" />
            Buka Google Maps
          </Button>
          <Button
            type="button"
            variant="outline"
            onClick={copyCoordinates}
            className="border-red-500/30 text-white hover:bg-red-500/20"
          >
            <Copy size={16} className="mr-2" />
            Copy Koordinat
          </Button>
        </div>
      </div>

      {/* Instructions */}
      <div className="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
        <p className="text-sm text-blue-400 font-semibold mb-2">💡 Cara Mendapatkan Koordinat:</p>
        <ol className="text-xs text-blue-300 space-y-1 list-decimal list-inside">
          <li>Klik "Buka Google Maps" di atas</li>
          <li>Cari dan klik lokasi yang diinginkan di Google Maps</li>
          <li>Koordinat akan muncul di bawah (contoh: -6.200000, 106.816666)</li>
          <li>Copy koordinat tersebut</li>
          <li>Paste ke input Latitude dan Longitude di atas</li>
        </ol>
      </div>
    </div>
  );
}
