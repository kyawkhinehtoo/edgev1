<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from '@suadelabs/vue3-multiselect'
import { ref } from "vue";

const props = defineProps({
  odfs: Array,
  odbs: Array,
  popDevices: Array,
  fiberCables: Array
})

const portOptions = Array.from({ length: 96 }, (_, i) => ({
  value: i + 1,
  label: `Port ${i + 1}`
}))

const form = useForm({
  name:null,
  odf: null,
  odf_id: null,
  odb: null,
  odb_id: null,
  fiber_cable: null,
  fiber_cable_id: null,
  pop_device: null,
  pop_device_id: null,
  olt_port_all: null,
  odb_port_all: null,
  odb_port: null,
  olt_port: null,
  olt_cable_label: '',
  description: '',
  status: ''
})

const filteredPopDevices = ref([])
const filteredOdbs = ref([])

const handleOdfSelect = (selectedOdf) => {
  form.odf = selectedOdf
  form.odf_id = selectedOdf?.id
  form.pop_device = null
  form.pop_device_id = null
  form.odb = null
  form.odb_id = null
  
  // Filter pop devices based on selected ODF's pop_device_id
  if (selectedOdf && props.popDevices) {
    filteredPopDevices.value = props.popDevices.filter(device => 
      selectedOdf.pop_device_id.includes(device.id)
    )
  } else {
    filteredPopDevices.value = []
  }

  // Filter ODbs based on selected ODF's id
  if (selectedOdf && props.odbs) {
    filteredOdbs.value = props.odbs.filter(odb => 
      odb.odf_id === selectedOdf.id
    )
  } else {
    filteredOdbs.value = []
  }
}
const handleOdbSelect = (selectedOdb) => {
  form.odb = selectedOdb
  form.odb_id = selectedOdb?.id
  
  
  // Filter pop devices based on selected ODF's pop_device_id
  if (selectedOdf && props.popDevices) {
    filteredPopDevices.value = props.popDevices.filter(device => 
      selectedOdf.pop_device_id.includes(device.id)
    )
  } else {
    filteredPopDevices.value = []
  }
}

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'maintenance', label: 'Maintenance' }
]

const handlePortSelect = (port, field) => {
  form[field] = port?.value || null
}

const submit = () => {
  form.post(route('odb-fiber-cables.store'), {
    onSuccess: (page) => {
      Toast.fire({
        icon: 'success',
        title: page.props.flash.message
      })
    }
  })
}
</script>

<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Create ODB Details</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white  shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-4">
              <Link :href="route('odb-fiber-cables.index')" class="text-indigo-600 hover:text-indigo-900">
                <i class="fas fa-arrow-left"></i> Back to List
              </Link>
            </div>

            <form @submit.prevent="submit">
              
              <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline mb-2">
               Feeder Connection Information
              </h6>
              <div class="grid sm:grid-cols-4 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700">ODF <span class="text-red-600">*</span></label>
                  <multiselect
                    v-model="form.odf"
                    :options="odfs"
                    track-by="id"
                    label="name"
                    placeholder="Select ODF"
                    :searchable="true"
                    :allow-empty="false"
                    @update:modelValue="handleOdfSelect"
                  />
                  <div v-if="form.errors.odf_id" class="text-red-500 text-sm mt-1">{{ form.errors.odf_id }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ODB <span class="text-red-600">*</span></label>
                  <multiselect
                    v-model="form.odb"
                    :options="filteredOdbs"
                    track-by="id"
                    label="name"
                    placeholder="Select ODB"
                    :searchable="true"
                    :allow-empty="false"
                    @update:modelValue="handleOdbSelect"
                    :disabled="!form.odf_id"
                  />
                  <div v-if="form.errors.odb_id" class="text-red-500 text-sm mt-1">{{ form.errors.odb_id }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Fiber Cable (Feeder) <span class="text-red-600">*</span></label>
                  <multiselect
                    v-model="form.fiber_cable"
                    :options="fiberCables"
                    track-by="id"
                    label="name"
                    placeholder="Select Fiber Cable"
                    :searchable="true"
                    :allow-empty="false"
                    @update:modelValue="cable => { form.fiber_cable_id = cable?.id }"
                  />
                  <div v-if="form.errors.fiber_cable_id" class="text-red-500 text-sm mt-1">{{ form.errors.fiber_cable_id }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ODB Port No. <span class="text-red-600">*</span></label>
                  <multiselect
                    v-model="form.odb_port_all"
                    :options="portOptions"
                    track-by="value"
                    label="label"
                    placeholder="Select Port"
                    :searchable="true"
                    :allow-empty="false"
                     @update:modelValue="form.odb_port = $event?.value"
                  />
                  <div v-if="form.errors.odb_port" class="text-red-500 text-sm mt-1">{{ form.errors.odb_port }}</div>
                </div>
               

           
              </div>

              <hr class="my-4 md:min-w-full" />
              <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline mb-2">
                OLT Information
              </h6>
              <div class="grid sm:grid-cols-4 gap-6">
              

                <div>
                  <label class="block text-sm font-medium text-gray-700">POP Device (OLT)</label>
                  <multiselect
                    v-model="form.pop_device"
                    :options="filteredPopDevices"
                    track-by="id"
                    label="device_name"
                    placeholder="Select POP Device"
                    :searchable="true"
                    :allow-empty="false"
                    @update:modelValue="device => { form.pop_device_id = device?.id }"
                    :disabled="!form.odf_id"
                  />
                  <div v-if="form.errors.pop_device_id" class="text-red-500 text-sm mt-1">{{ form.errors.pop_device_id }}</div>
                </div>

               

                <div>
                  <label class="block text-sm font-medium text-gray-700">OLT Port No.</label>
                  <multiselect
                    v-model="form.olt_port_all"
                    :options="portOptions"
                    track-by="value"
                    label="label"
                    placeholder="Select Port"
                    :searchable="true"
                    :allow-empty="true"
                    @update:modelValue="form.olt_port = $event?.value"
                  />
                  <div v-if="form.errors.olt_port" class="text-red-500 text-sm mt-1">{{ form.errors.olt_port }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">OLT To ODF Label</label>
                  <input
                    type="text"
                    v-model="form.olt_cable_label"
                    class="block w-full rounded-md border-gray-200 shadow-xsfocus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="form.errors.olt_cable_label" class="text-red-500 text-sm mt-1">{{ form.errors.olt_cable_label }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Status</label>
                  <select
                    v-model="form.status"
                    class="block w-full rounded-md border-gray-200 shadow-xsfocus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="">Select Status</option>
                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                      {{ option.label }}
                    </option>
                  </select>
                  <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                </div>

                <div class="col-span-4">
                  <label class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    class="block w-full rounded-md border-gray-200 shadow-xsfocus:border-indigo-500 focus:ring-indigo-500"
                  ></textarea>
                  <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                </div>
              </div>

              <div class="flex justify-end space-x-3 mt-4">
                <Link
                  :href="route('odb-fiber-cables.index')"
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
            </form>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>