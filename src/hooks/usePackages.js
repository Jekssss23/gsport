import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { auth, db } from '../config/firebase';
import { collection, query, where, getDocs, addDoc, serverTimestamp, orderBy } from 'firebase/firestore';
import { API_BASE_URL } from '../config/api';

export const usePackages = () => {
  const user = auth.currentUser;
  return useQuery({
    queryKey: ['gscPackages', user?.uid],
    queryFn: async () => {
      if (!user) return [];
      const q = query(
        collection(db, 'gsc_packages'),
        where('userId', '==', user.uid),
        orderBy('createdAt', 'desc')
      );
      const snapshot = await getDocs(q);
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    },
    enabled: !!user,
    staleTime: 30 * 1000,
    gcTime: 5 * 60 * 1000,
  });
};

export const useBuyPackage = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: async ({ selectedSport, selectedPackage, paymentProof }) => {
      if (!auth.currentUser) throw new Error('User not authenticated');

      const recordFinancials = async (amount, type, desc) => {
        try {
          await addDoc(collection(db, 'financial_transactions'), {
            userId: auth.currentUser.uid,
            userEmail: auth.currentUser.email,
            amount: amount,
            transactionType: type,
            description: desc,
            createdAt: serverTimestamp()
          });

          const payload = {
            firebase_uid: auth.currentUser.uid,
            user_email: auth.currentUser.email,
            amount: amount,
            transaction_type: type,
            description: desc
          };

          await fetch(`${API_BASE_URL}/finance/record_transaction`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
          });
        } catch (e) {
          console.error("Error logging finance:", e);
        }
      };

      await addDoc(collection(db, 'gsc_packages'), {
        userId: auth.currentUser.uid,
        userEmail: auth.currentUser.email,
        sportType: selectedSport.name,
        packageName: selectedPackage.name,
        pricePerHour: Math.round(selectedPackage.price / selectedPackage.hours),
        totalHours: selectedPackage.hours,
        remainingHours: selectedPackage.hours,
        totalPrice: selectedPackage.price,
        status: 'active',
        validityMonths: selectedPackage.validityMonths,
        paymentProofBase64: paymentProof,
        createdAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      });

      await recordFinancials(selectedPackage.price, 'package', `GSC Package Purchase ${selectedPackage.name} for ${selectedSport.name}`);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['gscPackages'] });
    },
  });
};

export const useDeductPackageHours = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: async ({ packageId, hours }) => {
      const res = await fetch(`${API_BASE_URL}/gsc_package/deduct_hours`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          package_id: packageId,
          hours: hours,
          admin_id: auth.currentUser?.uid,
          admin_name: auth.currentUser?.email
        })
      });
      const json = await res.json();
      if (!res.ok || !json.ok) throw new Error(json.message || 'Failed to deduct hours');
      return json;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['gscPackages'] });
    },
  });
};