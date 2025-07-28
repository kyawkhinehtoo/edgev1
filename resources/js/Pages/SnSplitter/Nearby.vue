<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Nearby SN Splitters</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
          <form @submit.prevent="searchNearby">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Location (lat,lng)</label>
                <input v-model="location" type="text" class="mt-1 block w-full rounded-md border-gray-300" placeholder="e.g. 16.8,96.15" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Radius (meter)</label>
                <input v-model.number="radius" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300" />
              </div>
              <div class="flex items-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Search</button>
              </div>
            </div>
          </form>
          <div v-if="error" class="text-red-500 mb-4">{{ error }}</div>
          <div v-if="splitters.data?.length">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr>
                  <th class="px-4 py-2">SN Name</th>
                  <th class="px-4 py-2">Location</th>
                  <th class="px-4 py-2">Distance (m)</th>
                  <th class="px-4 py-2">DN Splitter</th>
                  <th class="px-4 py-2">DN Box</th>
                  <th class="px-4 py-2">OLT</th>
                  <th class="px-4 py-2">POP</th>
                  <th class="px-4 py-2">Partner</th>
                  <th class="px-4 py-2">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in splitters.data" :key="s.id">
                  <td class="px-4 py-2">{{ s.name }}</td>
                  <td class="px-4 py-2">{{ s.location }}</td>
                  <td class="px-4 py-2">{{ s.distance ? s.distance.toFixed(2) : '-' }}</td>
                  <td class="px-4 py-2">{{ s.sn_box.dn_splitter?.name }}</td>
                  <td class="px-4 py-2">{{ s.sn_box.dn_splitter?.dn_box?.name }} {{ s.sn_box.dn_splitter?.dn_box?.type }} </td>
                  <td class="px-4 py-2">{{ s.sn_box.dn_splitter?.pop_device?.device_name  }}</td>
                  <td class="px-4 py-2">{{ s.sn_box.dn_splitter?.pop_device?.pop?.site_name  }}</td>
                  <td class="px-4 py-2">{{ s.sn_box.dn_splitter?.pop_device?.pop?.partner?.name  }}</td>
                  <td class="px-4 py-2 capitalize">{{ s.status }}</td>
                </tr>
              </tbody>
            </table>
             <span v-if="splitters.total" class="block mt-4 text-xs text-gray-600">
          {{ splitters.data.length }} Splitter in Current Page. Total Number of Splitters: {{ splitters.total }}
        </span>
             <pagination class="mt-6" v-if="splitters.links" :links="splitters.links" />
          </div>
          <div v-else class="text-gray-500">No splitters found.</div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Pagination from "@/Components/Pagination";
export default {
  name: 'NearbySnSplitter',
  components: { AppLayout },
  setup() {
    const page = usePage();
    const location = ref(page.props.location || '');
    const radius = ref(page.props.radius || 10);
    const splitters = ref(page.props.splitters || []);
    const error = ref(page.props.error || null);

    function isValidLatLng(str) {
      const regex = /^\s*-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?\s*$/;
      return regex.test(str);
    }

    function searchNearby() {
      if (!isValidLatLng(location.value)) {
        error.value = "Please enter a valid location in 'lat,lng' format.";
        return;
      }
      error.value = null;
      router.get('/sn/nearby', { location: location.value, radius: radius.value }, {
        preserveState: false,
        replace: true,
      });
    }

    return { location, radius, splitters, error, searchNearby };
  },
};
</script>
