<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center mb-2">
            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                {{ label }}
                <span v-if="required" class="text-red-500 ml-1">*</span>
            </label>
            <button
                v-if="!hideAddButton"
                type="button"
                @click="addIdentifier"
                class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium flex items-center transition-colors"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
            </button>
        </div>

        <div v-if="localIdentifiers.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            Belum ada {{ label.toLowerCase() }}. Klik "Tambah" untuk menambahkan.
        </div>

        <div v-for="(identifier, index) in localIdentifiers" :key="identifier._uid"
             class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="flex-1">
                <input
                    :type="inputType"
                    :value="identifier.value"
                    @input="handleInput($event, index)"
                    :placeholder="placeholder"
                    :required="required && index === 0"
                    class="bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                />
            </div>

            <button
                v-if="localIdentifiers.length > 1 || !required"
                type="button"
                @click="removeIdentifier(index)"
                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-2 transition-colors"
                :disabled="required && localIdentifiers.length === 1"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script>
let uidCounter = 0;

export default {
    name: 'IdentifierInput',
    props: {
        modelValue: {
            type: Array,
            default: () => []
        },
        type: {
            type: String,
            required: true,
            validator: (value) => ['email', 'phone', 'username'].includes(value)
        },
        label: {
            type: String,
            required: true
        },
        placeholder: {
            type: String,
            default: ''
        },
        required: {
            type: Boolean,
            default: false
        },
        inputType: {
            type: String,
            default: 'text'
        },
        hideAddButton: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            localIdentifiers: [],
            initialized: false
        }
    },
    watch: {
        modelValue: {
            immediate: true,
            handler(newVal) {
                // Hanya sync saat pertama kali atau jika jumlah item berbeda signifikan
                if (!this.initialized || this.shouldSync(newVal)) {
                    this.syncFromParent(newVal);
                    this.initialized = true;
                }
            }
        }
    },
    mounted() {
        if (this.localIdentifiers.length === 0 && this.required) {
            this.localIdentifiers = [this.createEmptyIdentifier()];
        }
    },
    methods: {
        createEmptyIdentifier() {
            return {
                id: null,
                type: this.type,
                value: '',
                _uid: `identifier-${++uidCounter}`
            };
        },

        shouldSync(newVal) {
            // Sync jika jumlah berbeda atau ada item dengan id yang belum ada di local
            if (!newVal || newVal.length === 0) return false;
            if (newVal.length !== this.localIdentifiers.length) return true;

            // Cek apakah ada item baru berdasarkan id
            const newIds = newVal.filter(v => v.id).map(v => v.id);
            const localIds = this.localIdentifiers.filter(v => v.id).map(v => v.id);

            return newIds.some(id => !localIds.includes(id));
        },

        syncFromParent(newVal) {
            if (!newVal || newVal.length === 0) {
                if (!this.required) {
                    this.localIdentifiers = [];
                }
                return;
            }

            this.localIdentifiers = newVal.map((item) => ({
                ...item,
                _uid: item._uid || `identifier-${++uidCounter}`
            }));
        },

        handleInput(event, index) {
            // Update value langsung tanpa trigger watcher
            this.localIdentifiers[index].value = event.target.value;

            // Emit ke parent dengan debounce
            this.emitUpdate();
        },

        addIdentifier() {
            this.localIdentifiers.push(this.createEmptyIdentifier());
        },

        removeIdentifier(index) {
            if (this.required && this.localIdentifiers.length === 1) {
                return;
            }

            this.localIdentifiers.splice(index, 1);
            this.emitUpdate();
        },

        emitUpdate() {
            const filtered = this.localIdentifiers
                .filter(item => item.value && item.value.trim() !== '')
                .map(({ _uid, ...item }) => item);

            this.$emit('update:modelValue', filtered);
        }
    }
}
</script>
