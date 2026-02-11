<template>
  <div class="relative w-full">
    <!-- Input Field -->
    <div class="relative">
      <input type="text" v-model="displayText" @input="onInput" @focus="onFocus" @blur="onBlur" @keydown="onKeydown"
        :required="required" v-bind="$attrs" :placeholder="placeholder" :class="[
          'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500',
          !multiple && modelValue ? 'pr-10' : '',
          inputClass
        ]" />

      <!-- Clear button for single selection -->
      <button v-if="!multiple && modelValue" type="button" @click="clearSelection"
        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 p-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Selected Tags (for multiple selection) -->
    <div v-if="multiple && modelValue.length" class="flex flex-wrap gap-2 mt-2">
      <span v-for="(item, index) in selectedItems" :key="index"
        class="flex items-center px-3 border border-primary-600 py-1 text-sm text-gray-700 dark:text-white bg-gray-300 bg-opacity-20  rounded-full dark:bg-transparent ">
        {{ item[labelKey] }}
        <button type="button" @click="removeItem(item)"
          class="ml-2 text-gray-800 dark:text-white hover:text-gray-300">✕</button>
      </span>
    </div>

    <!-- Dropdown -->
    <ul v-if="filteredItems.length && focused"
      class="absolute z-10 w-full mt-2 p-2 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-md max-h-60 dark:bg-gray-800 dark:border-gray-700">
      <li v-for="(item, index) in filteredItems" :key="index" @mousedown.prevent="toggleSelectItem(item)"
        class="flex justify-between p-2 text-sm dark:text-white cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
        <span>{{ item[labelKey] }}</span>
        <span v-if="isSelected(item)" class="text-blue-500 dark:text-blue-400">✓</span>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  emits: ['select'],
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    modelValue: {
      type: [Array, Object, Number, String, null],
      default: () => [],
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    placeholder: {
      type: String,
      default: "Search...",
    },
    labelKey: {
      type: String,
      default: "name",
    },
    valueKey: {
      type: String,
      default: "id",
    },
    returnObject: {
      type: Boolean,
      default: false,
    },
    required: {
      type: Boolean,
      default: false
    },
    inputClass: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      searchTerm: "",
      filteredItems: [],
      focused: false,
      isTyping: false,
    };
  },
  computed: {
    selectedItems() {
      if (this.multiple) {
        return this.modelValue.map(value => this.items.find(item => item[this.valueKey] === value) || value);
      }
      return this.items.find(item => item[this.valueKey] === this.modelValue) || this.modelValue;
    },
    displayText: {
      get() {
        if (!this.multiple && this.selectedItems && typeof this.selectedItems === 'object' && !this.isTyping) {
          return this.selectedItems[this.labelKey] || "";
        }
        return this.searchTerm;
      },
      set(value) {
        this.searchTerm = value;
        this.isTyping = true;
      }
    }
  },
  watch: {
    searchTerm: "onSearch",
    modelValue: {
      handler() {
        // Reset typing state when modelValue changes externally
        this.isTyping = false;
      },
      deep: true
    }
  },
  methods: {
    onInput() {
      this.isTyping = true;
      this.onSearch();
    },
    onSearch() {
      if (!this.isTyping && !this.multiple && this.modelValue) {
        // Don't filter if not typing and single selection exists
        this.filteredItems = [];
        return;
      }

      const search = this.searchTerm.toLowerCase();
      this.filteredItems = this.items.filter(
        (item) =>
          item?.[this.labelKey]?.toString().toLowerCase().includes(search) &&
          (!this.multiple || !this.isSelected(item))
      );
    },
    onKeydown(event) {
      // Handle backspace for single selection
      if (event.key === 'Backspace' && !this.multiple && this.modelValue && !this.isTyping) {
        event.preventDefault();
        this.clearSelection();
        this.isTyping = true;
        this.focused = true;
        this.$nextTick(() => {
          this.onSearch();
        });
      }
      // Handle escape key
      else if (event.key === 'Escape') {
        this.focused = false;
      }
      // Handle enter key
      else if (event.key === 'Enter' && this.filteredItems.length > 0) {
        event.preventDefault();
        this.toggleSelectItem(this.filteredItems[0]);
      }
    },
    onFocus() {
      this.focused = true;
      if (!this.multiple && this.modelValue && !this.isTyping) {
        // Show all items for single selection when focused
        this.filteredItems = this.items.filter(item => !this.isSelected(item));
      } else {
        this.onSearch();
      }
    },
    onBlur() {
      setTimeout(() => {
        this.focused = false;
      }, 200);
    },
    toggleSelectItem(item) {
      if (this.multiple) {
        if (this.isSelected(item)) {
          this.removeItem(item);
        } else {
          const newValue = this.returnObject ? item : item[this.valueKey];
          this.$emit("update:modelValue", [...this.modelValue, newValue]);
          this.$emit("select", newValue);
        }
        this.clearSearch();
      } else {
        const newValue = this.returnObject ? item : item[this.valueKey];
        this.$emit("update:modelValue", newValue);
        this.$emit("select", newValue);
        this.isTyping = false;
        this.focused = false;
        this.clearSearch();
      }
    },
    isSelected(item) {
      if (this.multiple) {
        return this.modelValue.some((selected) => selected === item[this.valueKey] || selected[this.valueKey] === item[this.valueKey]);
      }
      return this.modelValue === item[this.valueKey] || (this.modelValue && this.modelValue[this.valueKey] === item[this.valueKey]);
    },
    removeItem(item) {
      this.$emit(
        "update:modelValue",
        this.modelValue.filter((selected) => selected !== item[this.valueKey] && selected[this.valueKey] !== item[this.valueKey])
      );
    },
    clearSelection() {
      this.$emit("update:modelValue", this.multiple ? [] : null);
      this.searchTerm = "";
      this.isTyping = false;
      this.focused = false;
    },
    clearSearch() {
      this.searchTerm = "";
      this.isTyping = false;
    },
  },
};
</script>
