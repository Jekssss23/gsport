'use client';

import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Calendar as CalendarIcon, Clock, Search, Loader2, Plus, Info, Repeat, Trash2 } from 'lucide-react';
import { db } from '@/lib/firebase';
import { collection, addDoc, getDocs, query, where, orderBy, deleteDoc, doc, updateDoc } from 'firebase/firestore';
import { Employee, EmployeeSchedule } from '@/lib/types';
import Image from 'next/image';
import { toast } from 'sonner';
import { format, startOfWeek, addDays, parseISO } from 'date-fns';

export default function JadwalKaryawanPage() {
    const [employees, setEmployees] = useState<Employee[]>([]);
    const [schedules, setSchedules] = useState<EmployeeSchedule[]>([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);

    // Dialog State
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(null);
    const [formLoading, setFormLoading] = useState(false);

    // Form State
    const [formData, setFormData] = useState({
        shift: 'morning' as 'morning' | 'middle' | 'night',
        startTime: '06:30',
        endTime: '15:00',
        outlet: 'futsal' as 'futsal' | 'swimming' | 'pickleball_badminton',
        selectedDays: [] as number[], // 0: Senin, 1: Selasa, ..., 6: Minggu
    });

    const [isRolling, setIsRolling] = useState(false);
    const [rollingWeeks, setRollingWeeks] = useState<any[]>([]);

    useEffect(() => {
        fetchData();
    }, [selectedDate]); // Refetch logic when date changes

    const fetchData = async () => {
        setLoading(true);
        try {
            // Fetch Employees
            const empSnapshot = await getDocs(collection(db, 'employees'));
            const empData = empSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() })) as Employee[];
            setEmployees(empData);

            // Fetch Schedules for selected Date
            const schedRef = collection(db, 'employee_schedules');
            const q = query(schedRef, where('date', '==', selectedDate));
            const schedSnapshot = await getDocs(q);
            const schedData = schedSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() })) as EmployeeSchedule[];

            // Inject Rolling Schedules
            const targetDate = parseISO(selectedDate);
            const dayOfWeek = targetDate.getDay() === 0 ? 6 : targetDate.getDay() - 1;

            const rollingSchedules: EmployeeSchedule[] = [];
            empData.forEach(emp => {
                if (!schedData.find(s => s.employeeId === emp.id) && emp.rollingConfig?.enabled) {
                    const startDate = parseISO(emp.rollingConfig.startDate);
                    const diffDays = Math.floor((targetDate.getTime() - startDate.getTime()) / (1000 * 60 * 60 * 24));
                    const weeksPassed = Math.floor(diffDays / 7);

                    if (weeksPassed >= 0) {
                        const weekIndex = weeksPassed % emp.rollingConfig.weeks.length;
                        const config = emp.rollingConfig.weeks[weekIndex];

                        if (config.days.includes(dayOfWeek)) {
                            rollingSchedules.push({
                                id: `rolling-${emp.id}`,
                                employeeId: emp.id,
                                employeeName: emp.name,
                                employeeImageUrl: emp.imageUrl || '',
                                divisi: emp.divisi,
                                date: selectedDate,
                                shift: config.shift as any,
                                startTime: config.startTime,
                                endTime: config.endTime,
                                outlet: config.outlet as any,
                                createdAt: new Date().toISOString()
                            });
                        }
                    }
                }
            });

            setSchedules([...schedData, ...rollingSchedules]);

        } catch (error) {
            console.error('Error fetching data:', error);
            toast.error('Gagal memuat data');
        } finally {
            setLoading(false);
        }
    };

    const handleOpenSchedule = (employee: Employee) => {
        setSelectedEmployee(employee);
        // Reset form defaults based on shift logic
        setFormData({
            shift: 'morning',
            startTime: '06:30',
            endTime: '15:00',
            outlet: 'futsal',
            selectedDays: [new Date(selectedDate).getDay() === 0 ? 6 : new Date(selectedDate).getDay() - 1], // Default to current day
        });

        setIsRolling(employee.rollingConfig?.enabled || false);
        setRollingWeeks(employee.rollingConfig?.weeks || [
            { shift: 'morning', startTime: '06:30', endTime: '15:00', outlet: 'futsal', days: [0, 1, 2, 3, 4, 5] }
        ]);

        setIsDialogOpen(true);
    };

    const handleShiftChange = (shift: 'morning' | 'middle' | 'night') => {
        let start = '06:30';
        let end = '15:00';

        if (shift === 'night') {
            start = '15:00';
            end = '23:00';
        } else if (shift === 'middle') {
            start = ''; // User must input
            end = '';
        }

        setFormData(prev => ({
            ...prev,
            shift,
            startTime: start,
            endTime: end
        }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedEmployee) return;

        const employee = selectedEmployee; // Capture for TS narrowing
        setFormLoading(true);
        try {
            if (isRolling) {
                // Save Rolling Pattern to Employee Doc
                await updateDoc(doc(db, 'employees', selectedEmployee.id), {
                    rollingConfig: {
                        enabled: true,
                        startDate: startOfWeek(new Date(), { weekStartsOn: 1 }).toISOString().split('T')[0],
                        weeks: rollingWeeks
                    }
                });
                toast.success('Pola Jadwal Rolling berhasil disimpan');
                setIsDialogOpen(false);
                fetchData();
                setFormLoading(false);
                return;
            }

            if (formData.selectedDays.length === 0) {
                toast.error('Pilih setidaknya satu hari');
                setFormLoading(false);
                return;
            }

            const mondayOfWeek = startOfWeek(parseISO(selectedDate), { weekStartsOn: 1 });

            for (const dayIndex of formData.selectedDays) {
                const targetDate = format(addDays(mondayOfWeek, dayIndex), 'yyyy-MM-dd');

                // Prepare Data
                const scheduleData: Omit<EmployeeSchedule, 'id'> = {
                    employeeId: employee.id,
                    employeeName: employee.name,
                    employeeImageUrl: employee.imageUrl || '',
                    divisi: employee.divisi,
                    date: targetDate,
                    shift: formData.shift,
                    startTime: formData.startTime,
                    endTime: formData.endTime,
                    createdAt: new Date().toISOString(),
                };

                if (employee.divisi === 'Sports') {
                    scheduleData.outlet = formData.outlet;
                }

                // Check and Overwrite duplicates for each specific targetDate
                const existingSnapshot = await getDocs(query(
                    collection(db, 'employee_schedules'),
                    where('employeeId', '==', employee.id),
                    where('date', '==', targetDate)
                ));

                for (const d of existingSnapshot.docs) {
                    await deleteDoc(doc(db, 'employee_schedules', d.id));
                }

                await addDoc(collection(db, 'employee_schedules'), scheduleData);
            }

            toast.success(`Jadwal berhasil disimpan untuk ${formData.selectedDays.length} hari`);
            setIsDialogOpen(false);
            fetchData(); // Refresh list

        } catch (error) {
            console.error('Error saving schedule:', error);
            toast.error('Gagal menyimpan jadwal');
        } finally {
            setFormLoading(false);
        }
    };

    const handleDeleteSchedule = async (scheduleId: string) => {
        if (!confirm('Hapus jadwal ini?')) return;
        try {
            await deleteDoc(doc(db, 'employee_schedules', scheduleId));
            toast.success('Jadwal dihapus');
            setSchedules(prev => prev.filter(s => s.id !== scheduleId));
        } catch (error) {
            toast.error('Gagal menghapus');
        }
    };

    const filteredEmployees = employees.filter(e =>
        e.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        e.divisi.toLowerCase().includes(searchTerm.toLowerCase())
    );

    return (
        <div className="space-y-8">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-3xl font-bold text-white mb-2">Jadwal Karyawan</h1>
                    <p className="text-gray-400">Atur shift dan jadwal jaga karyawan</p>
                </div>
                <div className="flex items-center gap-2 bg-black/40 p-2 rounded-lg border border-red-500/20">
                    <CalendarIcon className="text-red-500" size={20} />
                    <Input
                        type="date"
                        value={selectedDate}
                        onChange={(e) => setSelectedDate(e.target.value)}
                        className="bg-transparent border-none text-white w-auto"
                    />
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Col: Employee List */}
                <div className="lg:col-span-2 space-y-6">
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
                            {loading ? (
                                <div className="flex justify-center py-8">
                                    <Loader2 className="animate-spin text-red-500" size={32} />
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {filteredEmployees.map(emp => {
                                        const hasSchedule = schedules.find(s => s.employeeId === emp.id);
                                        return (
                                            <Card key={emp.id} className={`bg-black/20 border-red-500/30 hover:bg-black/40 transition-colors ${hasSchedule ? 'border-green-500/30' : ''}`}>
                                                <CardContent className="p-4 flex items-center gap-4">
                                                    <div className="relative w-12 h-12 rounded-full overflow-hidden bg-gray-700 flex-shrink-0">
                                                        {emp.imageUrl ? (
                                                            <Image src={emp.imageUrl} alt={emp.name} fill className="object-cover" />
                                                        ) : (
                                                            <div className="w-full h-full flex items-center justify-center text-gray-400 font-bold">
                                                                {emp.name.charAt(0)}
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <h3 className="font-semibold text-white truncate">{emp.name}</h3>
                                                        <div className="flex items-center gap-2">
                                                            <Badge className="bg-red-500/20 text-red-400 border border-red-500/30 text-[10px] px-1 py-0 h-5">
                                                                {emp.divisi}
                                                            </Badge>
                                                            {hasSchedule && (
                                                                <Badge className="bg-green-500/20 text-green-400 border border-green-500/30 text-[10px] px-1 py-0 h-5">
                                                                    Terjadwal
                                                                </Badge>
                                                            )}
                                                        </div>
                                                    </div>
                                                    <Button
                                                        size="sm"
                                                        className={`${hasSchedule ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'} text-white h-8 px-3`}
                                                        onClick={() => handleOpenSchedule(emp)}
                                                    >
                                                        {hasSchedule ? 'Edit' : 'Atur'}
                                                    </Button>
                                                </CardContent>
                                            </Card>
                                        );
                                    })}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Right Col: Schedule Summary for Date */}
                <div className="space-y-6">
                    <Card className="bg-black/40 border-red-500/20">
                        <CardHeader>
                            <CardTitle className="text-white text-lg">Jadwal Tanggal Ini</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {schedules.length === 0 ? (
                                <p className="text-gray-400 text-sm text-center py-4">Belum ada jadwal di tanggal ini.</p>
                            ) : (
                                <div className="space-y-4">
                                    {schedules.map(sched => (
                                        <div key={sched.id} className="bg-black/30 p-3 rounded-lg border border-red-500/10 flex items-start gap-3">
                                            <div className="relative w-10 h-10 rounded-full overflow-hidden bg-gray-700 flex-shrink-0">
                                                {sched.employeeImageUrl ? (
                                                    <Image src={sched.employeeImageUrl} alt={sched.employeeName} fill className="object-cover" />
                                                ) : (
                                                    <div className="w-full h-full flex items-center justify-center text-gray-400 font-bold text-xs">
                                                        {sched.employeeName.charAt(0)}
                                                    </div>
                                                )}
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="text-white font-medium text-sm truncate">{sched.employeeName}</p>
                                                <div className="flex flex-wrap gap-1 mt-1">
                                                    <Badge variant="outline" className="text-xs border-gray-600 text-gray-300">
                                                        {sched.shift === 'morning' ? 'Pagi' : sched.shift === 'night' ? 'Malam' : 'Middle'}
                                                    </Badge>
                                                    {sched.id.startsWith('rolling-') && (
                                                        <Badge variant="outline" className="text-xs border-blue-500/50 text-blue-400 bg-blue-500/10">
                                                            Rolling
                                                        </Badge>
                                                    )}
                                                    <Badge variant="outline" className="text-xs border-gray-600 text-gray-300">
                                                        {sched.startTime} - {sched.endTime}
                                                    </Badge>
                                                </div>
                                                {sched.outlet && (
                                                    <p className="text-xs text-red-400 mt-1">
                                                        📍 {sched.outlet === 'pickleball_badminton' ? 'Pickleball & Badminton' : sched.outlet.charAt(0).toUpperCase() + sched.outlet.slice(1)}
                                                    </p>
                                                )}
                                            </div>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                className="h-6 w-6 text-gray-500 hover:text-red-500"
                                                onClick={() => handleDeleteSchedule(sched.id)}
                                                disabled={sched.id.startsWith('rolling-')}
                                            >
                                                <Trash2 size={14} />
                                            </Button>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                <DialogContent className="bg-black border-red-500/20 max-w-md">
                    <DialogHeader>
                        <DialogTitle className="text-white">Atur Jadwal - {selectedEmployee?.name}</DialogTitle>
                    </DialogHeader>

                    <div className="flex items-center justify-between p-3 bg-red-500/10 border border-red-500/20 rounded-lg mb-4">
                        <div className="flex items-center gap-2">
                            <Repeat size={18} className="text-red-500" />
                            <Label className="text-sm font-medium text-white cursor-pointer" htmlFor="rolling-mode">Gunakan Jadwal Rolling</Label>
                        </div>
                        <input
                            id="rolling-mode"
                            type="checkbox"
                            checked={isRolling}
                            onChange={(e) => setIsRolling(e.target.checked)}
                            className="w-4 h-4 accent-red-600"
                        />
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-4">

                        {isRolling ? (
                            <div className="space-y-4 max-h-[400px] overflow-auto pr-2">
                                {rollingWeeks.map((week, wIdx) => (
                                    <div key={wIdx} className="p-4 bg-white/5 border border-white/10 rounded-lg space-y-3 relative">
                                        <div className="flex justify-between items-center">
                                            <span className="text-xs font-bold text-red-500">MINGGU {wIdx + 1}</span>
                                            {rollingWeeks.length > 1 && (
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    className="h-6 w-6 text-gray-400 hover:text-red-500"
                                                    onClick={() => setRollingWeeks(prev => prev.filter((_, i) => i !== wIdx))}
                                                >
                                                    <Trash2 size={14} />
                                                </Button>
                                            )}
                                        </div>

                                        <div>
                                            <Label className="text-[10px] text-gray-400">Pilih Operasional (Hari)</Label>
                                            <div className="flex flex-wrap gap-1 mt-1">
                                                {['S', 'S', 'R', 'K', 'J', 'S', 'M'].map((day, dIdx) => {
                                                    const isSel = week.days.includes(dIdx);
                                                    return (
                                                        <Button
                                                            key={dIdx}
                                                            type="button"
                                                            variant={isSel ? 'default' : 'outline'}
                                                            className={`h-6 w-7 p-0 text-[10px] ${isSel ? 'bg-red-600' : 'border-white/20'}`}
                                                            onClick={() => {
                                                                const newWeeks = [...rollingWeeks];
                                                                newWeeks[wIdx].days = isSel
                                                                    ? week.days.filter((d: number) => d !== dIdx)
                                                                    : [...week.days, dIdx].sort();
                                                                setRollingWeeks(newWeeks);
                                                            }}
                                                        >
                                                            {day}
                                                        </Button>
                                                    );
                                                })}
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-2 gap-2">
                                            <div className="space-y-1">
                                                <Label className="text-[10px] text-gray-400">Shift</Label>
                                                <Select
                                                    value={week.shift}
                                                    onValueChange={(v) => {
                                                        const newWeeks = [...rollingWeeks];
                                                        newWeeks[wIdx].shift = v;
                                                        if (v === 'morning') { newWeeks[wIdx].startTime = '06:30'; newWeeks[wIdx].endTime = '15:00'; }
                                                        if (v === 'night') { newWeeks[wIdx].startTime = '15:00'; newWeeks[wIdx].endTime = '23:00'; }
                                                        setRollingWeeks(newWeeks);
                                                    }}
                                                >
                                                    <SelectTrigger className="h-8 text-xs bg-black/20 border-white/20">
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="morning">Pagi</SelectItem>
                                                        <SelectItem value="night">Malam</SelectItem>
                                                        <SelectItem value="middle">Middle</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            {selectedEmployee?.divisi === 'Sports' && (
                                                <div className="space-y-1">
                                                    <Label className="text-[10px] text-gray-400">Outlet</Label>
                                                    <Select
                                                        value={week.outlet}
                                                        onValueChange={(v) => {
                                                            const newWeeks = [...rollingWeeks];
                                                            newWeeks[wIdx].outlet = v;
                                                            setRollingWeeks(newWeeks);
                                                        }}
                                                    >
                                                        <SelectTrigger className="h-8 text-xs bg-black/20 border-white/20">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="futsal">Futsal</SelectItem>
                                                            <SelectItem value="swimming">Swimming</SelectItem>
                                                            <SelectItem value="pickleball_badminton">Sports</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ))}
                                <Button
                                    type="button"
                                    variant="outline"
                                    className="w-full border-dashed border-red-500/40 text-red-500 hover:bg-red-500/10 h-8 text-xs"
                                    onClick={() => setRollingWeeks([...rollingWeeks, { shift: 'morning', startTime: '06:30', endTime: '15:00', outlet: 'futsal', days: [0, 1, 2, 3, 4, 5] }])}
                                >
                                    + Tambah Pola Minggu Baru
                                </Button>
                            </div>
                        ) : (
                            <>

                                <div>
                                    <Label className="text-white">Pilih Hari</Label>
                                    <div className="flex flex-wrap gap-2 mt-2">
                                        {['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'].map((day, idx) => {
                                            const isSelected = formData.selectedDays.includes(idx);
                                            return (
                                                <Button
                                                    key={day}
                                                    type="button"
                                                    size="sm"
                                                    variant={isSelected ? 'default' : 'outline'}
                                                    className={`h-8 w-10 p-0 text-[10px] ${isSelected ? 'bg-red-600 border-red-600' : 'border-red-500/30 text-gray-400'}`}
                                                    onClick={() => {
                                                        setFormData(prev => ({
                                                            ...prev,
                                                            selectedDays: isSelected
                                                                ? prev.selectedDays.filter(d => d !== idx)
                                                                : [...prev.selectedDays, idx].sort()
                                                        }));
                                                    }}
                                                >
                                                    {day}
                                                </Button>
                                            );
                                        })}
                                    </div>
                                    <p className="text-[10px] text-gray-500 mt-1 italic">*Jadwal akan diterapkan pada minggu yang dipilih ({format(startOfWeek(parseISO(selectedDate), { weekStartsOn: 1 }), 'dd MMM')} - {format(addDays(startOfWeek(parseISO(selectedDate), { weekStartsOn: 1 }), 6), 'dd MMM')})</p>
                                </div>

                                <div>
                                    <Label className="text-white">Pilih Shift</Label>
                                    <Select
                                        value={formData.shift}
                                        onValueChange={(v: any) => handleShiftChange(v)}
                                    >
                                        <SelectTrigger className="bg-black/20 border-red-500/30 text-white mt-1">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent className="bg-black border-red-500/30">
                                            <SelectItem value="morning" className="text-white hover:bg-red-500/20">Pagi (06:30 - 15:00)</SelectItem>
                                            {(selectedEmployee?.divisi !== 'Sports' && selectedEmployee?.divisi !== 'HK' && selectedEmployee?.divisi !== 'housekeeping' && selectedEmployee?.divisi !== 'Housekeeping') && (
                                                <SelectItem value="middle" className="text-white hover:bg-red-500/20">Middle (Custom)</SelectItem>
                                            )}
                                            <SelectItem value="night" className="text-white hover:bg-red-500/20">Malam (15:00 - 23:00)</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label className="text-white">Jam Masuk</Label>
                                        <Input
                                            type="time"
                                            value={formData.startTime}
                                            onChange={(e) => setFormData({ ...formData, startTime: e.target.value })}
                                            disabled={formData.shift !== 'middle'}
                                            className="bg-black/20 border-red-500/30 text-white mt-1 disabled:opacity-50"
                                        />
                                    </div>
                                    <div>
                                        <Label className="text-white">Jam Pulang</Label>
                                        <Input
                                            type="time"
                                            value={formData.endTime}
                                            onChange={(e) => setFormData({ ...formData, endTime: e.target.value })}
                                            disabled={formData.shift !== 'middle'}
                                            className="bg-black/20 border-red-500/30 text-white mt-1 disabled:opacity-50"
                                        />
                                    </div>
                                </div>

                                {selectedEmployee?.divisi === 'Sports' && (
                                    <div>
                                        <Label className="text-white">Penempatan Outlet</Label>
                                        <Select
                                            value={formData.outlet}
                                            onValueChange={(v: any) => setFormData({ ...formData, outlet: v })}
                                        >
                                            <SelectTrigger className="bg-black/20 border-red-500/30 text-white mt-1">
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent className="bg-black border-red-500/30">
                                                <SelectItem value="futsal" className="text-white hover:bg-red-500/20">Futsal</SelectItem>
                                                <SelectItem value="swimming" className="text-white hover:bg-red-500/20">Swimming</SelectItem>
                                                <SelectItem value="pickleball_badminton" className="text-white hover:bg-red-500/20">Pickleball & Badminton</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p className="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                            <Info size={12} />
                                            Wajib pilih outlet untuk divisi Sports
                                        </p>
                                    </div>
                                )}
                            </>
                        )}

                        <Button
                            type="submit"
                            className="w-full bg-red-600 hover:bg-red-700 text-white mt-4"
                            disabled={formLoading}
                        >
                            {formLoading ? <Loader2 className="animate-spin" /> : 'Simpan Jadwal'}
                        </Button>

                    </form>
                </DialogContent>
            </Dialog>
        </div>
    );
}
