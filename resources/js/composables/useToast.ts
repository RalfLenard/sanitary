import { ref, reactive } from 'vue';

interface Toast {
  id: number;
  type: 'success' | 'error' | 'info' | 'warning';
  title: string;
  message?: string;
  duration?: number;
}

// Create a reactive store for toasts
const toasts = reactive<Toast[]>([]);
let toastCounter = 0;

export function useToast() {
  // Add a new toast notification
  const addToast = (toast: Omit<Toast, 'id'>) => {
    const id = toastCounter++;
    const duration = toast.duration || 5000; // Default 5 seconds
    
    toasts.push({
      id,
      ...toast
    });
    
    // Auto-remove toast after duration
    setTimeout(() => {
      removeToast(id);
    }, duration);
  };

  // Remove a toast by ID
  const removeToast = (id: number) => {
    const index = toasts.findIndex(toast => toast.id === id);
    if (index !== -1) {
      toasts.splice(index, 1);
    }
  };

  // Clear all toasts
  const clearToasts = () => {
    toasts.splice(0, toasts.length);
  };

  // Helper methods for common toast types
  const success = (title: string, message?: string, duration?: number) => {
    addToast({ type: 'success', title, message, duration });
  };

  const error = (title: string, message?: string, duration?: number) => {
    addToast({ type: 'error', title, message, duration });
  };

  const info = (title: string, message?: string, duration?: number) => {
    addToast({ type: 'info', title, message, duration });
  };

  const warning = (title: string, message?: string, duration?: number) => {
    addToast({ type: 'warning', title, message, duration });
  };

  return {
    toasts,
    addToast,
    removeToast,
    clearToasts,
    success,
    error,
    info,
    warning
  };
}