<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-[80vh] flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center bg-blue-50 rounded-t-lg">
                <h2 class="text-xl font-bold text-blue-800">Batch Item Selection (Katalog)</h2>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4">
                <!-- Search -->
                <div class="mb-4">
                    <input 
                        v-model="search" 
                        placeholder="Search items..." 
                        class="w-full border px-4 py-2 rounded shadow-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    />
                </div>

                <!-- Grouped Items -->
                <div v-if="Object.keys(groupedItems).length === 0" class="text-center py-8 text-gray-500">
                    No items found.
                </div>

                <div v-for="(items, area) in groupedItems" :key="area" class="mb-6">
                    <h3 class="font-bold text-lg text-gray-700 border-b pb-1 mb-3 sticky top-0 bg-white z-10">
                        {{ area || 'Uncategorized' }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div 
                            v-for="item in items" 
                            :key="item.id" 
                            @click="toggleSelection(item)"
                            :class="[
                                'border rounded-lg p-3 cursor-pointer transition-all',
                                isSelected(item) ? 'bg-blue-100 border-blue-500 ring-1 ring-blue-500' : 'hover:bg-gray-50 border-gray-200'
                            ]"
                        >
                            <div class="flex justify-between items-start">
                                <div class="font-semibold text-sm">{{ item.name }}</div>
                                <div v-if="isSelected(item)" class="text-blue-600">✓</div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ item.code || '-' }}
                            </div>
                            <div class="text-xs font-mono mt-1 font-bold text-gray-700">
                                Rp {{ fmt(item.price) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t bg-gray-50 rounded-b-lg flex justify-between items-center">
                <div class="text-sm font-semibold text-gray-600">
                    {{ selectedItems.length }} key items selected
                </div>
                <div class="flex gap-3">
                    <button 
                        @click="$emit('close')" 
                        class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="confirmSelection" 
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors shadow-sm font-medium"
                        :disabled="selectedItems.length === 0"
                    >
                        Add Selected Items
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    show: Boolean,
    items: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'confirm']);

const search = ref('');
const selectedItems = ref([]);

// toggle selection logic
const toggleSelection = (item) => {
    const idx = selectedItems.value.findIndex(i => i.id === item.id);
    if (idx >= 0) {
        selectedItems.value.splice(idx, 1);
    } else {
        selectedItems.value.push(item);
    }
};

const isSelected = (item) => selectedItems.value.some(i => i.id === item.id);

const filteredItems = computed(() => {
    if (!search.value) return props.items;
    const s = search.value.toLowerCase();
    return props.items.filter(i => 
        i.name.toLowerCase().includes(s) || 
        (i.code && i.code.toLowerCase().includes(s))
    );
});

// Group items by category (which is mapped to area in our context)
// Assuming item.category relationship exists and has name, or item has category_name
// BUT wait, in ItemSeeder we mapped Category -> Area.
// The Item model likely has a category relationship.
// If the items prop comes from `InvoiceForm` fetching `items`, we need to ensure it includes category.
// `InvoiceController` index? No, `ItemController` index.
// Let's assume standard API returns loaded relationships or we might need to adjust `ItemController`.
const groupedItems = computed(() => {
    const groups = {};
    filteredItems.value.forEach(item => {
        // Use category name as Area, or 'Lainnya' if null
        // We need to check expected data structure from `items` API.
        // Usually it's item.category.name or item.category_name
        const area = item.category?.name || 'Uncategorized'; 
        if (!groups[area]) groups[area] = [];
        groups[area].push(item);
    });
    return groups;
});

const fmt = (n) => new Intl.NumberFormat("id").format(n);

const confirmSelection = () => {
    // Map selected items to invoice item structure
    const mapped = selectedItems.value.map(item => ({
        item_id: item.id,
        item_code: item.code || '',
        item_name: item.name,
        area: item.category?.name || null, // Map Category Name to Area
        quantity: 1, // Default qty
        unit_price: item.price || 0,
        discount: 0
    }));
    emit('confirm', mapped);
    selectedItems.value = []; // Reset after confirm
};
</script>
