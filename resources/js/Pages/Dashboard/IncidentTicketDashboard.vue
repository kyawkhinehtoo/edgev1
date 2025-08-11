<template>
  <AppLayout title="Rectification Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Rectification Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Total Ticket Within Filter Date Range</h3>
            
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

        <!-- Main Dashboard Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Incident Ticket Status Matrix
            </h3>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                      Supervisor Name
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Total Ticket
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Supervisor Assign Tickets
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Team Assigned Tickets
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Pending Team Assign
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Photo upload completed
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Photo Approved
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Resolve Opened
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Ticket Closed
                    </th>
                  </tr>
                  
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(row, index) in supervisor_matrix" :key="row.supervisor_id" 
                      :class="getSupervisorRowColor(index)">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">
                      {{ row.supervisor_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ row.total_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                      {{ row.supervisor_assign_tickets }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                      {{ row.team_assigned_tickets }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                      {{ row.pending_team_assign }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                      {{ row.photo_upload_completed }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                      {{ row.photo_approved }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                      {{ row.resolve_opened }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                      {{ row.ticket_closed }}
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-100">
                  <tr class="font-bold">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 border-r">
                      Grand Total
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-blue-600">
                      {{ grand_total.total_ticket }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ grand_total.supervisor_assign_tickets }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ grand_total.team_assigned_tickets }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ grand_total.pending_team_assign }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ grand_total.photo_upload_completed }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ grand_total.photo_approved }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ grand_total.resolve_opened }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ grand_total.ticket_closed }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- Team Workload Dashboard -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Team Workload Dashboard (Subcontractors)
            </h3>
            
            <!-- Subcom Filter -->
            <div class="mb-4">
              <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500"
                  >
                    Filter Team
                  </button>
                </div>
              </form>
            </div>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                      Subcom Name
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                      Total Tasks
                    </th>
                    <th colspan="4" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-l">
                      Task Status
                    </th>
                    <th colspan="2" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-l">
                      Incident Status
                    </th>
                  </tr>
                  <tr class="bg-blue-50">
                    <th class="px-3 py-2 text-center text-xs text-gray-600">Team Assigned</th>
                    <th class="px-3 py-2 text-center text-xs text-gray-600">Rectification Ongoing</th>
                    <th class="px-3 py-2 text-center text-xs text-gray-600">Photo Upload Complete</th>
                    <th class="px-3 py-2 text-center text-xs text-gray-600">Photo Upload Rejected</th>
                    <th class="px-3 py-2 text-center text-xs text-gray-600">Photo Upload Approved</th>
                    <th class="px-3 py-2 text-center text-xs text-gray-600">WIP</th>
                    <th class="px-3 py-2 text-center text-xs text-gray-600">Closed</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(row, index) in team_workload_matrix" :key="row.subcom_id" 
                      :class="getSubcomRowColor(index)">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">
                      {{ row.subcom_name }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold text-blue-600">
                      {{ row.total_tasks }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ row.task_wip }}
                      </span>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ row.task_photo_complete }}
                      </span>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ row.task_photo_rejected }}
                      </span>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ row.task_approved }}
                      </span>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ row.incident_wip }}
                      </span>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ row.incident_closed }}
                      </span>
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-100">
                  <tr class="font-bold">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 border-r">
                      Grand Total
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold text-blue-600">
                      {{ team_grand_total.total_tasks }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ team_grand_total.task_wip }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ team_grand_total.task_photo_complete }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ team_grand_total.task_photo_rejected }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ team_grand_total.task_approved }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ team_grand_total.incident_wip }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-bold">
                      {{ team_grand_total.incident_closed }}
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
              Status Legend
            </h3>
            
            <!-- Supervisor Dashboard Legend -->
            <div class="mb-6">
              <h4 class="text-md font-medium text-gray-800 mb-3">Supervisor Dashboard</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-blue-900">Supervisor Assign (Status 6)</div>
                  <div class="text-xs text-blue-700 mt-1">Tickets assigned to supervisor</div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-green-900">Team Assigned (Status 2)</div>
                  <div class="text-xs text-green-700 mt-1">Tickets assigned to field team</div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-yellow-900">Pending Team Assign (Status 1)</div>
                  <div class="text-xs text-yellow-700 mt-1">Awaiting team assignment</div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-purple-900">Photo Upload Completed</div>
                  <div class="text-xs text-purple-700 mt-1">Tasks marked as completed</div>
                </div>
                
                <div class="bg-indigo-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-indigo-900">Photo Approved</div>
                  <div class="text-xs text-indigo-700 mt-1">Tasks approved by supervisor</div>
                </div>
                
                <div class="bg-orange-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-orange-900">Resolve Opened (Status 5)</div>
                  <div class="text-xs text-orange-700 mt-1">Resolved but still open</div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-gray-900">Ticket Closed (Status 3)</div>
                  <div class="text-xs text-gray-700 mt-1">Completed and closed tickets</div>
                </div>
              </div>
            </div>

            <!-- Team Workload Legend -->
            <div>
              <h4 class="text-md font-medium text-gray-800 mb-3">Team Workload Dashboard</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-yellow-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-yellow-900">Task WIP (Status 1)</div>
                  <div class="text-xs text-yellow-700 mt-1">Tasks currently in progress</div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-green-900">Photo Upload Complete (Status 4)</div>
                  <div class="text-xs text-green-700 mt-1">Tasks with photos uploaded</div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-red-900">Photo Upload Rejected (Status 5)</div>
                  <div class="text-xs text-red-700 mt-1">Tasks with rejected photos</div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-blue-900">Supervisor Approved (Status 2)</div>
                  <div class="text-xs text-blue-700 mt-1">Tasks approved by supervisor</div>
                </div>
                
                <div class="bg-orange-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-orange-900">Incident WIP (Status 1)</div>
                  <div class="text-xs text-orange-700 mt-1">Incidents currently in progress</div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-gray-900">Incident Closed (Status 3)</div>
                  <div class="text-xs text-gray-700 mt-1">Completed incidents</div>
                </div>
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
  supervisor_matrix: Array,
  grand_total: Object,
  team_workload_matrix: Array,
  team_grand_total: Object,
  subcoms: Array,
  supervisors: Array,
  supervisor_id: [String, Number],
  subcom_id: [String, Number],
  date_from: String,
  date_to: String,
})

const filters = ref({
  date_from: props.date_from || '',
  date_to: props.date_to || '',
  supervisor_id: props.supervisor_id || '',
  subcom_id: props.subcom_id || '',
})

const getSupervisorRowColor = (index) => {
  const colors = [
    'bg-yellow-100',  // Supervisor A
    'bg-gray-100',    // Supervisor B  
    'bg-green-100',   // Supervisor C
    'bg-orange-100'   // Supervisor D
  ]
  return colors[index % colors.length] || 'bg-white'
}

const getSubcomRowColor = (index) => {
  const colors = [
    'bg-blue-50',    // Subcom 1
    'bg-green-50',   // Subcom 2
    'bg-purple-50',  // Subcom 3
    'bg-pink-50',    // Subcom 4
    'bg-indigo-50',  // Subcom 5
    'bg-yellow-50'   // Subcom 6
  ]
  return colors[index % colors.length] || 'bg-white'
}

const applyFilters = () => {
  router.get(route('incident-ticket-dashboard'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>

<style scoped>
.table-cell {
  border: 1px solid #e5e7eb;
}
</style>
