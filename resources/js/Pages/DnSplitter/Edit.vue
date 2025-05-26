<script setup>
import {  ref,watch } from "vue";
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from "@suadelabs/vue3-multiselect";

const props = defineProps({
  dnSplitter: Object,
  dnBoxes: Array,
  fiberCables: Array,
  pops: Object,
  
})
const popDevices = ref([]);
const form = useForm({
  name: props.dnSplitter.name,
  pop: props.dnSplitter?.pop_device?.pop,
  pop_id : props.dnSplitter?.pop_device?.pop.id,
  pop_device: props.dnSplitter?.pop_device,
  pop_device_id: props.dnSplitter?.pop_device?.id,
  dn: props.dnBoxes.find(box => box.id === props.dnSplitter.dn_id),
  dn_id: props.dnSplitter.dn_id,
  fiber: props.fiberCables.find(cable => cable.id === props.dnSplitter.fiber_id),
  fiber_id: props.dnSplitter.fiber_id,
  core: { id: props.dnSplitter.core_number.toString(), name: props.dnSplitter.core_number.toString() },
  core_number: props.dnSplitter.core_number,
  location: props.dnSplitter.location,
  status: props.dnSplitter.status
})

const cores = Array.from({ length: 96 }, (_, i) => {
  const num = (i + 1).toString()
  return { id: num, name: num }
})

const submit = () => {
  form.put(route('dn-splitters.update', props.dnSplitter.id), {
    onSuccess: (page) => {
      Toast.fire({
        icon: 'success',
        title: page.props.flash.message, // Access the message property from the page props
      })
    }
  })
}
const fetchPopDevices = async () => {
      if (!form.pop_id) {
        popDevices.value = [];
        return;
      }
      try {
        const response = await fetch(`/getOLTByPOP/${form.pop_id}`);
        const data = await response.json();
        popDevices.value = data || [];
        console.log('fetch POP Devices');
      } catch (error) {
        console.error("Failed to fetch POP devices", error);
      }
    };
watch(
      () => form.pop_id,
      ()=>{
        fetchPopDevices();
        form.pop_device = null;
        form.pop_device_id = null;
      }
    );
</script>

<template>
  <AppLayout title="Edit DN Splitter">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Edit DN Splitter
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Name</label>
                  <input
                    type="text"
                    v-model="form.name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>
             
              

              <div>
                <label class="block text-sm font-medium text-gray-700">DN Box</label>
                <multiselect deselect-label="Selected already" :options="dnBoxes" track-by="id"
                label="name" v-model="form.dn" :allow-empty="false" :multiple="false" tabindex="2"
                @update:modelValue="form.dn_id = $event?.id">
              </multiselect>
                <div v-if="form.errors.dn_id" class="text-red-500 text-sm mt-1">{{ form.errors.dn_id }}</div>
              </div>
              <div v-if="pops">
                <label class="block text-sm font-medium text-gray-700">POP Site</label>
                <multiselect deselect-label="Selected already" :options="pops" track-by="id"
                label="site_name" v-model="form.pop" :allow-empty="false" :multiple="false" tabindex="2"
                @update:modelValue="form.pop_id = $event?.id">
              </multiselect>
               
              </div>
              <div v-if="popDevices">
                <label class="block text-sm font-medium text-gray-700">POP Device</label>
                <multiselect deselect-label="Selected already" :options="popDevices" track-by="id"
                label="device_name" v-model="form.pop_device" :allow-empty="false" :multiple="false" tabindex="2"
                @update:modelValue="form.pop_device_id = $event?.id">
              </multiselect>
               
              </div>
            
                <div>
                  <label class="block text-sm font-medium text-gray-700">Source Fiber Cable</label>
                  <multiselect deselect-label="Selected already" :options="fiberCables" track-by="id"
                    label="name" v-model="form.fiber" :allow-empty="false" :multiple="false" tabindex="3"
                    @update:modelValue="form.fiber_id = $event?.id">
                  </multiselect>
                  <div v-if="form.errors.fiber_id" class="text-red-500 text-sm mt-1">{{ form.errors.fiber_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Core Number</label>
                  <multiselect deselect-label="Selected already" :options="cores" track-by="id"
                    label="name" v-model="form.core" :allow-empty="false" :multiple="false" tabindex="4"
                    @update:modelValue="form.core_number = $event?.id">
                  </multiselect>
                  <div v-if="form.errors.core_number" class="text-red-500 text-sm mt-1">{{ form.errors.core_number }}</div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Location (Latitude, Longitude)</label>
                <input
                  type="text"
                  v-model="form.location"
                  placeholder="e.g. 16.8661, 96.1951"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
                <p class="mt-1 text-sm text-gray-500">Enter latitude (-90 to 90) and longitude (-180 to 180) separated by comma</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select
                  v-model="form.status"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
                <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
              </div>

              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('dn-splitters.index')"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Update
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>