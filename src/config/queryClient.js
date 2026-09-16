import { QueryClient } from '@tanstack/react-query';
import { PersistQueryClientProvider } from '@tanstack/react-query-persist-client';
import { createSyncStoragePersister } from '@tanstack/query-sync-storage-persister';
import AsyncStorage from '@react-native-async-storage/async-storage';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 5 * 60 * 1000,
      gcTime: 30 * 60 * 1000,
      retry: 1,
      refetchOnWindowFocus: false,
      refetchOnReconnect: 'always',
    },
  },
});

const persister = createSyncStoragePersister({
  storage: AsyncStorage,
  key: 'react-query-cache',
  maxAge: 1000 * 60 * 60 * 24,
  deserialize: (cachedString) => {
    try {
      return JSON.parse(cachedString);
    } catch (e) {
      console.warn('[react-query] Cache rusak, diabaikan:', e?.message);
      return undefined;
    }
  },
});

export function QueryProvider({ children }) {
  return (
    <PersistQueryClientProvider
      client={queryClient}
      persistOptions={{
        persister,
        maxAge: 1000 * 60 * 60 * 24,
        buster: 'v2',
      }}
    >
      {children}
    </PersistQueryClientProvider>
  );
}

export { queryClient };