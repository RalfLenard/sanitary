<template>
  <div class="toast-container" v-if="visible">
    <transition-group name="toast" tag="div">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="['toast', `toast-${toast.type}`]"
        @click="removeToast(toast.id)"
      >
        <div class="toast-icon">
          <CheckCircle v-if="toast.type === 'success'" class="h-5 w-5" />
          <AlertCircle v-else-if="toast.type === 'error'" class="h-5 w-5" />
          <Info v-else-if="toast.type === 'info'" class="h-5 w-5" />
          <AlertTriangle v-else-if="toast.type === 'warning'" class="h-5 w-5" />
        </div>
        <div class="toast-content">
          <div class="toast-title">{{ toast.title }}</div>
          <div class="toast-message" v-if="toast.message">{{ toast.message }}</div>
        </div>
        <button class="toast-close" @click.stop="removeToast(toast.id)">
          <X class="h-4 w-4" />
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { CheckCircle, AlertCircle, Info, AlertTriangle, X } from 'lucide-vue-next';

interface Toast {
  id: number;
  type: 'success' | 'error' | 'info' | 'warning';
  title: string;
  message?: string;
  duration?: number;
}

const toasts = ref<Toast[]>([]);
const visible = computed(() => toasts.value.length > 0);
let toastCounter = 0;

const addToast = (toast: Omit<Toast, 'id'>) => {
  const id = toastCounter++;
  const duration = toast.duration || 5000; // Default to 5 seconds
  
  toasts.value.push({
    id,
    ...toast
  });

  setTimeout(() => {
    removeToast(id);
  }, duration);
};

const removeToast = (id: number) => {
  const index = toasts.value.findIndex(toast => toast.id === id);
  if (index !== -1) {
    toasts.value.splice(index, 1);
  }
};

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

defineExpose({
  success,
  error,
  info,
  warning,
  addToast,
  removeToast
});
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 10px;
  right: 10px;
  z-index: 9999;
}

.toast {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  padding: 10px 15px;
  border-radius: 8px;
  width: 300px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
  opacity: 1;
  animation: fadeIn 0.5s ease-in-out;
}

.toast-success {
  background-color: #28a745;
  color: white;
}

.toast-error {
  background-color: #dc3545;
  color: white;
}

.toast-info {
  background-color: #17a2b8;
  color: white;
}

.toast-warning {
  background-color: #ffc107;
  color: white;
}

.toast-icon {
  margin-right: 10px;
}

.toast-content {
  flex-grow: 1;
}

.toast-title {
  font-weight: bold;
}

.toast-message {
  font-size: 0.875rem;
}

.toast-close {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
  padding: 0;
  margin-left: 10px;
}

.toast-enter-active, .toast-leave-active {
  transition: opacity 0.5s ease;
}

.toast-enter, .toast-leave-to {
  opacity: 0;
}

@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
