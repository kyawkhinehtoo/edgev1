<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">ISP Details</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
          <Link :href="route('isps.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to ISPs
          </Link>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">ISP Information</h3>
              <div v-if="isp.logo" class="mb-4">
                <img :src="'/storage/' + isp.logo" :alt="isp.name + ' Logo'" class="h-32 w-auto">
              </div>  
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <span class="font-semibold">Domain:</span>
                  <span>{{ isp.domain || 'N/A' }}</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Name</p>
                  <p class="mt-1">{{ isp.name }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Short Code</p>
                  <p class="mt-1">{{ isp.short_code }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Contact Person</p>
                  <p class="mt-1">{{ isp.contact_person }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Phone</p>
                  <p class="mt-1">{{ isp.phone }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Email</p>
                  <p class="mt-1">{{ isp.email }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Service Type</p>
                  <p class="mt-1">{{ isp.service_type }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Bandwidth Capacity</p>
                  <p class="mt-1">{{ isp.bandwidth_capacity }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Website</p>
                  <p class="mt-1">
                    <a :href="isp.website" target="_blank" class="text-blue-600 hover:text-blue-800">
                      {{ isp.website }}
                    </a>
                  </p>
                </div>
                <div class="col-span-2">
                  <p class="text-sm font-medium text-gray-500">Address</p>
                  <p class="mt-1">{{ isp.address }}</p>
                </div>
                <div class="col-span-2">
                  <p class="text-sm font-medium text-gray-500">Description</p>
                  <p class="mt-1">{{ isp.description }}</p>
                </div>
              </div>
            </div>

            <div class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">ISP Users</h3>
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="user in isp.users" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="[
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        !user.disabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                      ]">
                        {{ user.disabled ? 'Disabled' : 'Enabled' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm">
                        <div>IP: {{ user.last_login_ip || 'N/A' }}</div>
                        <div>Time: {{ user.last_login_time || 'N/A' }}</div>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="isp.users.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No users found for this ISP</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    AppLayout,
    Link
  },
  props: {
    isp: Object
  }
}
</script>
