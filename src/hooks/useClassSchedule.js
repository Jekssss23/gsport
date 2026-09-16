import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ClassScheduleService } from '../services/ClassScheduleService';

export const useAvailableClasses = (date = null, categoryId = null) => {
  return useQuery({
    queryKey: ['availableClasses', date, categoryId],
    queryFn: () => ClassScheduleService.getAvailableClasses(date, categoryId),
    staleTime: 5 * 60 * 1000,
    gcTime: 10 * 60 * 1000,
    enabled: !!date,
  });
};

export const useCategories = () => {
  return useQuery({
    queryKey: ['classCategories'],
    queryFn: () => ClassScheduleService.getCategories(),
    staleTime: 30 * 60 * 1000,
    gcTime: 60 * 60 * 1000,
  });
};

export const useClassTypes = (categoryId) => {
  return useQuery({
    queryKey: ['classTypes', categoryId],
    queryFn: () => ClassScheduleService.getClassTypes(categoryId),
    staleTime: 10 * 60 * 1000,
    gcTime: 30 * 60 * 1000,
    enabled: !!categoryId,
  });
};

export const useUserClassBookings = (firebaseUid) => {
  return useQuery({
    queryKey: ['myClassBookings', firebaseUid],
    queryFn: () => ClassScheduleService.getUserBookings(firebaseUid),
    staleTime: 5 * 60 * 1000,
    gcTime: 10 * 60 * 1000,
    enabled: !!firebaseUid,
  });
};

export const useBookClass = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (bookingData) => ClassScheduleService.bookClass(bookingData),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['myClassBookings', variables.firebase_uid] });
      queryClient.invalidateQueries({ queryKey: ['availableClasses'] });
    },
  });
};

export const useCancelClassBooking = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ bookingId, firebaseUid }) => ClassScheduleService.cancelBooking(bookingId, firebaseUid),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['myClassBookings', variables.firebaseUid] });
      queryClient.invalidateQueries({ queryKey: ['availableClasses'] });
    },
  });
};

export const useTodaySessions = () => {
  return useQuery({
    queryKey: ['todaySessions'],
    queryFn: () => ClassScheduleService.getTodaySessions(),
    staleTime: 2 * 60 * 1000,
    gcTime: 5 * 60 * 1000,
  });
};

export const useAttendanceCheckIn = () => {
  return useMutation({
    mutationFn: (qrPayload) => ClassScheduleService.attendanceCheckIn(qrPayload),
  });
};

export const useAttendanceCheckInMember = () => {
  return useMutation({
    mutationFn: ({ classSessionId, memberQrPayload }) => 
      ClassScheduleService.attendanceCheckInMember(classSessionId, memberQrPayload),
  });
};

export const useAttendanceCheckInMemberAuto = () => {
  return useMutation({
    mutationFn: (memberQrPayload) => ClassScheduleService.attendanceCheckInMemberAuto(memberQrPayload),
  });
};