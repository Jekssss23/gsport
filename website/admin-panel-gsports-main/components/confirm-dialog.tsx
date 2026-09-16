'use client';

import { useState, useCallback } from 'react';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';

interface ConfirmDialogOptions {
  title: string;
  description: string;
  actionText?: string;
  cancelText?: string;
  actionVariant?: 'default' | 'destructive';
}

interface ConfirmDialogState {
  isOpen: boolean;
  options: ConfirmDialogOptions | null;
  resolve: ((value: boolean) => void) | null;
}

export function useConfirmDialog() {
  const [state, setState] = useState<ConfirmDialogState>({
    isOpen: false,
    options: null,
    resolve: null,
  });

  const confirm = useCallback(
    (options: ConfirmDialogOptions): Promise<boolean> => {
      return new Promise((resolve) => {
        setState({
          isOpen: true,
          options,
          resolve,
        });
      });
    },
    []
  );

  const handleConfirm = useCallback(() => {
    if (state.resolve) {
      state.resolve(true);
    }
    setState({
      isOpen: false,
      options: null,
      resolve: null,
    });
  }, [state.resolve]);

  const handleCancel = useCallback(() => {
    if (state.resolve) {
      state.resolve(false);
    }
    setState({
      isOpen: false,
      options: null,
      resolve: null,
    });
  }, [state.resolve]);

  return {
    confirm,
    dialog: (
      <ConfirmDialogComponent
        isOpen={state.isOpen}
        options={state.options}
        onConfirm={handleConfirm}
        onCancel={handleCancel}
      />
    ),
  };
}

interface ConfirmDialogComponentProps {
  isOpen: boolean;
  options: ConfirmDialogOptions | null;
  onConfirm: () => void;
  onCancel: () => void;
}

function ConfirmDialogComponent({
  isOpen,
  options,
  onConfirm,
  onCancel,
}: ConfirmDialogComponentProps) {
  if (!options) return null;

  return (
    <AlertDialog open={isOpen} onOpenChange={(open) => {
      if (!open) onCancel();
    }}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>{options.title}</AlertDialogTitle>
          <AlertDialogDescription>
            {options.description}
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel onClick={onCancel}>
            {options.cancelText || 'Cancel'}
          </AlertDialogCancel>
          <AlertDialogAction
            onClick={onConfirm}
            className={
              options.actionVariant === 'destructive'
                ? 'bg-destructive text-destructive-foreground hover:bg-destructive/90'
                : ''
            }
          >
            {options.actionText || 'Confirm'}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
