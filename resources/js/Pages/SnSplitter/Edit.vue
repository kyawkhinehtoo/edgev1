<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from "@suadelabs/vue3-multiselect";
import { watch } from 'vue';
const props = defineProps({
  snSplitter: Object,
  snBoxes: Array,
  fiberCables: Array
})

const form = useForm({
  name: props.snSplitter.name,
  fiber_type: props.snSplitter.fiber_type, // Set the initial value of fiber_type to the fiber_type of the SN Splitte
  sn: props.snBoxes.find(box => box.id === props.snSplitter.sn_id),
  sn_id: props.snSplitter.sn_id,
  fiber: props.fiberCables.find(cable => cable.id === props.snSplitter.fiber_id),
  fiber_id: props.snSplitter.fiber_id,
  fiber_color: props.snSplitter.fiber_color || '',
  core: { id: props.snSplitter.core_number?.toString(), name: props.snSplitter?.core_number?.toString() },
  core_number: props.snSplitter.core_number,
  location: props.snSplitter.location,
  port: { id: props.snSplitter.port_number?.toString(), name: props.snSplitter?.port_number?.toString() },
  port_number: props.snSplitter.port_number,
  status: props.snSplitter.status
})
watch(() => form.fiber_type, (newValue) => {
  chooseFiberCable(newValue)
})
const chooseFiberCable = (fiberCable) => {
  if(fiberCable == 'patch_chord'){
    form.fiber = null
    form.fiber_id = null
    form.core = null
    form.core_number = null
  }
}
const cores = Array.from({ length: 96 }, (_, i) => {
  const num = (i + 1).toString()
  return { id: num, name: num }
})
const ports = Array.from({ length: 4 }, (_, i) => {
  const num = (i + 1).toString()
  return { id: num, name: num }
})
const submit = () => {
  form.put(route('sn-splitters.update', props.snSplitter.id), {
    onSuccess: (page) => {
        Toast.fire({
        icon: 'success',
        title: page.props.flash.message, // Access the message property from the page props
      })
    }
  })
}
</script>

<template>
  <AppLayout title="Edit SN Splitter">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Edit SN Splitter
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input
                  type="text"
                  v-model="form.name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700">SN Box</label>
                  <multiselect
                    v-model="form.sn"
                    :options="snBoxes"
                    track-by="id"
                    label="name"
                    placeholder="Select SN Box"
                    :allow-empty="false"
                    @update:modelValue="form.sn_id = $event?.id"
                  />
                  <div v-if="form.errors.sn_id" class="text-red-500 text-sm mt-1">{{ form.errors.sn_id }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">DN Port Number</label>
                  <multiselect
                  v-model="form.port"
                  :options="ports"
                  track-by="id"
                  label="name"
                  placeholder="Select Port Number"
                  :allow-empty="false"
                  @update:modelValue="form.port_number = $event?.id"
                />
                  <div v-if="form.errors.core_number" class="text-red-500 text-sm mt-1">{{ form.errors.core_number }}</div>
                </div>
              </div>
             

              <div>
                <label class="block text-sm font-medium text-gray-700">Fiber Type</label>
                <div class="flex gap-4">
                  <label class="inline-flex items-center">
                    <input
                      type="radio"
                      v-model="form.fiber_type"
                      value="patch_chord"
                      class="form-radio text-yellow-500 border-gray-300 focus:ring-yellow-500"
                    />
                    <span class="ml-2">Patch Chord</span>
                  </label>
                  <label class="inline-flex items-center">
                    <input
                      type="radio"
                      v-model="form.fiber_type"
                      value="distributed_route" 
                      class="form-radio text-indigo-600 border-gray-300 focus:ring-indigo-500"
                    />
                    <span class="ml-2">Destributed Route</span>
                  </label>
                </div>
                <div v-if="form.errors.fiber_type" class="text-red-500 text-sm mt-1">{{ form.errors.fiber_type }}</div>
              </div>
              <div class="grid grid-cols-3 gap-2" v-if="form.fiber_type == 'distributed_route'">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Source Fiber Cable</label>
                  <multiselect
                    v-model="form.fiber"
                    :options="fiberCables"
                    track-by="id"
                    label="name"
                    placeholder="Select Fiber Cable"
                    :allow-empty="false"
                    @update:modelValue="form.fiber_id = $event?.id"
                  />
                  <div v-if="form.errors.fiber_id" class="text-red-500 text-sm mt-1">{{ form.errors.fiber_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Fiber Color</label>
                  <select
                    v-model="form.fiber_color"
                    class="block w-full rounded-md border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="">Select Color</option>
                    <option value="blue">Blue</option>
                    <option value="orange">Orange</option>
                    <option value="green">Green</option>
                    <option value="brown">Brown</option>
                    <option value="gray">Gray</option>
                    <option value="white">White</option>
                    <option value="red">Red</option>
                    <option value="black">Black</option>
                    <option value="yellow">Yellow</option>
                    <option value="purple">Purple</option>
                    <option value="pink">Pink</option>
                    <option value="aqua">Aqua</option>
                  </select>
                  <div v-if="form.errors.fiber_color" class="text-red-500 text-sm mt-1">{{ form.errors.fiber_color }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Core Number</label>
                  <multiselect
                    v-model="form.core"
                    :options="cores"
                    track-by="id"
                    label="name"
                    placeholder="Select Core Number"
                    :allow-empty="false"
                    @update:modelValue="form.core_number = $event?.id"
                  />
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
                  :href="route('sn-splitters.index')"
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