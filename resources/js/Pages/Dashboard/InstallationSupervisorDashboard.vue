<template>
  <AppLayout title="Installation Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Installation Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Installation Progress by Supervisor</h3>
            
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-5 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Date From</label>
                <input 
                  type="date" 
                  v-model="filters.date_from"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Date To</label>
                <input 
                  type="date" 
                  v-model="filters.date_to"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Supervisor</label>
                <select 
                  v-model="filters.supervisor_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Supervisors</option>
                  <option v-for="supervisor in supervisors" :key="supervisor.id" :value="supervisor.id">
                    {{ supervisor.name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Installation Status</label>
                <select 
                  v-model="filters.installation_status"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Statuses</option>
                  <option v-for="(label, key) in installation_statuses" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
              </div>
              
              <div class="flex items-end">
                <button 
                  type="submit"
                  class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
                >
                  Apply Filters
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
          <div v-for="(label, key) in installation_statuses" :key="key" 
               class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center"
                       :class="getStatusColor(key)">
                    <span class="text-white font-bold text-sm">{{ key.charAt(0).toUpperCase() }}</span>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">{{ label }}</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ grand_total[key] || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Supervisor Matrix Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Supervisor Performance Matrix
            </h3>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Supervisor
                    </th>
                    <th v-for="(label, key) in installation_statuses" :key="key"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ label }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="row in supervisor_matrix" :key="row.supervisor_id" 
                      class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ row.supervisor_name }}
                    </td>
                    <td v-for="(label, key) in installation_statuses" :key="key"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="getCountBadgeColor(row[key])">
                        {{ row[key] || 0 }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ row.total || 0 }}
                      </span>
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      Grand Total
                    </td>
                    <td v-for="(label, key) in installation_statuses" :key="key"
                        class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                        {{ grand_total[key] || 0 }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        {{ grand_total.total || 0 }}
                      </span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- Progress Bar Chart -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Installation Progress Overview
            </h3>
            
            <div class="space-y-4">
              <div v-for="row in supervisor_matrix" :key="row.supervisor_id" class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-gray-900">{{ row.supervisor_name }}</span>
                  <span class="text-sm text-gray-500">Total: {{ row.total }}</span>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-4">
                  <div class="h-4 rounded-full flex">
                    <div v-if="row.total > 0"
                         class="bg-yellow-400 rounded-l-full flex items-center justify-center text-xs text-white font-bold"
                         :style="{ width: `${(row.team_assigned / row.total) * 100}%` }">
                      {{ row.team_assigned > 0 ? row.team_assigned : '' }}
                    </div>
                    <div v-if="row.total > 0"
                         class="bg-gray-500 flex items-center justify-center text-xs text-white font-bold"
                         :style="{ width: `${(row.installation_started / row.total) * 100}%` }">
                      {{ row.installation_started > 0 ? row.installation_started : '' }}
                    </div>
                    <div v-if="row.total > 0"
                         class="bg-purple-500 flex items-center justify-center text-xs text-white font-bold"
                         :style="{ width: `${(row.photo_upload_complete / row.total) * 100}%` }">
                      {{ row.photo_upload_complete > 0 ? row.photo_upload_complete : '' }}
                    </div>
                    <div v-if="row.total > 0"
                         class="bg-green-500 rounded-r-full flex items-center justify-center text-xs text-white font-bold"
                         :style="{ width: `${(row.supervisor_approved / row.total) * 100}%` }">
                      {{ row.supervisor_approved > 0 ? row.supervisor_approved : '' }}
                    </div>
                      <div v-if="row.total > 0"
                         class="bg-blue-500 flex items-center justify-center text-xs text-white font-bold"
                         :style="{ width: `${(row.installation_complete / row.total) * 100}%` }">
                      {{ row.installation_complete > 0 ? row.installation_complete : '' }}
                    </div>
                  </div>
                </div>
                
                <div class="flex justify-between mt-2 text-xs text-gray-600">
                  <span class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-400 rounded mr-1"></div>
                    Team Assigned
                  </span>
                  <span class="flex items-center">
                    <div class="w-3 h-3 bg-gray-500 rounded mr-1"></div>
                    Installation Started
                  </span>
                  <span class="flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded mr-1"></div>
                    Photo Upload Complete
                  </span>
                  <span class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded mr-1"></div>
                    Supervisor Approved
                  </span>
                  <span class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded mr-1"></div>
                    Installation Complete
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Team Workload Dashboard -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Team Workload Dashboard
            </h3>

            <!-- Subcom Filter -->
            <form @submit.prevent="filterTeam" class="mb-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Subcontractor</label>
                  <select 
                    v-model="filters.subcom_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="">All Subcontractors</option>
                    <option v-for="subcom in subcoms" :key="subcom.id" :value="subcom.id">
                      {{ subcom.name }}
                    </option>
                  </select>
                </div>
                <div class="flex items-end">
                  <button 
                    type="submit"
                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500"
                  >
                    Filter Team
                  </button>
                </div>
              </div>
            </form>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Subcontractor
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total Customers
                    </th>
                    <th v-for="(label, key) in installation_statuses" :key="key"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ label }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(row, index) in team_workload_matrix" :key="row.subcom_id"
                      :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ row.subcom_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ row.total_customers || 0 }}
                      </span>
                    </td>
                    <td v-for="(label, key) in installation_statuses" :key="key"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="getInstallationStatusBadgeColor(key, row[key])">
                        {{ row[key] || 0 }}
                      </span>
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-100">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      Grand Total
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        {{ team_grand_total?.total_customers || 0 }}
                      </span>
                    </td>
                    <td v-for="(label, key) in installation_statuses" :key="key"
                        class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                        {{ team_grand_total?.[key] || 0 }}
                      </span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- Installation Status Legend -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Installation Status Legend
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div v-for="(label, key) in installation_statuses" :key="key" class="flex items-center">
                <div class="w-4 h-4 rounded mr-3" :class="getStatusColor(key)"></div>
                <span class="text-sm font-medium text-gray-700">{{ label }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  installation_statuses: Object,
  supervisor_matrix: Array,
  grand_total: Object,
  team_workload_matrix: Array,
  team_grand_total: Object,
  subcoms: Array,
  supervisors: Array,
  supervisor_id: [String, Number],
  installation_status: String,
  subcom_id: [String, Number],
  date_from: String,
  date_to: String,
})

const filters = ref({
  date_from: props.date_from || '',
  date_to: props.date_to || '',
  supervisor_id: props.supervisor_id || '',
  installation_status: props.installation_status || '',
  subcom_id: props.subcom_id || '',
})

const getStatusColor = (status) => {
  const colors = {
    'team_assigned': 'bg-yellow-500',
    'installation_complete': 'bg-blue-500',
    'photo_upload_complete': 'bg-purple-500',
    'supervisor_approved': 'bg-green-500'
  }
  return colors[status] || 'bg-gray-500'
}

const getCountBadgeColor = (count) => {
  if (count === 0) return 'bg-gray-100 text-gray-800'
  if (count <= 5) return 'bg-green-100 text-green-800'
  if (count <= 15) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

const getInstallationStatusBadgeColor = (status, count) => {
  if (count === 0) return 'bg-gray-100 text-gray-800'
  
  const colors = {
    'team_assigned': 'bg-yellow-100 text-yellow-800',
    'installation_complete': 'bg-blue-100 text-blue-800',
    'photo_upload_complete': 'bg-purple-100 text-purple-800',
    'supervisor_approved': 'bg-green-100 text-green-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const applyFilters = () => {
  router.get(route('installation-supervisor-dashboard'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const filterTeam = () => {
  router.get(route('installation-supervisor-dashboard'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>
