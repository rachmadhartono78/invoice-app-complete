<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-bold mb-4">Import Items from CSV</h2>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    Upload a CSV file with columns: Area, Item Name, Dimension, Price, Qty.
                </p>
                <div 
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="$refs.fileInput.click()"
                >
                    <input 
                        ref="fileInput" 
                        type="file" 
                        class="hidden" 
                        accept=".csv,.txt"
                        @change="handleFileChange"
                    />
                    <div v-if="!file">
                        <span class="text-gray-400 text-4xl block mb-2">📂</span>
                        <span class="text-blue-600 font-medium">Click to upload CSV</span>
                    </div>
                    <div v-else>
                        <span class="text-green-500 text-4xl block mb-2">📄</span>
                        <span class="font-medium text-gray-800">{{ file.name }}</span>
                        <p class="text-xs text-gray-500 mt-1">{{ (file.size / 1024).toFixed(1) }} KB</p>
                    </div>
                </div>
            </div>

            <div v-if="uploading" class="mb-4">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 animate-pulse w-full"></div>
                </div>
                <p class="text-xs text-center text-gray-500 mt-1">Processing...</p>
            </div>

            <div v-if="message" :class="`mb-4 text-sm p-2 rounded ${isError ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`">
                {{ message }}
            </div>

            <div class="flex justify-end gap-3 mt-4">
                <button 
                    @click="close" 
                    class="px-4 py-2 border rounded hover:bg-gray-100 disabled:opacity-50"
                    :disabled="uploading"
                >
                    Cancel
                </button>
                <button 
                    @click="upload" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                    :disabled="!file || uploading"
                >
                    Import
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from '../../../api/dashboardAxios'; // Adjust path if needed

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['close', 'success']);

const file = ref(null);
const uploading = ref(false);
const message = ref('');
const isError = ref(false);

const handleFileChange = (e) => {
    const selected = e.target.files[0];
    if (selected) {
        if (selected.type === "text/csv" || selected.type === "application/vnd.ms-excel" || selected.name.endsWith('.csv') || selected.name.endsWith('.txt')) {
             file.value = selected;
             message.value = '';
             isError.value = false;
        } else {
            message.value = "Invalid file type. Please upload a CSV.";
            isError.value = true;
        }
    }
};

const upload = async () => {
    if (!file.value) return;

    uploading.value = true;
    message.value = '';
    isError.value = false;

    const formData = new FormData();
    formData.append('file', file.value);

    try {
        const response = await axios.post('/items/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        message.value = response.data.message;
        isError.value = false;
        
        setTimeout(() => {
            close();
            emit('success');
        }, 1500);

    } catch (e) {
        console.error(e);
        message.value = e.response?.data?.message || 'Import failed.';
        isError.value = true;
    } finally {
        uploading.value = false;
    }
};

const close = () => {
    emit('close');
    file.value = null;
    message.value = '';
    isError.value = false;
};
</script>
