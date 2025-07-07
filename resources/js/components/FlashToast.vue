<template>
  <div v-if="toasts.length > 0" class="toast-container">
      <transition-group name="toast">
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

<script setup>
import { ref, computed } from 'vue';
import { CheckCircle, AlertCircle, Info, AlertTriangle, X } from 'lucide-vue-next';

let toastCounter = 0;
const toasts = ref([]);

const addToast = (toast) => {
  const id = toastCounter++;
  toasts.value.push({ id, ...toast });

  setTimeout(() => {
      removeToast(id);
  }, toast.duration || 5000); // Default 5 seconds duration
};

const removeToast = (id) => {
  const index = toasts.value.findIndex((toast) => toast.id === id);
  if (index !== -1) {
      toasts.value.splice(index, 1);
  }
};

// Helper methods for toast types
const success = (title, message, duration = 5000) => {
  addToast({ type: 'success', title, message, duration });
};

const error = (title, message, duration = 5000) => {
  addToast({ type: 'error', title, message, duration });
};

const info = (title, message, duration = 5000) => {
  addToast({ type: 'info', title, message, duration });
};

const warning = (title, message, duration = 5000) => {
  addToast({ type: 'warning', title, message, duration });
};

// Expose methods for usage outside of the component
defineExpose({
  success,
  error,
  info,
  warning,
  addToast,
  removeToast,
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
  background-color: #333;
  color: white;
  padding: 10px;
  margin: 5px 0;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.toast-success {
  background-color: green;
}

.toast-error {
  background-color: red;
}

.toast-info {
  background-color: blue;
}

.toast-warning {
  background-color: orange;
}

.toast-close {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
}
</style>
