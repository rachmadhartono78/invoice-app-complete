import { ref } from "vue";

type ConfirmationOptions = {
  title?: string;
  message?: string;
  confirmText?: string;
  cancelText?: string;
  confirmButtonColor?: string;
  loading?: boolean;
};

const isVisible = ref(false);
const options = ref<ConfirmationOptions>({
  title: "Konfirmasi",
  message: "Apakah Anda yakin ingin melanjutkan?",
  confirmText: "Ya, saya yakin",
  cancelText: "Tidak, batal",
  confirmButtonColor: "primary",
  loading: false,
});

let resolver: ((value: boolean) => void) | null = null;

export function useConfirmation() {
  function confirm(customOptions: ConfirmationOptions): Promise<boolean> {
    options.value = { ...options.value, ...customOptions };
    isVisible.value = true;

    return new Promise((resolve) => {
      resolver = resolve;
    });
  }

  function close(result: boolean) {
    isVisible.value = false;
    if (resolver) {
      resolver(result);
      resolver = null;
    }
  }

  return { isVisible, options, confirm, close };
}

export const confirmation = useConfirmation();