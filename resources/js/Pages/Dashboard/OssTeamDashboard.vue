<template>
  <AppLayout title="OSS Team Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        OSS Team Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Ticket Count by Zone (City Filter)</h3>
            
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
                <label class="block text-sm font-medium text-gray-700">City</label>
                <select 
                  v-model="filters.city_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Cities</option>
                  <option v-for="city in cities" :key="city.id" :value="city.id">
                    {{ city.name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Zone</label>
                <select 
                  v-model="filters.zone_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Zones</option>
                  <option v-for="zone in zones" :key="zone.id" :value="zone.id">
                    {{ zone.name }}
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

        <!-- Zone Summary Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Zone Summary</h3>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Zone
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      New Installation Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Rectification Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Suspend Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Plan Change Ticket
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="zone in zone_matrix" :key="zone.zone_id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      Zone {{ zone.zone_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                      {{ zone.total_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ zone.new_installation_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ zone.rectification_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ zone.suspend_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ zone.plan_change_ticket }}
                    </td>
                  </tr>
                  <!-- Grand Total Row -->
                  <tr class="bg-gray-100 font-semibold">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                      Grand Total
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                      {{ grand_total.total_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                      {{ grand_total.new_installation_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                      {{ grand_total.rectification_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                      {{ grand_total.suspend_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                      {{ grand_total.plan_change_ticket }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Township Detail Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Township Detail</h3>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Zone
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Township
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      New Installation Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Rectification Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Suspend Ticket
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Plan Change Ticket
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <template v-for="township in groupedTownships" :key="`${township.zone_name}-${township.township_name}`">
                    <!-- Zone Header Row -->
                    <tr v-if="township.isZoneHeader" class="bg-yellow-100">
                      <td colspan="7" class="px-6 py-3 text-sm font-bold text-gray-900">
                        Zone {{ township.zone_name }}
                      </td>
                    </tr>
                    <!-- Township Row -->
                    <tr v-else class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <!-- Empty for township rows -->
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ township.township_name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ township.total_ticket }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ township.new_installation_ticket }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ township.rectification_ticket }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ township.suspend_ticket }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ township.plan_change_ticket }}
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
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
  ticket_types: Object,
  zone_matrix: Array,
  township_matrix: Array,
  grand_total: Object,
  zones: Array,
  cities: Array,
  city_id: [String, Number],
  zone_id: [String, Number],
  date_from: String,
  date_to: String,
})

const filters = ref({
  date_from: props.date_from || '',
  date_to: props.date_to || '',
  city_id: props.city_id || '',
  zone_id: props.zone_id || '',
})

// Group townships by zone with zone headers
const groupedTownships = computed(() => {
  const result = []
  let currentZone = null
  
  // Sort townships by zone name first
  const sortedTownships = [...props.township_matrix].sort((a, b) => {
    if (a.zone_name !== b.zone_name) {
      return a.zone_name.localeCompare(b.zone_name)
    }
    return a.township_name.localeCompare(b.township_name)
  })
  
  sortedTownships.forEach(township => {
    if (currentZone !== township.zone_name) {
      // Add zone header
      result.push({
        ...township,
        isZoneHeader: true
      })
      currentZone = township.zone_name
    }
    
    // Add township data
    result.push({
      ...township,
      isZoneHeader: false
    })
  })
  
  return result
})

const applyFilters = () => {
  router.get(route('oss-team-dashboard'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>
