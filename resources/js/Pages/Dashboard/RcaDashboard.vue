<template>
  <AppLayout title="RCA Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Root Cause Analysis Dashboard (Maintenance)
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Maintenance Incident Analysis</h3>
            
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-sm">T</span>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Incidents</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ grand_total.total_incidents || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

        


          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-sm">R</span>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Resolved</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ grand_total.resolved_incidents || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-sm">C</span>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Closed</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ grand_total.closed_incidents || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
<!-- 
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-sm">P</span>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ grand_total.pending_incidents || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div> -->
        </div>

        <!-- Top Root Causes Chart -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Top 5 Root Causes by Incident Count
            </h3>
            
            <div class="space-y-4">
              <div v-for="(item, index) in top_root_causes" :key="item.root_cause_id" class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-gray-900">{{ index + 1 }}. {{ item.root_cause_name }}</span>
                  <span class="text-sm text-gray-500">Total: {{ item.total_incidents }}</span>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-4" v-if="grand_total.total_incidents > 0">
                  <div class="h-4 rounded-full flex"
                       :style="{ width: `${(item.total_incidents / grand_total.total_incidents) * 100}%` }">
                    <div class="bg-blue-500 rounded-full flex items-center justify-center text-xs text-white font-bold w-full">
                      {{ item.total_incidents > 0 ? item.total_incidents : '' }}
                    </div>
                  </div>
                </div>
                
                <div class="mt-2 text-xs text-gray-600">
                  {{ grand_total.total_incidents > 0 ? ((item.total_incidents / grand_total.total_incidents) * 100).toFixed(1) : 0 }}% of total incidents
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- RCA Matrix Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Root Cause Analysis Matrix
            </h3>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Root Cause
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total Incidents
                    </th>
                    
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Resolved
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Closed
                    </th>
                 
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(row, index) in rca_matrix" :key="row.root_cause_id"
                      :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ row.root_cause_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ row.total_incidents }}
                      </span>
                    </td>
                   
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ row.resolved_incidents }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ row.closed_incidents }}
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
                        {{ grand_total.total_incidents }}
                      </span>
                    </td>
                   
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-200 text-green-800">
                        {{ grand_total.resolved_incidents }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                        {{ grand_total.closed_incidents }}
                      </span>
                    </td>
                   
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- Status Legend -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Incident Status Legend
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
             
              <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                <span class="text-sm font-medium text-gray-700">Resolved</span>
              </div>
              <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-500 rounded mr-3"></div>
                <span class="text-sm font-medium text-gray-700">Closed</span>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  rca_matrix: Array,
  grand_total: Object,
  top_root_causes: Array,
  date_from: String,
  date_to: String,
})

const filters = ref({
  date_from: props.date_from || '',
  date_to: props.date_to || '',
})

const applyFilters = () => {
  router.get(route('rca-dashboard'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>
