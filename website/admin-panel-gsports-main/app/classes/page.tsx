'use client';

import { useEffect, useState } from 'react';
import { ProtectedRoute } from '@/components/protected-route';
import { DashboardLayout } from '@/components/dashboard-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import { Loader2, Plus, Trash2 } from 'lucide-react';
import { db } from '@/lib/firebase';
import { collection, getDocs, addDoc, deleteDoc, doc, updateDoc } from 'firebase/firestore';
import { useAuth } from '@/lib/auth-context';
import { useConfirmDialog } from '@/components/confirm-dialog';
import { uploadToCloudinary } from '@/lib/cloudinary';
import { toast } from 'sonner';
import { Checkbox } from '@/components/ui/checkbox';

interface Member {
  id: string;
  name: string;
  age: number;
  gender: 'Laki-laki' | 'Perempuan';
}

interface Class {
  id: string;
  category: string; // 'Les renang' | 'Karate' | 'Aikido' | 'Silat' | 'Taekwondo' | 'Akademi Futsal'
  name: string;
  coachName: string;
  coachImage?: string;
  days: string[];
  startTime: string;
  endTime: string;
  capacity: number;
  enrolled: number;
  status: 'active' | 'inactive';
}

export default function ClassesPage() {
  const { userData } = useAuth();
  const { confirm, dialog } = useConfirmDialog();
  const [classes, setClasses] = useState<Class[]>([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [formData, setFormData] = useState({
    category: '',
    name: '',
    coachName: '',
    coachImage: '',
    days: [] as string[],
    startTime: '',
    endTime: '',
    capacity: '30',
  });
  const [isUploading, setIsUploading] = useState(false);

  const [selectedClassForMembers, setSelectedClassForMembers] = useState<Class | null>(null);
  const [members, setMembers] = useState<Member[]>([]);
  const [memberLoading, setMemberLoading] = useState(false);
  const [memberFormData, setMemberFormData] = useState({
    name: '',
    age: '',
    gender: 'Laki-laki' as 'Laki-laki' | 'Perempuan',
  });

  useEffect(() => {
    const fetchClasses = async () => {
      try {
        const q = collection(db, 'classes');
        const querySnapshot = await getDocs(q);
        const classesData = querySnapshot.docs.map((doc) => ({
          id: doc.id,
          ...doc.data(),
        })) as Class[];
        setClasses(classesData);
      } catch (error) {
        console.error('Error fetching classes:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchClasses();
  }, []);

  const fetchMembers = async (classId: string) => {
    setMemberLoading(true);
    try {
      const snap = await getDocs(collection(db, 'classes', classId, 'members'));
      const data = snap.docs.map(d => ({ id: d.id, ...d.data() })) as Member[];
      setMembers(data);
    } catch (e) {
      console.error('Error fetching members:', e);
    } finally {
      setMemberLoading(false);
    }
  };

  useEffect(() => {
    if (selectedClassForMembers) {
      fetchMembers(selectedClassForMembers.id);
    }
  }, [selectedClassForMembers]);

  const handleCreateClass = async () => {
    if (
      !formData.category ||
      !formData.name ||
      !formData.coachName ||
      formData.days.length === 0 ||
      !formData.startTime ||
      !formData.endTime
    ) {
      alert('Please fill all fields');
      return;
    }

    try {
      const docRef = await addDoc(collection(db, 'classes'), {
        ...formData,
        capacity: parseInt(formData.capacity),
        enrolled: 0,
        status: 'active',
      });

      setClasses([
        ...classes,
        {
          id: docRef.id,
          ...formData,
          capacity: parseInt(formData.capacity),
          enrolled: 0,
          status: 'active',
        },
      ]);

      setFormData({
        category: '',
        name: '',
        coachName: '',
        coachImage: '',
        days: [],
        startTime: '',
        endTime: '',
        capacity: '30',
      });
      setOpen(false);
    } catch (error) {
      console.error('Error creating class:', error);
      alert('Failed to create class');
    }
  };

  const handleDeleteClass = async (classId: string) => {
    const confirmed = await confirm({
      title: 'Delete Class',
      description: 'Are you sure you want to delete this class? This action cannot be undone.',
      actionText: 'Delete',
      cancelText: 'Cancel',
      actionVariant: 'destructive',
    });

    if (!confirmed) return;

    try {
      await deleteDoc(doc(db, 'classes', classId));
      setClasses(classes.filter((c) => c.id !== classId));
    } catch (error) {
      console.error('Error deleting class:', error);
      alert('Failed to delete class');
    }
  };

  const categories = [
    'Les renang',
    'Karate',
    'Aikido',
    'Silat',
    'Taekwondo',
    'Akademi Futsal'
  ];

  const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
  const handleCreateMember = async () => {
    const currentClass = selectedClassForMembers;
    if (!currentClass || !memberFormData.name || !memberFormData.age) return;
    try {
      const docRef = await addDoc(collection(db, 'classes', currentClass.id, 'members'), {
        name: memberFormData.name,
        age: parseInt(memberFormData.age),
        gender: memberFormData.gender,
        createdAt: new Date().toISOString()
      });

      // Update local state
      const newMember: Member = {
        id: docRef.id,
        name: memberFormData.name,
        age: parseInt(memberFormData.age),
        gender: memberFormData.gender as 'Laki-laki' | 'Perempuan'
      };
      setMembers([...members, newMember]);
      setMemberFormData({ name: '', age: '', gender: 'Laki-laki' });

      // Update enrolled count in class
      const newEnrolled = currentClass.enrolled + 1;
      await updateDoc(doc(db, 'classes', currentClass.id), {
        enrolled: newEnrolled
      });

      await addDoc(collection(db, 'history'), {
        type: 'add_member',
        className: currentClass.name,
        memberName: memberFormData.name,
        timestamp: new Date().toISOString()
      });
      // For simplicity update local classes too
      setClasses(prev => prev.map(c => c.id === currentClass.id ? { ...c, enrolled: newEnrolled } : c));
      setSelectedClassForMembers({ ...currentClass, enrolled: newEnrolled });

    } catch (err) {
      console.error(err);
      alert('Gagal menambah anggota');
    }
  };

  const handleDeleteMember = async (memberId: string) => {
    const currentClass = selectedClassForMembers;
    if (!currentClass) return;
    const confirmed = await confirm({
      title: 'Hapus Anggota',
      description: 'Apakah Anda yakin ingin menghapus anggota ini?',
      actionText: 'Hapus',
      cancelText: 'Batal',
      actionVariant: 'destructive',
    });
    if (!confirmed) return;
    try {
      await deleteDoc(doc(db, 'classes', currentClass.id, 'members', memberId));
      setMembers(prev => prev.filter(m => m.id !== memberId));

      const newEnrolled = Math.max(0, currentClass.enrolled - 1);
      await updateDoc(doc(db, 'classes', currentClass.id), {
        enrolled: newEnrolled
      });

      setClasses(prev => prev.map(c => c.id === currentClass.id ? { ...c, enrolled: newEnrolled } : c));
      setSelectedClassForMembers({ ...currentClass, enrolled: newEnrolled });
    } catch (err) {
      console.error(err);
    }
  };

  return (
    <ProtectedRoute>
      <DashboardLayout>
        {dialog}
        <div className="space-y-6">
          <div className="flex items-center justify-end">
            {userData?.role === 'admin' && (
              <Dialog open={open} onOpenChange={setOpen}>
                <DialogTrigger asChild>
                  <Button className="gap-2 bg-red-600 hover:bg-red-700 text-white">
                    <Plus size={16} />
                    New Class
                  </Button>
                </DialogTrigger>
                <DialogContent className="bg-black border-red-500/30">
                  <DialogHeader>
                    <DialogTitle className="text-white">Create New Class</DialogTitle>
                  </DialogHeader>
                  <div className="space-y-4">
                    <div>
                      <label className="text-sm font-medium text-white">Kategori</label>
                      <Select
                        value={formData.category}
                        onValueChange={(value) =>
                          setFormData({ ...formData, category: value })
                        }
                      >
                        <SelectTrigger className="bg-black/20 border-red-500/30 text-white">
                          <SelectValue placeholder="Pilih Kategori" />
                        </SelectTrigger>
                        <SelectContent className="bg-black border-red-500/30">
                          {categories.map((cat) => (
                            <SelectItem key={cat} value={cat} className="text-white hover:bg-red-500/20">
                              {cat}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>

                    <div>
                      <label className="text-sm font-medium text-white">Nama Kelas</label>
                      <Input
                        placeholder="Contoh: Pemula 1"
                        value={formData.name}
                        onChange={(e) =>
                          setFormData({ ...formData, name: e.target.value })
                        }
                        className="bg-black/20 border-red-500/30 text-white placeholder:text-gray-400"
                      />
                    </div>

                    <div>
                      <label className="text-sm font-medium text-white">Nama Coach</label>
                      <Input
                        placeholder="Nama Pelatih"
                        value={formData.coachName}
                        onChange={(e) =>
                          setFormData({ ...formData, coachName: e.target.value })
                        }
                        className="bg-black/20 border-red-500/30 text-white placeholder:text-gray-400"
                      />
                    </div>

                    <div>
                      <label className="text-sm font-medium text-white block mb-2">Foto Coach (Upload)</label>
                      <div className="flex items-center gap-4">
                        {formData.coachImage && (
                          <img src={formData.coachImage} alt="Preview" className="w-12 h-12 rounded-full object-cover border border-red-500/30" />
                        )}
                        <Input
                          type="file"
                          accept="image/*"
                          onChange={async (e) => {
                            const file = e.target.files?.[0];
                            if (file) {
                              setIsUploading(true);
                              try {
                                const url = await uploadToCloudinary(file);
                                setFormData({ ...formData, coachImage: url });
                                toast.success('Gambar berhasil diunggah!');
                              } catch (e) {
                                console.error(e);
                                toast.error('Gagal mengunggah gambar');
                              } finally {
                                setIsUploading(false);
                              }
                            }
                          }}
                          className="bg-black/20 border-red-500/30 text-white cursor-pointer"
                          disabled={isUploading}
                        />
                      </div>
                      {isUploading && <p className="text-xs text-red-500 mt-1 animate-pulse">Sedang mengunggah...</p>}
                    </div>

                    <div>
                      <label className="text-sm font-medium text-white block mb-2">Days (Bisa Pilih Banyak)</label>
                      <div className="grid grid-cols-2 gap-2 bg-black/20 p-3 rounded-lg border border-red-500/30">
                        {days.map((day) => (
                          <div key={day} className="flex items-center space-x-2">
                            <Checkbox
                              id={`day-${day}`}
                              checked={formData.days.includes(day)}
                              onCheckedChange={(checked) => {
                                const newDays = checked
                                  ? [...formData.days, day]
                                  : formData.days.filter(d => d !== day);
                                setFormData({ ...formData, days: newDays });
                              }}
                              className="border-red-500"
                            />
                            <label htmlFor={`day-${day}`} className="text-sm text-white cursor-pointer hover:text-red-400">
                              {day}
                            </label>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="text-sm font-medium text-white">Start Time</label>
                        <Input
                          type="time"
                          value={formData.startTime}
                          onChange={(e) =>
                            setFormData({ ...formData, startTime: e.target.value })
                          }
                          className="bg-black/20 border-red-500/30 text-white"
                        />
                      </div>
                      <div>
                        <label className="text-sm font-medium text-white">End Time</label>
                        <Input
                          type="time"
                          value={formData.endTime}
                          onChange={(e) =>
                            setFormData({ ...formData, endTime: e.target.value })
                          }
                          className="bg-black/20 border-red-500/30 text-white"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="text-sm font-medium text-white">Capacity</label>
                      <Input
                        type="number"
                        value={formData.capacity}
                        onChange={(e) =>
                          setFormData({ ...formData, capacity: e.target.value })
                        }
                        className="bg-black/20 border-red-500/30 text-white"
                      />
                    </div>

                    <Button onClick={handleCreateClass} className="w-full bg-red-600 hover:bg-red-700 text-white">
                      Create Class
                    </Button>
                  </div>
                </DialogContent>
              </Dialog>
            )}
          </div>

          <Card className="bg-black/40 border-red-500/20">
            <CardHeader>
              <CardTitle className="text-white">Classes Schedule</CardTitle>
            </CardHeader>
            <CardContent>
              {loading ? (
                <div className="flex items-center justify-center py-12">
                  <div className="flex flex-col items-center gap-4">
                    <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                    <p className="text-gray-400">Loading classes...</p>
                  </div>
                </div>
              ) : classes.length === 0 ? (
                <div className="text-center py-12">
                  <p className="text-gray-400">
                    {userData?.role === 'admin'
                      ? 'No classes yet. Create your first class!'
                      : 'No classes scheduled'}
                  </p>
                </div>
              ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {classes.map((classItem) => (
                    <Card key={classItem.id} className="bg-black/20 border-red-500/30">
                      <CardContent className="pt-6">
                        <div className="space-y-3">
                          <div>
                            <div className="flex justify-between items-start">
                              <Badge className="bg-red-600 text-white text-[10px] mb-1">
                                {classItem.category}
                              </Badge>
                              {classItem.coachImage && (
                                <img src={classItem.coachImage} alt={classItem.coachName} className="w-10 h-10 rounded-full border border-red-500/30 object-cover" />
                              )}
                            </div>
                            <h3 className="font-semibold text-lg text-white">{classItem.name}</h3>
                            <p className="text-sm text-gray-400">
                              Coach: {classItem.coachName}
                            </p>
                          </div>

                          <div className="space-y-1 text-sm">
                            <p className="text-gray-300">
                              <span className="font-medium text-white">Hari:</span> {classItem.days?.join(', ') || '-'}
                            </p>
                            <p className="text-gray-300">
                              <span className="font-medium text-white">Jam:</span>{' '}
                              {classItem.startTime} - {classItem.endTime}
                            </p>
                          </div>

                          <div className="flex items-center justify-between py-2 border-t border-red-500/20 pt-3">
                            <div>
                              <p className="text-xs text-gray-400">Enrollment</p>
                              <p className="font-semibold text-white">
                                {classItem.enrolled}/{classItem.capacity}
                              </p>
                            </div>
                            <Badge
                              className={
                                classItem.status === 'active'
                                  ? 'bg-green-500/20 text-green-400 border border-green-500/30'
                                  : 'bg-gray-500/20 text-gray-400 border border-gray-500/30'
                              }
                            >
                              {classItem.status}
                            </Badge>
                          </div>

                          <div className="grid grid-cols-2 gap-2 mt-2">
                            <Button
                              className="w-full gap-2 bg-blue-600 hover:bg-blue-700 text-white"
                              size="sm"
                              onClick={() => setSelectedClassForMembers(classItem)}
                            >
                              {classItem.enrolled} Murid
                            </Button>
                            {userData?.role === 'admin' && (
                              <Button
                                className="w-full gap-2 bg-red-600 hover:bg-red-700 text-white"
                                size="sm"
                                onClick={() => handleDeleteClass(classItem.id)}
                              >
                                <Trash2 size={14} />
                              </Button>
                            )}
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>

          {/* Members Dialog */}
          <Dialog open={!!selectedClassForMembers} onOpenChange={(open) => !open && setSelectedClassForMembers(null)}>
            <DialogContent className="bg-black border-red-500/30 max-w-2xl">
              <DialogHeader>
                <DialogTitle className="text-white">
                  Anggota Kelas: {selectedClassForMembers?.name} ({selectedClassForMembers?.category})
                </DialogTitle>
              </DialogHeader>

              <div className="space-y-4">
                <div className="grid grid-cols-4 gap-2 items-end border-b border-red-500/20 pb-4">
                  <div className="col-span-2">
                    <label className="text-xs text-gray-400">Nama Murid</label>
                    <Input
                      value={memberFormData.name}
                      onChange={(e) => setMemberFormData({ ...memberFormData, name: e.target.value })}
                      className="bg-black/20 border-red-500/30 text-white h-8 text-sm"
                    />
                  </div>
                  <div>
                    <label className="text-xs text-gray-400">Umur</label>
                    <Input
                      type="number"
                      value={memberFormData.age}
                      onChange={(e) => setMemberFormData({ ...memberFormData, age: e.target.value })}
                      className="bg-black/20 border-red-500/30 text-white h-8 text-sm"
                    />
                  </div>
                  <div>
                    <label className="text-xs text-gray-400">Gender</label>
                    <Select
                      value={memberFormData.gender}
                      onValueChange={(v) => setMemberFormData({ ...memberFormData, gender: v as any })}
                    >
                      <SelectTrigger className="bg-black/20 border-red-500/30 text-white h-8 text-sm">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent className="bg-black border-red-500/30">
                        <SelectItem value="Laki-laki" className="text-white">L</SelectItem>
                        <SelectItem value="Perempuan" className="text-white">P</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <Button onClick={handleCreateMember} className="bg-red-600 h-8 col-span-4 mt-2">Tambah Anggota</Button>
                </div>

                <div className="max-h-[300px] overflow-auto">
                  {memberLoading ? (
                    <div className="flex justify-center p-4"><Loader2 className="animate-spin text-red-500" /></div>
                  ) : members.length === 0 ? (
                    <p className="text-center text-gray-500 py-4">Belum ada murid terdaftar.</p>
                  ) : (
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="text-left text-gray-400 border-b border-red-500/10">
                          <th className="py-2">Nama</th>
                          <th className="py-2">Umur</th>
                          <th className="py-2">L/P</th>
                          <th className="py-2 text-right">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        {members.map(m => (
                          <tr key={m.id} className="text-white border-b border-red-500/5">
                            <td className="py-2">{m.name}</td>
                            <td className="py-2">{m.age}</td>
                            <td className="py-2">{m.gender === 'Laki-laki' ? 'L' : 'P'}</td>
                            <td className="py-2 text-right">
                              <Button variant="ghost" size="sm" onClick={() => handleDeleteMember(m.id)} className="h-6 w-6 p-0 text-red-500 hover:text-red-400 hover:bg-red-500/10">
                                <Trash2 size={12} />
                              </Button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  )}
                </div>
              </div>
            </DialogContent>
          </Dialog>
        </div>
      </DashboardLayout>
    </ProtectedRoute>
  );
}
