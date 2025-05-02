<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Create ODF</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-4">
              <Link :href="route('odfs.index')" class="text-indigo-600 hover:text-indigo-900">
                <i class="fas fa-arrow-left"></i> Back to ODFs
              </Link>
            </div>

            <form @submit.prevent="submit">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">POP Device</label>
                <div class="flex gap-4">
                  <multiselect
    
                    :options="pops"
                    track-by="id"
                    label="site_name"
                    placeholder="Select POP"
                    :searchable="true"
                    :allow-empty="false"
                    class="w-1/2"
                    @update:modelValue="loadPopDevices($event?.id)"
                    v-model="form.pop_id"
                    v-if="pops"
                  />

                  <multiselect
                    v-model="form.pop_device"
                    :options="filteredPopDevices"
                    track-by="id"
                    label="device_name"
                    placeholder="Select POP Device"
                    :searchable="true"
                    :allow-empty="false"
                    :multiple="true"
                    class="w-1/2"
                    :disabled="!form.pop_id"
                   @update:modelValue="updatePopDeviceIds"
                  />
                </div>
                <div v-if="form.errors.pop_device_id" class="text-red-500 text-xs mt-1">
                  {{ form.errors.pop_device_id }}
                </div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ODF Name</label>
                <input 
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Enter ODF Name"
                />
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">
                  {{ form.errors.name }}
                </div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                <input v-model="form.location" type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <div v-if="form.errors.location" class="text-red-500 text-xs mt-1">
                  {{ form.errors.location }}
                </div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea v-model="form.description"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">
                  {{ form.errors.description }}
                </div>
              </div>

              <div class="flex items-center justify-between">
                <button type="submit"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                  Create ODF
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm,Link } from '@inertiajs/vue3'
import Multiselect from '@suadelabs/vue3-multiselect'

export default {
  components: {
    AppLayout,
    Link,
    Multiselect
  },
  props: {
    pops: {
      type: Array,
      required: true
    },
    popDevices: {
      type: Array,
      required: true
    }
  },
  setup() {
    const form = useForm({
      pop_id: null,
      pop_device: null,
      pop_device_id: null,
      name: null,
      location: '',
      description: ''
    })

    return { form }
  },
  data() {
    return {
      filteredPopDevices: [],
      portNumbers: Array.from({ length: 16 }, (_, i) => i + 1)
    }
  },
  methods: {
    updatePopDeviceIds(selectedDevices) {
    this.form.pop_device_id = selectedDevices.map(device => device.id);
  },
    loadPopDevices(id) {
      if (id) {
        this.filteredPopDevices = this.popDevices.filter(device => device.pop_id === id)
        this.form.pop_device = null
        this.form.pop_device_id = null
      } else {
        this.filteredPopDevices = []
      }
    },
    submit() {
      this.form.post(route('odfs.store'), {
        onSuccess: (page) => {
          Toast.fire({
            icon: 'success',
            title: page.props.flash.message
          })
        }
      })
    }
  }
}
</script>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>