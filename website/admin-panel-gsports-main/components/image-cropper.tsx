'use client';

import { useState, useRef } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Slider } from '@/components/ui/slider';
import { Upload, ZoomIn, ZoomOut } from 'lucide-react';
import Image from 'next/image';

interface ImageCropperProps {
  onImageCropped: (file: File) => void;
}

export function ImageCropper({ onImageCropped }: ImageCropperProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [imageSrc, setImageSrc] = useState('');
  const [zoom, setZoom] = useState(1);
  const fileInputRef = useRef<HTMLInputElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const imageRef = useRef<HTMLImageElement>(null);

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = () => {
        setImageSrc(reader.result as string);
        setIsOpen(true);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleCrop = async () => {
    if (!imageRef.current || !canvasRef.current) return;

    const canvas = canvasRef.current;
    const image = imageRef.current;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const size = 400;
    canvas.width = size;
    canvas.height = size;

    const scale = zoom;
    const scaledWidth = image.naturalWidth * scale;
    const scaledHeight = image.naturalHeight * scale;
    const x = (size - scaledWidth) / 2;
    const y = (size - scaledHeight) / 2;

    ctx.fillStyle = '#000';
    ctx.fillRect(0, 0, size, size);
    ctx.drawImage(image, x, y, scaledWidth, scaledHeight);

    canvas.toBlob((blob) => {
      if (blob) {
        const file = new File([blob], 'employee-photo.jpg', { type: 'image/jpeg' });
        onImageCropped(file);
        setIsOpen(false);
        setImageSrc('');
        setZoom(1);
      }
    }, 'image/jpeg', 0.9);
  };

  return (
    <>
      <input
        ref={fileInputRef}
        type="file"
        className="hidden"
        accept="image/*"
        onChange={handleFileSelect}
      />
      
      <label
        onClick={() => fileInputRef.current?.click()}
        className="flex flex-col items-center justify-center w-full h-32 border-2 border-red-500/30 border-dashed rounded-lg cursor-pointer hover:bg-red-500/5"
      >
        <Upload className="w-8 h-8 mb-2 text-gray-400" />
        <p className="text-sm text-gray-400">Klik untuk upload & crop foto</p>
      </label>

      <Dialog open={isOpen} onOpenChange={setIsOpen}>
        <DialogContent className="bg-black border-red-500/20 max-w-md">
          <DialogHeader>
            <DialogTitle className="text-white">Crop Foto</DialogTitle>
          </DialogHeader>
          <div className="space-y-4">
            <div className="relative w-full h-64 bg-black rounded-lg overflow-hidden flex items-center justify-center">
              {imageSrc && (
                <img
                  ref={imageRef}
                  src={imageSrc}
                  alt="Crop preview"
                  style={{
                    transform: `scale(${zoom})`,
                    maxWidth: '100%',
                    maxHeight: '100%',
                    objectFit: 'contain',
                  }}
                />
              )}
            </div>
            <div className="space-y-2">
              <div className="flex items-center gap-2">
                <ZoomOut className="w-4 h-4 text-gray-400" />
                <Slider
                  value={[zoom]}
                  onValueChange={(value) => setZoom(value[0])}
                  min={0.5}
                  max={3}
                  step={0.1}
                  className="flex-1"
                />
                <ZoomIn className="w-4 h-4 text-gray-400" />
              </div>
              <p className="text-xs text-gray-400 text-center">Zoom: {zoom.toFixed(1)}x</p>
            </div>
            <div className="flex gap-2">
              <Button
                variant="outline"
                onClick={() => setIsOpen(false)}
                className="flex-1 border-red-500/30 text-white hover:bg-red-500/20"
              >
                Batal
              </Button>
              <Button
                onClick={handleCrop}
                className="flex-1 bg-red-500 hover:bg-red-600 text-white"
              >
                Crop & Gunakan
              </Button>
            </div>
          </div>
          <canvas ref={canvasRef} className="hidden" />
        </DialogContent>
      </Dialog>
    </>
  );
}
