<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 mr-4">
              <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-home text-blue-600 text-2xl"></i>
              </div>
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Welcome, {{ $page.props.auth.user.name }}!
              </h3>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ welcomeMessage }}
              </p>
            </div>
          </div>
        </div>

        <!-- Quick Access Cards -->
       
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" >
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex flex-col items-center">
                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                    <i class="fas fa-users text-indigo-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2"> Total  {{ $page.props.customers.length }} Customers</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-4">
                    Manage your customers and their services
                    </p>
                    <Link :href="route('customer.index')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    View Customers
                    </Link>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex flex-col items-center">
                  <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-purple-600"></i>
                  </div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Reports</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-4">
                    View POP Information
                  </p>
                  <Link :href="route('incidentReport')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    View Reports
                  </Link>
                </div>
              </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex flex-col items-center">
                  <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mb-4">
                    <i class="fas fa-file-invoice-dollar text-green-600"></i>
                  </div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Billing</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-4">
                    Generate and manage customer bills
                  </p>
                  <Link :href="route('showbill')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    View Bills
                  </Link>
                </div>
              </div>
             
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" v-if="$page.props.login_type == 'isp'">
         
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" v-if="$page.props.login_type == 'subcon'">
            
        </div>
       
         
        

        <!-- Account Information -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Account Information</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">User Details</h4>
                <div class="mt-2 border-t border-gray-200 dark:border-gray-700 pt-2">
                  <div class="flex justify-between py-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</span>
                  </div>
                  <div class="flex justify-between py-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Email:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.email }}</span>
                  </div>
                  <div class="flex justify-between py-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Account Type:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $page.props.login_type }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="$page.props.isp">
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">ISP Information</h4>
                <div class="mt-2 border-t border-gray-200 dark:border-gray-700 pt-2">
                  <div class="flex justify-between py-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.isp.name }}</span>
                  </div>
                  <div class="flex justify-between py-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Domain:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.isp.domain }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="$page.props.partner">
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Partner Information</h4>
                <div class="mt-2 border-t border-gray-200 dark:border-gray-700 pt-2">
                  <div class="flex justify-between py-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.partner.name }}</span>
                  </div>
                  <div class="flex justify-between py-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Domain:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.partner.domain }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout,
    Link,
  },
  props: {
    user: Object,
    customers: Object
  },
  computed: {
    welcomeMessage() {
      const loginType = this.$page.props.login_type;
      
      if (loginType === 'isp') {
        return `Welcome to your ISP dashboard. Manage your customers, complaints, and services.`;
      } else if (loginType === 'partner') {
        return `Welcome to your Partner dashboard. Access your services and manage your operations.`;
      } else if (loginType === 'subcom') {
        return `Welcome to your Subcontractor dashboard. View your assigned tasks and reports.`;
      } else {
        return `Welcome to your dashboard. Access your account and manage your services.`;
      }
    }
  }
}
</script>