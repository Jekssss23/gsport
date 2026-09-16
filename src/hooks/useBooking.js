import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { BookingService } from '../services/BookingService';

export const useFacilities = () => {
  return useQuery({
    queryKey: ['facilities'],
    queryFn: () => BookingService.getFacilities(),
  });
};

export const useFacilityByCourtId = (courtId) => {
  return useQuery({
    queryKey: ['facility', courtId],
    queryFn: () => BookingService.getFacilityByCourtId(courtId),
    enabled: !!courtId,
  });
};

export const useAvailableSlots = (courtId, date) => {
  return useQuery({
    queryKey: ['availableSlots', courtId, date],
    queryFn: () => BookingService.getAvailableSlots(courtId, date),
    enabled: !!courtId && !!date,
  });
};

export const useEmployeesOnDuty = (date) => {
  return useQuery({
    queryKey: ['employeesOnDuty', date],
    queryFn: () => BookingService.getEmployeesOnDuty(date),
    enabled: !!date,
  });
};

export const useStaffBySlot = (date, hour, facilityName) => {
  return useQuery({
    queryKey: ['staffBySlot', date, hour, facilityName],
    queryFn: () => BookingService.getStaffBySlot(date, hour, facilityName),
    enabled: !!date && !!hour && !!facilityName,
  });
};

export const useEmployeeRatings = (employeeId) => {
  return useQuery({
    queryKey: ['employeeRatings', employeeId],
    queryFn: () => BookingService.getEmployeeRatings(employeeId),
    enabled: !!employeeId,
  });
};

export const useFacilityReviews = (facilityId) => {
  return useQuery({
    queryKey: ['facilityReviews', facilityId],
    queryFn: () => BookingService.getFacilityReviews(facilityId),
    enabled: !!facilityId,
  });
};

export const useSubmitRating = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (reviewData) => BookingService.addReview(reviewData),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['facilityReviews'] });
      queryClient.invalidateQueries({ queryKey: ['employeeRatings'] });
    },
  });
};

export const useCreateBooking = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (bookingData) => BookingService.createBooking(bookingData),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['myBookings'] });
    },
  });
};

export const useCancelBooking = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (bookingId) => BookingService.cancelBooking(bookingId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['myBookings'] });
    },
  });
};