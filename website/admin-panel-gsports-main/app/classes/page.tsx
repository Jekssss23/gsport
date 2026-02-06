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
import { collection, getDocs, addDoc, deleteDoc, doc } from 'firebase/firestore';
import { useAuth } from '@/lib/auth-context';
import { useConfirmDialog } from '@/components/confirm-dialog';

interface Class {
  id: string;
  name: string;
  instructor: string;
  day: string;
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
    name: '',
    instructor: userData?.name || '',
    day: '',
    startTime: '',
    endTime: '',
    capacity: '30',
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

  const handleCreateClass = async () => {
    if (
      !formData.name ||
      !formData.day ||
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
        name: '',
        instructor: userData?.name || '',
        day: '',
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

  const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

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
                      <label className="text-sm font-medium text-white">Class Name</label>
                      <Input
                        placeholder="e.g., Morning Yoga"
                        value={formData.name}
                        onChange={(e) =>
                          setFormData({ ...formData, name: e.target.value })
                        }
                        className="bg-black/20 border-red-500/30 text-white placeholder:text-gray-400"
                      />
                    </div>

                    <div>
                      <label className="text-sm font-medium text-white">Day</label>
                      <Select
                        value={formData.day}
                        onValueChange={(value) =>
                          setFormData({ ...formData, day: value })
                        }
                      >
                        <SelectTrigger className="bg-black/20 border-red-500/30 text-white">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent className="bg-black border-red-500/30">
                          {days.map((day) => (
                            <SelectItem key={day} value={day} className="text-white hover:bg-red-500/20">
                              {day}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
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
                            <h3 className="font-semibold text-lg text-white">{classItem.name}</h3>
                            <p className="text-sm text-gray-400">
                              Instructor: {classItem.instructor}
                            </p>
                          </div>

                          <div className="space-y-1 text-sm">
                            <p className="text-gray-300">
                              <span className="font-medium text-white">Day:</span> {classItem.day}
                            </p>
                            <p className="text-gray-300">
                              <span className="font-medium text-white">Time:</span>{' '}
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

                          {userData?.role === 'admin' && (
                            <Button
                              className="w-full gap-2 mt-2 bg-red-600 hover:bg-red-700 text-white"
                              size="sm"
                              onClick={() => handleDeleteClass(classItem.id)}
                            >
                              <Trash2 size={14} />
                              Delete
                            </Button>
                          )}
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </DashboardLayout>
    </ProtectedRoute>
  );
}
