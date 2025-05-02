<template>
  <app-layout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-white leading-tight">Edit ODB</h2>
       
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="odf">
                  ODF
                </label>
                <Multiselect
                  v-model="form.odf"
                  :options="odfs"
                  :searchable="true"
                  :create-option="false"
                  placeholder="Select ODF"
                  label="name"
                  track-by="id"
                  class="w-full"
                  :class="{ 'border-red-500': form.errors.odf_id }"
                   @update:modelValue="odf => { form.odf_id = odf?.id }"
                />
                <div v-if="form.errors.odf_id" class="text-red-500 text-xs mt-1">{{ form.errors.odf_id }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                  Name
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="shadow-sm appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{ 'border-red-500': form.errors.name }"
                >
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="total_ports">
                  Total Ports
                </label>
                <Multiselect
                  v-model="form.total_ports"
                  :options="portOptions"
                  :searchable="true"
                  :create-option="false"
                  placeholder="Select Total Ports"
                  class="w-full"
                  :class="{ 'border-red-500': form.errors.total_ports }"
                />
                <div v-if="form.errors.total_ports" class="text-red-500 text-xs mt-1">{{ form.errors.total_ports }}</div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                  Status
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  class="shadow-sm appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{ 'border-red-500': form.errors.status }"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="maintenance">Maintenance</option>
                </select>
                <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
              </div>

              <div class="flex items-center justify-between">
                <Link
                :href="route('odbs.index')"
                class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
              >
                Back to List
              </Link>
                <button
                  type="submit"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                  :disabled="form.processing"
                >
                  Update ODB
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
import { useForm, Link } from '@inertiajs/vue3'
import Multiselect from '@suadelabs/vue3-multiselect'

export default {
  components: {
    AppLayout,
    Link,
    Multiselect
  },
  props: {
    odb: Object,
    odfs: Array
  },
  data() {
    return {
      portOptions: Array.from({ length: 96 }, (_, i) => i + 1)
    }
  },
  setup(props) {
    const form = useForm({
      odf: props.odfs.filter(odf => odf.id === props.odb.odf_id)[0],
      odf_id: props.odb.odf_id,
      name: props.odb.name,
      total_ports: props.odb.total_ports,
      status: props.odb.status
    })

    return { form }
  },
  methods: {
    submit() {
      this.form.put(route('odbs.update', this.odb.id), {
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