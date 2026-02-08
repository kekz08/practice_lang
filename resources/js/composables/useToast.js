import { ref } from 'vue';

const toasts = ref([]);
const defaultDuration = 3000;

export function useToast() {
  const show = (message, type = 'success') => {
    const id = Date.now();
    toasts.value.push({ id, message, type });
    setTimeout(() => {
      toasts.value = toasts.value.filter((t) => t.id !== id);
    }, defaultDuration);
  };

  const dismiss = (id) => {
    toasts.value = toasts.value.filter((t) => t.id !== id);
  };

  return { toasts, show, dismiss };
}
