'use client';

import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Plus, Search, Trash2, Loader2, Eye } from 'lucide-react';
import { auth, db } from '@/lib/firebase';
import { collection, addDoc, getDocs, deleteDoc, doc } from 'firebase/firestore';
import { uploadToCloudinary } from '@/lib/cloudinary';
import { ImageCropper } from '@/components/image-cropper';
import Image from 'next/image';
import { toast } from 'sonner';

interface Employee {
  id: string;
  name: string;
  divisi: string;
  imageUrl: string;
  email?: string;
  authUid?: string;
  hasAccount?: boolean;
  createdAt: any;
}

export default function BigDataKaryawanPage() {
  const [employees, setEmployees] = useState<Employee[]>([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [isOpen, setIsOpen] = useState(false);
  const [detailOpen, setDetailOpen] = useState(false);
  const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(null);
  const [accountOpen, setAccountOpen] = useState(false);
  const [selectedEmployeeForAccount, setSelectedEmployeeForAccount] = useState<Employee | null>(null);
  const [loading, setLoading] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [creatingAccount, setCreatingAccount] = useState(false);
  
  const [formData, setFormData] = useState({
    name: '',
    divisi: '',
  });

  const [accountForm, setAccountForm] = useState({
    email: '',
    password: '',
  });
  const [croppedImage, setCroppedImage] = useState<File | null>(null);
  const [imagePreview, setImagePreview] = useState('');

  useEffect(() => {
    fetchEmployees();
  }, []);

  const fetchEmployees = async () => {
    try {
      const querySnapshot = await getDocs(collection(db, 'employees'));
      const data = querySnapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      })) as Employee[];
      setEmployees(data);
    } catch (error) {
      console.error('Error fetching employees:', error);
      toast.error('Gagal memuat data karyawan');
    }
  };

  const handleImageCropped = (file: File) => {
    setCroppedImage(file);
    const reader = new FileReader();
    reader.onloadend = () => {
      setImagePreview(reader.result as string);
    };
    reader.readAsDataURL(file);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!croppedImage) {
      toast.error('Silakan upload foto terlebih dahulu');
      return;
    }

    setLoading(true);

    try {
      setUploading(true);
      const imageUrl = await uploadToCloudinary(croppedImage);
      setUploading(false);

      await addDoc(collection(db, 'employees'), {
        name: formData.name,
        divisi: formData.divisi,
        imageUrl,
        createdAt: new Date(),
      });

      toast.success('Karyawan berhasil ditambahkan!');
      setIsOpen(false);
      resetForm();
      fetchEmployees();
    } catch (error) {
      console.error('Error adding employee:', error);
      toast.error('Gagal menambahkan karyawan');
    } finally {
      setLoading(false);
      setUploading(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Yakin ingin menghapus karyawan ini?')) return;

    try {
      await deleteDoc(doc(db, 'employees', id));
      toast.success('Karyawan berhasil dihapus');
      fetchEmployees();
    } catch (error) {
      console.error('Error deleting employee:', error);
      toast.error('Gagal menghapus karyawan');
    }
  };

  const handleViewDetail = (emp: Employee) => {
    setSelectedEmployee(emp);
    setDetailOpen(true);
  };

  const handleOpenCreateAccount = (emp: Employee) => {
    setSelectedEmployeeForAccount(emp);
    setAccountForm({
      email: emp.email || '',
      password: '',
    });
    setAccountOpen(true);
  };

  const handleCreateAccount = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedEmployeeForAccount) return;

    if (!auth.currentUser) {
      toast.error('Anda harus login terlebih dahulu');
      return;
    }

    if (!accountForm.email || !accountForm.password) {
      toast.error('Email dan password wajib diisi');
      return;
    }

    setCreatingAccount(true);
    try {
      const token = await auth.currentUser.getIdToken();

      const res = await fetch('/api/employees/create-account', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify({
          employeeId: selectedEmployeeForAccount.id,
          email: accountForm.email,
          password: accountForm.password,
        }),
      });

      const data = await res.json();
      if (!res.ok) {
        toast.error(data?.error || 'Gagal membuat akun');
        return;
      }

      toast.success('Akun login APK berhasil dibuat!');
      setAccountOpen(false);
      setSelectedEmployeeForAccount(null);
      setAccountForm({ email: '', password: '' });
      fetchEmployees();
    } catch (error) {
      console.error('Error creating account:', error);
      toast.error('Gagal membuat akun');
    } finally {
      setCreatingAccount(false);
    }
  };

  const resetForm = () => {
    setFormData({ name: '', divisi: '' });
    setCroppedImage(null);
    setImagePreview('');
  };

  const filteredEmployees = employees.filter(emp =>
    emp.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    emp.divisi.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-white mb-2">Manage Karyawan</h1>
          <p className="text-gray-400">Kelola data karyawan</p>
        </div>
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
          <DialogTrigger asChild>
            <Button className="bg-red-500 hover:bg-red-600 text-white gap-2">
              <Plus size={16} />
              Tambah Karyawan
            </Button>
          </DialogTrigger>
          <DialogContent className="bg-black border-red-500/20">
            <DialogHeader>
              <DialogTitle className="text-white">Tambah Karyawan Baru</DialogTitle>
            </DialogHeader>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <Label className="text-white">Nama</Label>
                <Input
                  required
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="bg-black/20 border-red-500/30 text-white"
                  placeholder="Nama karyawan"
                />
              </div>
              <div>
                <Label className="text-white">Divisi</Label>
                <Select
                  required
                  value={formData.divisi}
                  onValueChange={(value) => setFormData({ ...formData, divisi: value })}
                >
                  <SelectTrigger className="bg-black/20 border-red-500/30 text-white">
                    <SelectValue placeholder="Pilih divisi" />
                  </SelectTrigger>
                  <SelectContent className="bg-black border-red-500/30">
                    <SelectItem value="Sports" className="text-white hover:bg-red-500/20">Sports</SelectItem>
                    <SelectItem value="Caffe" className="text-white hover:bg-red-500/20">Caffe</SelectItem>
                    <SelectItem value="Entertain" className="text-white hover:bg-red-500/20">Entertain</SelectItem>
                    <SelectItem value="GRO" className="text-white hover:bg-red-500/20">GRO</SelectItem>
                    <SelectItem value="HK" className="text-white hover:bg-red-500/20">HK</SelectItem>
                    <SelectItem value="Marketing" className="text-white hover:bg-red-500/20">Marketing</SelectItem>
                    <SelectItem value="Security" className="text-white hover:bg-red-500/20">Security</SelectItem>
                    <SelectItem value="Maintenance" className="text-white hover:bg-red-500/20">Maintenance</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label className="text-white">Foto</Label>
                <div className="mt-2">
                  {imagePreview ? (
                    <div className="space-y-2">
                      <div className="relative w-full h-48 rounded-lg overflow-hidden bg-gray-900">
                        <Image src={imagePreview} alt="Preview" fill className="object-contain" />
                      </div>
                      <Button
                        type="button"
                        variant="outline"
                        onClick={() => {
                          setCroppedImage(null);
                          setImagePreview('');
                        }}
                        className="w-full border-red-500/30 text-white hover:bg-red-500/20"
                      >
                        Ganti Foto
                      </Button>
                    </div>
                  ) : (
                    <ImageCropper onImageCropped={handleImageCropped} />
                  )}
                </div>
              </div>
              <Button
                type="submit"
                disabled={loading || uploading}
                className="w-full bg-red-500 hover:bg-red-600 text-white"
              >
                {uploading ? (
                  <><Loader2 className="w-4 h-4 mr-2 animate-spin" /> Uploading...</>
                ) : loading ? (
                  <><Loader2 className="w-4 h-4 mr-2 animate-spin" /> Menyimpan...</>
                ) : (
                  'Simpan'
                )}
              </Button>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <div className="flex items-center gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
              <Input
                placeholder="Cari karyawan..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10 bg-black/20 border-red-500/30 text-white"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {filteredEmployees.map((emp) => (
              <Card key={emp.id} className="bg-black/20 border-red-500/30">
                <CardContent className="pt-6">
                  <div className="flex flex-col items-center text-center space-y-3">
                    <div className="relative w-32 h-32 rounded-lg overflow-hidden bg-gray-700">
                      {emp.imageUrl ? (
                        <Image src={emp.imageUrl} alt={emp.name} fill className="object-contain" />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center text-gray-400">
                          <span className="text-3xl">{emp.name.charAt(0)}</span>
                        </div>
                      )}
                    </div>
                    <div>
                      <h3 className="font-semibold text-lg text-white">{emp.name}</h3>
                      <Badge className="mt-1 bg-red-500/20 text-red-400 border border-red-500/30">
                        {emp.divisi}
                      </Badge>
                      {emp.authUid ? (
                        <p className="text-xs text-green-400 mt-2">Akun APK: Aktif</p>
                      ) : (
                        <p className="text-xs text-gray-400 mt-2">Akun APK: Belum dibuat</p>
                      )}
                    </div>
                    <div className="flex gap-2 w-full">
                      <Button
                        size="sm"
                        variant="outline"
                        className="flex-1 border-red-500/30 text-white hover:bg-red-500/20"
                        onClick={() => handleViewDetail(emp)}
                      >
                        <Eye size={14} className="mr-1" />
                        Detail
                      </Button>
                      <Button
                        size="sm"
                        variant="outline"
                        className="flex-1 border-red-500/30 text-white hover:bg-red-500/20"
                        onClick={() => handleOpenCreateAccount(emp)}
                        disabled={!!emp.authUid}
                      >
                        Buat Akun
                      </Button>
                      <Button
                        size="sm"
                        variant="outline"
                        className="flex-1 border-red-500/30 text-red-400 hover:bg-red-500/20"
                        onClick={() => handleDelete(emp.id)}
                      >
                        <Trash2 size={14} className="mr-1" />
                        Hapus
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
          {filteredEmployees.length === 0 && (
            <div className="text-center py-12">
              <p className="text-gray-400">Tidak ada karyawan ditemukan</p>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Detail Dialog */}
      <Dialog open={detailOpen} onOpenChange={setDetailOpen}>
        <DialogContent className="bg-black border-red-500/20 max-w-md">
          <DialogHeader>
            <DialogTitle className="text-white">Detail Karyawan</DialogTitle>
          </DialogHeader>
          {selectedEmployee && (
            <div className="space-y-4">
              <div className="flex justify-center">
                <div className="relative w-48 h-48 rounded-lg overflow-hidden bg-gray-700">
                  {selectedEmployee.imageUrl ? (
                    <Image
                      src={selectedEmployee.imageUrl}
                      alt={selectedEmployee.name}
                      fill
                      className="object-contain"
                    />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center text-gray-400">
                      <span className="text-6xl">{selectedEmployee.name.charAt(0)}</span>
                    </div>
                  )}
                </div>
              </div>
              <div className="space-y-3">
                <div className="bg-black/40 p-4 rounded-lg border border-red-500/20">
                  <p className="text-sm text-gray-400">Nama Lengkap</p>
                  <p className="text-lg font-semibold text-white">{selectedEmployee.name}</p>
                </div>
                <div className="bg-black/40 p-4 rounded-lg border border-red-500/20">
                  <p className="text-sm text-gray-400">Divisi</p>
                  <p className="text-lg font-semibold text-white">{selectedEmployee.divisi}</p>
                </div>
                <div className="bg-black/40 p-4 rounded-lg border border-red-500/20">
                  <p className="text-sm text-gray-400">Tanggal Ditambahkan</p>
                  <p className="text-lg font-semibold text-white">
                    {selectedEmployee.createdAt?.toDate?.()?.toLocaleDateString('id-ID', {
                      day: 'numeric',
                      month: 'long',
                      year: 'numeric'
                    }) || 'N/A'}
                  </p>
                </div>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>

      {/* Create APK Account Dialog */}
      <Dialog open={accountOpen} onOpenChange={setAccountOpen}>
        <DialogContent className="bg-black border-red-500/20 max-w-md">
          <DialogHeader>
            <DialogTitle className="text-white">Buat Akun Login APK</DialogTitle>
          </DialogHeader>
          {selectedEmployeeForAccount && (
            <form onSubmit={handleCreateAccount} className="space-y-4">
              <div className="bg-black/40 p-4 rounded-lg border border-red-500/20">
                <p className="text-sm text-gray-400">Karyawan</p>
                <p className="text-lg font-semibold text-white">{selectedEmployeeForAccount.name}</p>
                <p className="text-xs text-gray-400 mt-1">Divisi: {selectedEmployeeForAccount.divisi}</p>
              </div>

              <div>
                <Label className="text-white">Email</Label>
                <Input
                  required
                  value={accountForm.email}
                  onChange={(e) => setAccountForm({ ...accountForm, email: e.target.value })}
                  className="bg-black/20 border-red-500/30 text-white"
                  placeholder="email karyawan"
                />
              </div>

              <div>
                <Label className="text-white">Password</Label>
                <Input
                  required
                  type="password"
                  value={accountForm.password}
                  onChange={(e) => setAccountForm({ ...accountForm, password: e.target.value })}
                  className="bg-black/20 border-red-500/30 text-white"
                  placeholder="password"
                />
              </div>

              <Button
                type="submit"
                disabled={creatingAccount}
                className="w-full bg-red-500 hover:bg-red-600 text-white"
              >
                {creatingAccount ? (
                  <><Loader2 className="w-4 h-4 mr-2 animate-spin" /> Membuat...</>
                ) : (
                  'Buat Akun'
                )}
              </Button>
            </form>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
