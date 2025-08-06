<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">ISP Dashboard</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-4" >
        
        <!-- ISP Dashboard Navigation -->
        <div class="bg-white shadow-xl sm:rounded-lg p-4">
          <div class="flex flex-wrap gap-2">
            <div class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
              </svg>
              Main Dashboard
              <span class="ml-2 bg-blue-500 text-xs px-2 py-1 rounded-full">Current</span>
            </div>
            
            <Link 
              :href="route('isp.installation.dashboard')"
              class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2L3 8v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V8l-7-6z" clip-rule="evenodd"/>
              </svg>
              Installation Dashboard
            </Link>
            
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
        
        <!-- Service Type Filter Section -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-4">Service Type Filter</h3>
            <div class="flex flex-wrap gap-4">
              <label class="flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="service_types.includes('ALL')"
                  @change="handleServiceTypeChange('ALL')"
                  class="mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <span class="text-sm font-medium text-gray-900">ALL</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="service_types.includes('FTTH')"
                  @change="handleServiceTypeChange('FTTH')"
                  class="mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <span class="text-sm font-medium text-gray-900">FTTH</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="service_types.includes('DIA')"
                  @change="handleServiceTypeChange('DIA')"
                  class="mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <span class="text-sm font-medium text-gray-900">DIA</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="service_types.includes('DPLC')"
                  @change="handleServiceTypeChange('DPLC')"
                  class="mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <span class="text-sm font-medium text-gray-900">DPLC</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="service_types.includes('IPLC')"
                  @change="handleServiceTypeChange('IPLC')"
                  class="mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <span class="text-sm font-medium text-gray-900">IPLC</span>
              </label>
            </div>
            <div class="mt-2 text-sm text-gray-600">
              Selected: {{ service_types.join(', ') }}
            </div>
          </div>
        </div>

        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Customer Matrix by Port Sharing Service</h3>
            <form class="flex items-center mb-4 gap-4" @submit.prevent="applyDateFilter">
              <label class="font-semibold">From:</label>
              <input type="date" v-model="date_from" class="border rounded px-2 py-1" />
              <label class="font-semibold">To:</label>
              <input type="date" v-model="date_to" class="border rounded px-2 py-1" />
              <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
            </form>
            <table class="min-w-full divide-y divide-gray-200 mb-8">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Port Sharing Service</th>
                  <th v-for="status in status_types" :key="status" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ status }}</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in matrix" :key="row.port_sharing_service_id">
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ row.port_sharing_service_name }}</td>
                  <td v-for="status in status_types" :key="status" class="px-6 py-4 whitespace-nowrap">{{ row[status] }}</td>
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ status_types.reduce((sum, status) => sum + (row[status] || 0), 0) }}</td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                  <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                  <td v-for="status in status_types" :key="'grand-' + status" class="px-6 py-4 whitespace-nowrap">{{ grand_total[status] }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ grand_total.total }}</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-4 text-lg font-bold">Total Customers: {{ grand_total.total }}</div>
          </div>
        </div>

        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Customer Matrix by Zone</h3>
            <form class="flex items-center mb-4 gap-4" @submit.prevent="applyZoneFilter">
              <label class="font-semibold">Zone:</label>
              <select v-model="zone_id" class="border rounded px-2 py-1 w-48">
                <option value="">All Zones</option>
                <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
              </select>
              <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
            </form>
            <table class="min-w-full divide-y divide-gray-200 mb-8">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Zone</th>
                  <th v-for="status in status_types" :key="'zone-header-' + status" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ status }}</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in zone_matrix" :key="row.zone_id">
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ row.zone_name }}</td>
                  <td v-for="status in status_types" :key="'zone-' + row.zone_id + '-' + status" class="px-6 py-4 whitespace-nowrap">{{ row[status] }}</td>
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ status_types.reduce((sum, status) => sum + (row[status] || 0), 0) }}</td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                  <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                  <td v-for="status in status_types" :key="'zone-grand-' + status" class="px-6 py-4 whitespace-nowrap">{{ zone_grand_total[status] }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ zone_grand_total.total }}</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-4 text-lg font-bold">Total Customers: {{ zone_grand_total.total }}</div>
          </div>
        </div>

        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Customer Matrix by Township</h3>
            <form class="flex items-center mb-4 gap-4" @submit.prevent="applyTownshipFilter">
              <label class="font-semibold">Township:</label>
              <select v-model="township_id" class="border rounded px-2 py-1">
                <option value="">All Townships</option>
                <option v-for="township in townships" :key="township.id" :value="township.id">{{ township.name }}</option>
              </select>
              <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
            </form>
            <table class="min-w-full divide-y divide-gray-200 mb-8">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Township</th>
                  <th v-for="status in status_types" :key="'township-header-' + status" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ status }}</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in township_matrix" :key="row.township_id">
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ row.township_name }}</td>
                  <td v-for="status in status_types" :key="'township-' + row.township_id + '-' + status" class="px-6 py-4 whitespace-nowrap">{{ row[status] }}</td>
                  <td class="px-6 py-4 whitespace-nowrap font-bold">{{ status_types.reduce((sum, status) => sum + (row[status] || 0), 0) }}</td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                  <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                  <td v-for="status in status_types" :key="'township-grand-' + status" class="px-6 py-4 whitespace-nowrap">{{ township_grand_total[status] }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ township_grand_total.total }}</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-4 text-lg font-bold">Total Customers: {{ township_grand_total.total }}</div>
          </div>
        </div>

        <!-- NEW LINE CHART SECTION -->
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Customer Trends by Port Sharing Service (Monthly)</h3>
            <form class="flex items-center mb-4 gap-4" @submit.prevent="applyChartFilter">
              <label class="font-semibold">From:</label>
              <input type="date" v-model="chart_date_from" class="border rounded px-2 py-1" />
              <label class="font-semibold">To:</label>
              <input type="date" v-model="chart_date_to" class="border rounded px-2 py-1" />
               <label class="font-semibold">Status:</label>
              <select v-model="chart_status_id" class="border rounded px-2 py-1 w-32 capitalize">
                <option value="">All Statuses</option>
                <option v-for="status in status_types" :value="status">{{ status }}</option>
              </select>
              <label class="font-semibold">Zone:</label>
              <select v-model="chart_zone_id" class="border rounded px-2 py-1 w-32">
                <option value="">All Zones</option>
                <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
              </select>
             
              <label class="font-semibold">Township:</label>
              <select v-model="chart_township_id" class="border rounded px-2 py-1 w-32">
                <option value="">All Townships</option>
                <option v-for="township in townships" :key="township.id" :value="township.id">{{ township.name }}</option>
              </select>
              <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
            </form>
            <LineChart
              chart-id="service-trends-chart"
              title="Total Customers by Port Sharing Service Over Time"
              :labels="chartLabels"
              :datasets="getPortSharingServiceDatasets()"
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
    name: 'IspDashboard',
    components: { AppLayout, LineChart, Link },
    props: {
        matrix: Object,
        status_types: Array,
        grand_total: Object,
        zone_matrix: Array,
        zone_grand_total: Object,
        zones: {
            type: Array,
            default: () => [],
        },
        township_matrix: Array,
        township_grand_total: Object,
        townships: {
            type: Array,
            default: () => [],
        },
        months: Array,
        chart_data: Object,
        chart_date_from: String,
        chart_date_to: String,
        chart_zone_id: String,
        chart_township_id: String,
        chart_status_id: String,
        service_types: {
            type: Array,
            default: () => ['ALL'],
        },
    },
    setup(props, { emit }) {
        const date_from = ref('');
        const date_to = ref('');
        const zone_id = ref('');
        const township_id = ref('');
        const service_types = ref([...props.service_types]);
        const chart_date_from = ref(props.chart_date_from || '');
        const chart_date_to = ref(props.chart_date_to || '');
        const chart_zone_id = ref(props.chart_zone_id || '');
        const chart_township_id = ref(props.chart_township_id || '');
        const chart_status_id = ref(props.chart_status_id || '');
        const key = ref(0);
        const chartLabels = computed(() => {
            return props.months.map(month => {
                const [year, monthNum] = month.split('-');
                const date = new Date(year, monthNum - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            });
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

        function getPortSharingServiceDatasets() {
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

        function applyDateFilter() {
            // eslint-disable-next-line
            router.post(route('isp.dashboard'), {
                date_from: date_from.value,
                date_to: date_to.value,
                zone_id: zone_id.value,
                township_id: township_id.value,
                service_types: service_types.value,
                chart_date_from: chart_date_from.value,
                chart_date_to: chart_date_to.value,
                chart_zone_id: chart_zone_id.value,
                chart_township_id: chart_township_id.value,
                chart_status_id: chart_status_id.value,
            });
        }

        function applyZoneFilter() {
            // eslint-disable-next-line
            router.post(route('isp.dashboard'), {
                date_from: date_from.value,
                date_to: date_to.value,
                zone_id: zone_id.value,
                township_id: township_id.value,
                service_types: service_types.value,
                chart_date_from: chart_date_from.value,
                chart_date_to: chart_date_to.value,
                chart_zone_id: chart_zone_id.value,
                chart_township_id: chart_township_id.value,
                chart_status_id: chart_status_id.value,
            });
        }

        function applyTownshipFilter() {
            // eslint-disable-next-line
            router.post(route('isp.dashboard'), {
                date_from: date_from.value,
                date_to: date_to.value,
                zone_id: zone_id.value,
                township_id: township_id.value,
                service_types: service_types.value,
                chart_date_from: chart_date_from.value,
                chart_date_to: chart_date_to.value,
                chart_zone_id: chart_zone_id.value,
                chart_township_id: chart_township_id.value,
                chart_status_id: chart_status_id.value,
            });
        }

        function applyChartFilter() {
            // eslint-disable-next-line
            console.log('Applying chart filter with:', {
                date_from: chart_date_from.value,
                date_to: chart_date_to.value,
                zone_id: chart_zone_id.value,
                township_id: chart_township_id.value,
            });
            // eslint-disable-next-line
            router.post(route('isp.dashboard'), {
                date_from: date_from.value,
                date_to: date_to.value,
                zone_id: zone_id.value,
                township_id: township_id.value,
                service_types: service_types.value,
                
                chart_date_from: chart_date_from.value,
                chart_date_to: chart_date_to.value,
                chart_zone_id: chart_zone_id.value,
                chart_township_id: chart_township_id.value,
                chart_status_id: chart_status_id.value,
            }, {
                onSuccess: () => {
                    key.value++;
                }
            });
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
            // Reset key to trigger reactivity
            // Apply the filter immediately
            applyServiceTypeFilter();
         
        }

        function applyServiceTypeFilter() {
            router.post(route('isp.dashboard'), {
                date_from: date_from.value,
                date_to: date_to.value,
                zone_id: zone_id.value,
                township_id: township_id.value,
                service_types: service_types.value,
                chart_date_from: chart_date_from.value,
                chart_date_to: chart_date_to.value,
                chart_zone_id: chart_zone_id.value,
                chart_township_id: chart_township_id.value,
                chart_status_id: chart_status_id.value,
            }, {
                onSuccess: () => {
                    key.value++;
                }
            });
        }

        return {
            date_from,
            date_to,
            zone_id,
            township_id,
            service_types,
            chart_date_from,
            chart_date_to,
            chart_zone_id,
            chart_township_id,
            chart_status_id,
            chartLabels,
            getStatusColor,
            getServiceColor,
            getPortSharingServiceDatasets,
            applyDateFilter,
            applyZoneFilter,
            applyTownshipFilter,
            applyChartFilter,
            handleServiceTypeChange,
            applyServiceTypeFilter,
            key
        };
    },
};
</script>