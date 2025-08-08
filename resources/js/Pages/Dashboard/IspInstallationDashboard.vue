<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">ISP Installation Dashboard</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-4" >
        
        <!-- ISP Dashboard Navigation -->
        <div class="bg-white shadow-xl sm:rounded-lg p-4">
          <div class="flex flex-wrap gap-2">
            <Link 
              :href="route('isp.dashboard')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
              </svg>
              Main Dashboard
            </Link>
            
            <div class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2L3 8v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V8l-7-6z" clip-rule="evenodd"/>
              </svg>
              Installation Dashboard
              <span class="ml-2 bg-green-500 text-xs px-2 py-1 rounded-full">Current</span>
            </div>
            
            <Link 
              :href="route('isp.maintenance.dashboard')"
              class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
              </svg>
              Maintenance Dashboard
            </Link>
          </div>
        </div>
        
        <!-- Filter Section -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Service Type Filter -->
            <div>
              <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                <span>Service Type Filter</span>
                <span class="inline-block bg-indigo-100 text-indigo-600 text-xs px-2 py-1 rounded">Multi-select</span>
              </h3>
              <div class="flex flex-wrap gap-2">
                <label v-for="type in ['ALL', 'FTTH', 'DIA', 'DPLC', 'IPLC']" :key="type" class="flex items-center cursor-pointer bg-gray-50 px-2 py-1 rounded hover:bg-indigo-50 transition">
                  <input 
                    type="checkbox" 
                    :checked="service_types.includes(type)"
                    @change="handleServiceTypeChange(type)"
                    class="mr-2 accent-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <span class="text-sm font-medium text-gray-900">{{ type }}</span>
                </label>
              </div>
              <div class="mt-2 text-xs text-gray-500">
                <span class="font-semibold">Selected:</span> 
                <span class="text-indigo-600">{{ selectedServiceNames.join(', ') }}</span>
              </div>
            </div>
             <!-- Installation Service Filter -->
            <div>
              <h3 class="text-lg font-bold mb-3">Installation Service</h3>
              <select 
                v-model="selected_installation_service" 
                @change="handleInstallationServiceChange"
                class="border rounded px-3 py-2 bg-white text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 w-full"
              >
                <option value="ALL">ALL</option>
                <option v-for="service in installation_services" :key="service.id" :value="service.id">
                  {{ service.name }}
                </option>
              </select>
              <div class="mt-2 text-xs text-gray-500">
                <span class="font-semibold">Selected:</span> 
                <span class="text-indigo-600">
                  {{ selected_installation_service === 'ALL' ? 'ALL' : 
                     (installation_services.find(s => s.id == selected_installation_service)?.name || selected_installation_service) }}
                </span>
              </div>
            </div>
            <!-- Date Range Filter -->
            <div class="col-span-2">
              <h3 class="text-lg font-bold mb-3">Date Range Filter</h3>
              <form class="inline-flex gap-2" @submit.prevent="applyDateFilter">

                <div class="flex items-center gap-2">
                  <label class="font-semibold text-sm">From:</label>
                  <input type="date" v-model="date_from" class="border rounded px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div class="flex items-center gap-2">
                  <label class="font-semibold text-sm">To:</label>
                  <input type="date" v-model="date_to" class="border rounded px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <button type="submit" class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition font-semibold shadow">Apply Filter</button>
              </form>
            </div>
           
          </div>
        </div>

        <!-- Installation Service Dashboard -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Customer Installation Service Dashboard</h3>
          
            <table class="min-w-full divide-y divide-gray-200 mb-8">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Installation Service</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Total Customers</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in matrix" :key="row.installation_service_id">
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ row.installation_service_name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ row.total }}</td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                  <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ grand_total.total }}</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-4 text-lg font-bold">Total Customers: {{ grand_total.total }}</div>
          </div>
        </div>

        <!-- Zone Dashboard -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-4">Zone Dashboard</h3>
            
            <!-- Port Sharing Services Filter -->
           

            <table class="min-w-full divide-y divide-gray-200 mb-8">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Zone</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Total Customers</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in zone_matrix" :key="row.zone_id">
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ row.zone_name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ row.total }}</td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                  <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ zone_grand_total.total }}</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-4 text-lg font-bold">Total Customers by Zone: {{ zone_grand_total.total }}</div>
          </div>
        </div>

        <!-- Township Dashboard -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-4">Township Dashboard</h3>
            
            <table class="min-w-full divide-y divide-gray-200 mb-8">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Township</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Total Customers</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in township_matrix" :key="row.township_id">
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ row.township_name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ row.total }}</td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                  <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ township_grand_total.total }}</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-4 text-lg font-bold">Total Customers by Township: {{ township_grand_total.total }}</div>
          </div>
        </div>

        <!-- SLA Performance Dashboard -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-4">SLA Performance Dashboard (FTTH Only)</h3>
            <p class="text-sm text-gray-600 mb-4">Shows installation performance against Service Level Agreement for FTTH customers</p>
            
            <table class="min-w-full divide-y divide-gray-200 mb-8">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Installation Service Name</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">No. of Customer</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Within SLA</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Over SLA</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in sla_matrix" :key="row.installation_service_id" 
                    :class="{'bg-yellow-50': row.over_sla > 0}">
                  <td class="px-6 py-4 whitespace-nowrap font-bold">
                    {{ row.installation_service_name }}
                    <span class="text-xs text-gray-500 block">({{ row.sla_hours }}h SLA)</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ row.total_customers }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ row.within_sla }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="row.over_sla > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      {{ row.over_sla }}
                    </span>
                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ row.over_sla }}
                    </span>
                  </td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                  <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ sla_grand_total.total_customers }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ sla_grand_total.within_sla }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="sla_grand_total.over_sla > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      {{ sla_grand_total.over_sla }}
                    </span>
                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ sla_grand_total.over_sla }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
              <div class="bg-blue-50 p-3 rounded">
                <div class="font-semibold text-blue-800">Total FTTH Installations:</div>
                <div class="text-xl font-bold text-blue-900">{{ sla_grand_total.total_customers }}</div>
              </div>
              <div class="bg-green-50 p-3 rounded">
                <div class="font-semibold text-green-800">Within SLA:</div>
                <div class="text-xl font-bold text-green-900">{{ sla_grand_total.within_sla }}</div>
                <div class="text-xs text-green-600">
                  {{ sla_grand_total.total_customers > 0 ? Math.round((sla_grand_total.within_sla / sla_grand_total.total_customers) * 100) : 0 }}% Performance
                </div>
              </div>
              <div class="bg-red-50 p-3 rounded">
                <div class="font-semibold text-red-800">Over SLA:</div>
                <div class="text-xl font-bold text-red-900">{{ sla_grand_total.over_sla }}</div>
                <div class="text-xs text-red-600">
                  {{ sla_grand_total.total_customers > 0 ? Math.round((sla_grand_total.over_sla / sla_grand_total.total_customers) * 100) : 0 }}% Over Target
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Customer Trends Chart -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Customer Trends by Installation Service (Monthly)</h3>
            <LineChart
              chart-id="installation-service-trends-chart"
              title="Total Customers by Installation Service Over Time"
              :labels="chartLabels"
              :datasets="getInstallationServiceDatasets()"
              :key="key"
            />
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import LineChart from '@/Components/LineChart.vue';
import { ref, computed } from 'vue';
import { router, useForm, Link } from "@inertiajs/vue3";
export default {
    name: 'IspInstallationDashboard',
    components: { AppLayout, LineChart, Link },
    props: {
        matrix: Object,
        status_types: Array,
        grand_total: Object,
        zone_matrix: Array,
        zone_grand_total: Object,
        zones: Array,
        township_matrix: Array,
        township_grand_total: Object,
        townships: Array,
        sla_matrix: Array,
        sla_grand_total: Object,
        installation_services: Array,
        service_types: {
            type: Array,
            default: () => ['ALL'],
        },
        selected_installation_service: {
            type: [String, Number],
            default: 'ALL',
        },
        months: Array,
        chart_data: Object,
        date_from: String,
        date_to: String,
    },
    setup(props, { emit }) {
        const date_from = ref(props.date_from || '');
        const date_to = ref(props.date_to || '');
        const service_types = ref([...props.service_types]);
        const selected_installation_service = ref(props.selected_installation_service || 'ALL');
        const key = ref(0);
        
        const chartLabels = computed(() => {
            return props.months.map(month => {
                const [year, monthNum] = month.split('-');
                const date = new Date(year, monthNum - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            });
        });

        const isAllSelected = computed(() => {
            return service_types.value.includes('ALL');
        });

        const selectedServiceNames = computed(() => {
            if (service_types.value.includes('ALL')) return ['ALL'];
            return service_types.value;
        });

        function getStatusColor(status) {
            const colors = {
                'active': '#10b981',
                'disabled': '#f59e0b',
                'suspense': '#ef4444',
                'terminate': '#6b7280',
            };
            return colors[status] || '#6366f1';
        }

        function getServiceColor(index) {
            const colors = [
                '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6',
                '#06b6d4', '#f97316', '#84cc16', '#ec4899', '#6b7280',
            ];
            return colors[index % colors.length];
        }

        function getInstallationServiceDatasets() {
            const firstMonth = props.months[0];
            const serviceNames = firstMonth && props.chart_data[firstMonth]
                ? Object.keys(props.chart_data[firstMonth])
                : [];
            return serviceNames.map((serviceName, index) => ({
                label: serviceName,
                data: props.months.map(month =>
                    props.chart_data[month] && props.chart_data[month][serviceName]
                        ? props.chart_data[month][serviceName]
                        : 0
                ),
                borderColor: getServiceColor(index),
                backgroundColor: getServiceColor(index) + '20',
                fill: false,
                tension: 0.4
            }));
        }

        function handleServiceTypeChange(serviceType) {
            if (serviceType === 'ALL') {
                service_types.value = ['ALL'];
            } else {
                // Remove 'ALL' if it exists and add the selected service type
                service_types.value = service_types.value.filter(type => type !== 'ALL');
                
                if (service_types.value.includes(serviceType)) {
                    service_types.value = service_types.value.filter(type => type !== serviceType);
                } else {
                    service_types.value.push(serviceType);
                }
                
                // If no service types are selected, default to ALL
                if (service_types.value.length === 0) {
                    service_types.value = ['ALL'];
                }
            }
            applyFilter();
        }

        function handleInstallationServiceChange() {
            applyInstallationServiceFilter();
        }

        function applyDateFilter() {
            applyFilter();
        }

        function applyFilter() {
            router.post(route('isp.installation.dashboard'), {
                date_from: date_from.value,
                date_to: date_to.value,
                service_types: service_types.value,
            }, {
                onSuccess: () => {
                    key.value++;
                }
            });
        }

        function applyInstallationServiceFilter() {
            router.post(route('isp.installation.dashboard'), {
                date_from: date_from.value,
                date_to: date_to.value,
                service_types: service_types.value,
                selected_installation_service: selected_installation_service.value,
            }, {
                onSuccess: () => {
                    key.value++;
                }
            });
        }

        return {
            date_from,
            date_to,
            service_types,
            selected_installation_service,
            isAllSelected,
            selectedServiceNames,
            chartLabels,
            getStatusColor,
            getServiceColor,
            getInstallationServiceDatasets,
            handleServiceTypeChange,
            handleInstallationServiceChange,
            applyDateFilter,
            applyFilter,
            applyInstallationServiceFilter,
            key
        };
    },
};
</script>