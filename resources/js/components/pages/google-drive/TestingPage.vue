<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Google Drive Testing Page</h1>

      <!-- Connection Test Section -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
          <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Connection Testsss
        </h2>
        <button
          @click="testConnection"
          :disabled="testing"
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ testing ? 'Testing...' : 'Test Connection' }}
        </button>
        <div v-if="connectionResult" class="mt-4 p-4 rounded-lg" :class="connectionResult.success ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'">
          <p class="font-medium">{{ connectionResult.message || connectionResult.error }}</p>
          <p v-if="connectionResult.user_email" class="text-sm mt-1">Connected as: {{ connectionResult.user_email }}</p>
        </div>
      </div>

      <!-- File Upload Section -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
          <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
          </svg>
          File Upload
        </h2>

        <div class="space-y-4">
          <input
            type="file"
            ref="fileInput"
            @change="handleFileSelect"
            multiple
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          />

          <button
            @click="uploadFiles"
            :disabled="!selectedFiles.length || uploading"
            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ uploading ? 'Uploading...' : `Upload ${selectedFiles.length} file(s)` }}
          </button>
        </div>

        <!-- Upload Progress -->
        <div v-if="uploadProgress.length > 0" class="mt-6 space-y-3">
          <h3 class="font-medium text-gray-700">Upload Progress:</h3>
          <div v-for="(progress, index) in uploadProgress" :key="index" class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="font-medium">{{ progress.name }}</span>
              <span class="text-gray-600">{{ progress.percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="h-2 rounded-full transition-all duration-300"
                :class="progress.status === 'completed' ? 'bg-green-600' : progress.status === 'failed' ? 'bg-red-600' : 'bg-blue-600'"
                :style="{width: progress.percentage + '%'}"
              ></div>
            </div>
            <div v-if="progress.status === 'completed' && progress.result" class="text-sm text-gray-600">
              <a :href="progress.result.web_view_link" target="_blank" class="text-blue-600 hover:underline">View in Drive</a>
              <span class="mx-2">|</span>
              <a :href="progress.result.download_link" target="_blank" class="text-blue-600 hover:underline">Download</a>
              <span class="mx-2">|</span>
              <span>{{ formatFileSize(progress.result.size) }}</span>
              <span v-if="progress.result.upload_speed_mbps" class="mx-2">| Speed: {{ progress.result.upload_speed_mbps }} MB/s</span>
            </div>
            <div v-if="progress.status === 'failed'" class="text-sm text-red-600">
              Error: {{ progress.error }}
            </div>
          </div>
        </div>
      </div>

      <!-- Files List Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center justify-between">
          <span class="flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
            Files in Google Drive
          </span>
          <button
            @click="listFiles"
            :disabled="loadingFiles"
            class="px-4 py-1 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
          >
            {{ loadingFiles ? 'Loading...' : 'Refresh' }}
          </button>
        </h2>

        <div v-if="files.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="file in files" :key="file.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ file.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatFileSize(file.size) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ file.mime_type }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(file.created_time) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <a :href="file.web_view_link" target="_blank" class="text-blue-600 hover:underline">View</a>
                  <a :href="file.download_link" target="_blank" class="text-green-600 hover:underline">Download</a>
                  <button @click="deleteFile(file.id)" class="text-red-600 hover:underline">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else-if="!loadingFiles" class="text-center py-8 text-gray-500">
          No files found. Upload some files to get started!
        </div>
      </div>

      <!-- Quick Test Buttons -->
      <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Quick Tests</h2>
        <div class="flex flex-wrap gap-3">
          <button
            @click="testGlobalUpload"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
          >
            Test Global Upload Function
          </button>
          <button
            @click="uploadTextFile"
            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition"
          >
            Upload Text Content
          </button>
          <button
            @click="getStats"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
          >
            Get Service Stats
          </button>
        </div>
        <div v-if="quickTestResult" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <pre class="text-sm">{{ JSON.stringify(quickTestResult, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import dashboardAxios from '@/api/dashboardAxios'

// State
const testing = ref(false)
const connectionResult = ref(null)
const selectedFiles = ref([])
const uploading = ref(false)
const uploadProgress = ref([])
const files = ref([])
const loadingFiles = ref(false)
const fileInput = ref(null)
const quickTestResult = ref(null)

// Get app instance to access global properties
const app = getCurrentInstance().appContext.app

// Methods
const testConnection = async () => {
  testing.value = true
  connectionResult.value = null
  try {
    const response = await dashboardAxios.get('/google-drive/test-connection')
    connectionResult.value = response
  } catch (error) {
    connectionResult.value = {
      success: false,
      error: error.response?.message || error.message
    }
  } finally {
    testing.value = false
  }
}

const handleFileSelect = (event) => {
  selectedFiles.value = Array.from(event.target.files)
}

const uploadFiles = async () => {
  if (selectedFiles.value.length === 0) return

  uploading.value = true
  uploadProgress.value = []

  for (const file of selectedFiles.value) {
    const progressItem = {
      name: file.name,
      percentage: 0,
      status: 'uploading',
      result: null,
      error: null
    }
    uploadProgress.value.push(progressItem)

    const formData = new FormData()
    formData.append('file', file)

    try {
      // Simulate progress updates
      const progressInterval = setInterval(() => {
        if (progressItem.percentage < 90) {
          progressItem.percentage += Math.random() * 20
        }
      }, 200)

      const response = await dashboardAxios.post('/google-drive/upload', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: (progressEvent) => {
          progressItem.percentage = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        }
      })

      clearInterval(progressInterval)
      progressItem.percentage = 100
      progressItem.status = 'completed'
      progressItem.result = response.data

    } catch (error) {
      progressItem.percentage = 100
      progressItem.status = 'failed'
      progressItem.error = error.response?.message || error.message
    }
  }

  uploading.value = false
  selectedFiles.value = []
  if (fileInput.value) {
    fileInput.value.value = ''
  }

  // Refresh file list after upload
  await listFiles()
}

const listFiles = async () => {
  loadingFiles.value = true
  try {
    const response = await dashboardAxios.get('/google-drive/list')
    if (response.success) {
      files.value = response.files
    }
  } catch (error) {
    console.error('Failed to list files:', error)
    files.value = []
  } finally {
    loadingFiles.value = false
  }
}

const deleteFile = async (fileId) => {
  if (!confirm('Are you sure you want to delete this file?')) return

  try {
    const response = await dashboardAxios.delete(`/google-drive/delete/${fileId}`)
    if (response.success) {
      await listFiles()
    }
  } catch (error) {
    alert('Failed to delete file: ' + (error.response?.message || error.message))
  }
}

const testGlobalUpload = async () => {
  // Create a test file
  const blob = new Blob(['This is a test file uploaded via global function'], { type: 'text/plain' })
  const file = new File([blob], 'test-global-upload.txt', { type: 'text/plain' })

  try {
    quickTestResult.value = { status: 'Uploading via global function...' }
    const result = await app.config.globalProperties.$uploadToGoogleDrive(file, {
      onProgress: (progress) => {
        quickTestResult.value = { status: 'Uploading...', progress: progress + '%' }
      }
    })
    quickTestResult.value = result
    await listFiles()
  } catch (error) {
    quickTestResult.value = { error: error.message }
  }
}

const uploadTextFile = async () => {
  try {
    quickTestResult.value = { status: 'Uploading text content...' }
    const response = await dashboardAxios.post('/google-drive/upload-content', {
      content: 'This is a test text file created at ' + new Date().toISOString(),
      filename: 'test-content-' + Date.now() + '.txt',
      mime_type: 'text/plain'
    })
    quickTestResult.value = response
    await listFiles()
  } catch (error) {
    quickTestResult.value = { error: error.response?.message || error.message }
  }
}

const getStats = async () => {
  try {
    const response = await dashboardAxios.get('/google-drive/stats')
    quickTestResult.value = response
  } catch (error) {
    quickTestResult.value = { error: error.response?.message || error.message }
  }
}

// Utility functions
const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleString()
}

// Load files on mount
onMounted(() => {
  testConnection()
  listFiles()
})
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>