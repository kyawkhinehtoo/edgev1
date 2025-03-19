<template>
    <div>
      <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
      <div class="relative">
        <div 
          class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-md border-gray-300 hover:border-indigo-500 transition-colors duration-200 cursor-pointer bg-gray-50"
          @click="triggerFileInput"
          @dragover.prevent="onDragOver"
          @dragleave.prevent="onDragLeave"
          @drop.prevent="onDrop"
          :class="{ 'border-indigo-500 bg-indigo-50': isDragging }"
          :disabled="disabled"
        >
          <div v-if="!imagePreview" class="space-y-1 text-center">
            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
            <div class="text-sm text-gray-600">
              <span class="font-medium text-indigo-600 hover:text-indigo-500">
                {{ uploadText }}
              </span>
              <span> or drag and drop</span>
            </div>
            <p class="text-xs text-gray-500">
              PNG, JPG, GIF up to 10MB
            </p>
          </div>
          <div v-else class="relative w-full h-full">
            <img :src="imagePreview" :alt="`${label} Preview`" class="object-contain w-full h-full rounded-md" />
            <div class="absolute top-1 right-1 flex space-x-1">
              <button 
                @click.stop="openInNewTab" 
                type="button" 
                class="bg-blue-500 text-white rounded-full p-1 shadow-md hover:bg-blue-600 transition-colors duration-200"
                title="View in new tab"
              >
                <i class="fas fa-external-link-alt text-xs"></i>
              </button>
              <button 
                @click.stop="removeImage" 
                type="button" 
                class="bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors duration-200"
                title="Remove image"
              >
                <i class="fas fa-times text-xs"></i>
              </button>
            </div>
          </div>
        </div>
        <input 
          :ref="setFileInputRef"
          type="file" 
          @change="handleImageUpload" 
          accept="image/*"
          class="hidden"
          :disabled="disabled" 
          :id="id"
        />
      </div>
      <p v-if="error" class="mt-2 text-sm text-red-500">{{ error }}</p>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  
  const props = defineProps({
    modelValue: {
      type: [File, null],
      default: null
    },
    imageUrl: {
      type: String,
      default: null
    },
    existingImage: {
      type: String,
      default: null
    },
    label: {
      type: String,
      default: ''
    },
    uploadText: {
      type: String,
      default: 'Click to upload'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    error: {
      type: String,
      default: ''
    },
    id: {
      type: String,
      default: () => `file-upload-${Math.random().toString(36).substring(2, 9)}`
    }
  });
  
  const emit = defineEmits(['update:modelValue', 'update:imageUrl']);
  
  const fileInput = ref(null);
  const isDragging = ref(false);
  const imagePreview = ref(props.imageUrl || props.existingImage);
  
  // Watch for changes in existingImage prop
  watch(() => props.existingImage, (newValue) => {
    if (newValue) {
      // Fix the URL path by removing any potential /customer/{id}/ prefix
      const fixedUrl = newValue.replace(/\/customer\/\d+\//, '/');
      imagePreview.value = fixedUrl;
      emit('update:imageUrl', fixedUrl);
    }
  });
  
  // Initialize with existing image on mount
  onMounted(() => {
    if (props.existingImage) {
      // Fix the URL path by removing any potential /customer/{id}/ prefix
      const fixedUrl = props.existingImage.replace(/\/customer\/\d+\//, '/');
      imagePreview.value = fixedUrl;
      emit('update:imageUrl', fixedUrl);
    }
  });
  
  function setFileInputRef(el) {
    fileInput.value = el;
  }
  
  function triggerFileInput() {
    if (!props.disabled) {
      fileInput.value.click();
    }
  }
  
  function onDragOver(event) {
    isDragging.value = true;
  }
  
  function onDragLeave(event) {
    isDragging.value = false;
  }
  
  function onDrop(event) {
    isDragging.value = false;
    if (props.disabled) return;
    
    const files = event.dataTransfer.files;
    if (files.length > 0) {
      const file = files[0];
      if (file.type.startsWith('image/')) {
        handleImageUploadWithFile(file);
      }
    }
  }
  
  function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    handleImageUploadWithFile(file);
  }
  
  function handleImageUploadWithFile(file) {
    // Check file size (limit to 10MB)
    if (file.size > 10 * 1024 * 1024) {
      alert('File size exceeds 10MB limit');
      return;
    }
    
    // Emit the file to parent
    emit('update:modelValue', file);
    
    // Create a preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
      emit('update:imageUrl', e.target.result);
    };
    reader.readAsDataURL(file);
  }
  
  function openInNewTab() {
    if (imagePreview.value) {
      window.open(imagePreview.value, '_blank');
    }
  }
  
  function removeImage(event) {
    event.stopPropagation(); // Prevent triggering the parent click event
    emit('update:modelValue', null);
    imagePreview.value = null;
    emit('update:imageUrl', null);
  }
  </script>