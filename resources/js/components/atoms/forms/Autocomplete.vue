<template>
    <Combobox v-model="internalValue" :optionLabel="props.optionLabel" :optionValue="props.optionValue"
        :multiple="props.multiple" as="div" class="relative w-full" :disabled="props.disabled">
        <!-- Input -->
        <div class="relative">
            <ComboboxInput ref="inputRef" as="input" :placeholder="props.placeholder" :disabled="props.disabled"
                :class="[
                    'w-full rounded-md border border-gray-300 bg-gray-50 py-2.3 md:py-2.5 pl-3 pr-10 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm',
                    props.inputClass
                ]"
                @change="query = $event.target.value" @focus="isOpen = true" @blur="closeDropdown"
                :displayValue="displayValue" />
            <button v-if="props.modelValue?.length" @click="clearValue()" type="button"
                class="absolute inset-y-0 right-0 flex items-center pr-2">
                <XMarkIcon class="h-5 w-5 text-gray-400" aria-hidden="true" v-if="!props.disabled"/>
            </button>
        </div>
        <div class="flex flex-wrap gap-2 mt-2" v-if="props.multiple && !isOpen">
            <div class="flex items-center justify-between gap-2 px-3 border border-primary-600 py-1 text-sm text-gray-700 dark:text-white bg-gray-300 bg-opacity-20  rounded-full dark:bg-transparent"
                v-for="value in props.modelValue">
                <p>{{getOptionLabel(options.find(x => x[props.optionValue] == value))}}</p>
                <XMarkIcon @click="removeValue(value)" class="h-5 w-5 cursor-pointer" aria-hidden="true" v-if="!props.disabled"/>
            </div>
        </div>

        <!-- Dropdown -->
        <div v-show="isOpen && (filteredOptions.length > 0 || query)">
            <ComboboxOptions static
                class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <div v-if="filteredOptions.length == 0"
                    class="relative cursor-default select-none px-4 py-2 text-gray-700">
                    Tidak ditemukan.
                </div>
                <ComboboxOption v-for="option in filteredOptions" :key="getOptionValue(option)" :value="option"
                    v-slot="{ active, selected }" as="template">
                    <li :class="[
                        'relative cursor-default select-none py-2 pl-4 pr-4',
                        active ? 'bg-primary-600 text-white' : 'text-gray-900'
                    ]">
                        <div class="flex gap-2">
                            <div :class="[
                                'block truncate',
                                selected ? 'font-semibold' : 'font-normal'
                            ]">
                                {{ getOptionLabel(option) }}
                            </div>
                            <CheckCircleIcon v-if="option?.mark" class="h-5 w-5 text-primary-500"/>
                        </div>
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </div>
    </Combobox>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
} from "@headlessui/vue";
import { XMarkIcon, CheckCircleIcon } from "@heroicons/vue/24/solid";

const inputRef = ref(null);

// Props
const props = defineProps({
    modelValue: { type: [String, Object, Number, Array<any>], default: null },
    options: { type: Array, default: () => [] },
    optionLabel: { type: String, default: null },
    optionValue: { type: String, default: null },
    multiple: { type: Boolean, default: false },
    placeholder: { type: String, default: null, required: false },
    disabled: { type: Boolean, default: false },
    inputClass: { type: String, default: '' },
});

const emit = defineEmits(["update:modelValue", "change"]);

const query = ref<string>("");
const isOpen = ref<boolean>(false);
const internalValue = ref<any>(props.modelValue);

// sinkron prop → local
const isEqualArray = (a: any[], b: any) => {
    if (a.length !== b.length) return false;
    return JSON.stringify(a) === JSON.stringify(b);
}

watch(
    () => props.modelValue,
    (val: any) => {
        if (props.multiple) {
            const selected = props.options.filter((opt: any) =>
                val?.includes(opt[props.optionValue])
            );

            if (isEqualArray(selected, internalValue.value)) return;

            internalValue.value = selected;
        } else {
            const selected = props.optionValue && val
                ? props.options.find((x: any) => x[props.optionValue] == val)
                : val;

            if (selected === internalValue.value) return;

            internalValue.value = selected;
        }
    },
    { immediate: true }
);

// sinkron local → emit ke parent
watch(internalValue, (val: any) => {
    if (props.multiple) {
        emit("update:modelValue", internalValue.value.map((v: any) => props.optionValue ? v[props.optionValue] : v));
        emit("change", internalValue.value.map((v: any) => props.optionValue ? v[props.optionValue] : v));
    } else {
        emit("update:modelValue", props.optionValue && val ? val[props.optionValue] : val);
        emit("change", props.optionValue && val ? val[props.optionValue] : val);
    }
    closeDropdown();
});

// close dropdown setelah blur dengan delay biar klik option tetap jalan
const closeDropdown = () => {
    setTimeout(() => {
        isOpen.value = false;
        query.value = "";
        inputRef.value?.$el?.blur();
    }, 100);
};

const clearValue = () => {
    emit("update:modelValue", props.multiple ? [] : null)
}

// Helpers
const getOptionLabel = (option: any) =>
    props.optionLabel ? option[props.optionLabel] : option;

const getOptionValue = (option: any) =>
    props.optionValue ? option[props.optionValue] : option;

const filteredOptions = computed<any>(() => {
    if (!props.options || !Array.isArray(props.options)) return [];
    if (!query.value) return props.options;
    return props.options.filter((opt) => {
        const label = getOptionLabel(opt);
        return label && label.toString().toLowerCase().includes(query.value.toLowerCase());
    });
});

const displayValue = (option: any) => {
    if (props.multiple) {
        return "";
    }
    return option ? getOptionLabel(option) : "";
};

const removeValue = (value: any) => {
    const index = internalValue.value.findIndex((i: any) => i?.[props.optionValue] == value);
    if (index !== -1) {
        internalValue.value = internalValue.value.filter(
            (i: any) => i?.[props.optionValue] != value
        )
    }
};

</script>
