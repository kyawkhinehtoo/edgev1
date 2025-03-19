<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Edit ISP</h2>
    </template>

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tab Headers -->
        <nav class="flex max-w-72 gap-1">
          <button
            type="button"
            @click="activeTab = 'data'"
            :class="[
              activeTab === 'data'
                ? 'border-t-2 border-indigo-500 text-indigo-600'
                : 'border-b border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700',
              'bg-white sm:rounded-t-lg w-1/2 py-4 px-1 text-center border-0 border-t-2 font-medium text-sm focus:ring-0 focus:outline-none'
            ]"
          >
            ISP Information
          </button>
          <button
            type="button"
            @click="activeTab = 'permissions'"
            :class="[
              activeTab === 'permissions'
                ? 'border-t-2 border-indigo-500 text-indigo-600'
                : 'border-b border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700',
              'bg-white sm:rounded-t-lg w-1/2 py-4 px-1 text-center border-0 border-t-2 font-medium text-sm focus:ring-0 focus:outline-none'
            ]"
          >
            Permissions
          </button>
        </nav>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-b-lg p-6 rounded-r-lg">
          <form @submit.prevent="submit">
            <div v-if="activeTab === 'data'">
              <div class="grid grid-cols-2 gap-6">
                <!-- Existing form fields -->
                <div>
                  <label for="domain" class="block text-sm font-medium text-gray-700">Domain</label>
                  <input type="text" id="domain" v-model="form.domain" 
                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <div v-if="form.errors.domain" class="text-red-500 text-sm mt-1">{{ form.errors.domain }}</div>
                </div>

            
    
                <div class="mb-4">
                  <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
                  <div v-if="isp.logo" class="mt-2 mb-2">
                    <img :src="'/storage/' + isp.logo" :alt="isp.name + ' Logo'" class="h-20 w-auto">
                  </div>
                  <input type="file" id="logo" @input="form.logo = $event.target.files[0]" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <div v-if="form.errors.logo" class="text-red-500 text-sm mt-1">{{ form.errors.logo }}</div>
                </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Short Code</label>
                    <input type="text" v-model="form.short_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.short_code" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                  </div>
               
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                    <input type="text" v-model="form.contact_person" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.contact_person" class="text-red-500 text-xs mt-1">{{ form.errors.contact_person }}</div>
                  </div>
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" v-model="form.phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                  </div>
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" v-model="form.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                  </div>
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Service Type</label>
                    <input type="text" v-model="form.service_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.service_type" class="text-red-500 text-xs mt-1">{{ form.errors.service_type }}</div>
                  </div>
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Bandwidth Capacity</label>
                    <input type="text" v-model="form.bandwidth_capacity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.bandwidth_capacity" class="text-red-500 text-xs mt-1">{{ form.errors.bandwidth_capacity }}</div>
                  </div>
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Website</label>
                    <input type="url" v-model="form.website" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div v-if="form.errors.website" class="text-red-500 text-xs mt-1">{{ form.errors.website }}</div>
                  </div>
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea v-model="form.address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <div v-if="form.errors.address" class="text-red-500 text-xs mt-1">{{ form.errors.address }}</div>
                  </div>
    
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                  </div>
              </div>
            </div>

            <!-- Tab Contents -->
            <span v-if="activeTab === 'permissions'">
              <div class="mb-4">
                <!-- Customer Status -->
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Customer Status</h4>
                
                <div class="mt-1 flex rounded-md shadow-sm" v-if="customerStatus.length !== 0">
                  <multiselect deselect-label="Selected already" :options="customerStatus" track-by="id" label="name"
                    v-model="form.customer_status" :allow-empty="true" :multiple="true" :taggable="false">
                  </multiselect>
                </div>
              </div>
              
              <!-- Permissions -->
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Customer Permission</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div v-for="(permission, index) in availablePermissions" :key="index" class="flex items-center">
                    <jet-checkbox 
                      :id="`permission_${index}`" 
                      :value="permission.value"
                      v-model:checked="form.permissions" 
                    />
                    <jet-label :for="`permission_${index}`" :value="permission.label" class="ml-2" />
                  </div>
                </div>
              </div>
            </span>

            <div class="mt-6 flex items-center justify-end">
              <Link :href="route('isps.index')" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded mr-2">
                Cancel
              </Link>
              <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update ISP
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from "vue";
import JetCheckbox from '@/Jetstream/Checkbox.vue';
import JetLabel from '@/Jetstream/InputLabel.vue';
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    JetCheckbox,
    JetLabel,
    Multiselect,
    Link
  },
  props: {
    isp: Object,
    customerColumns: {
      type: Array,
      required: true
    },
    customerStatus: Object,
  },
  setup(props) {
    const form = useForm({
      _method: 'PUT',
      name: props.isp.name,
      short_code: props.isp.short_code,
      contact_person: props.isp.contact_person,
      phone: props.isp.phone,
      email: props.isp.email || '',
      address: props.isp.address || '',
      website: props.isp.website || '',
      description: props.isp.description || '',
      service_type: props.isp.service_type || '',
      bandwidth_capacity: props.isp.bandwidth_capacity || '',
      domain: props.isp.domain || '',
      logo: null,
      permissions: props.isp.permissions || [],
      customer_status: props.isp.customer_status ? props.isp.customer_status : null,
    })

    const availablePermissions = computed(() => {
      return props.customerColumns.map(column => ({
        value: column.name,
        label: column.name.replace('_', ' ').toUpperCase()
      }));
    });

    const submit = () => {
      if (!form.logo) {
        delete form.logo;
      }
      form.post(route('isps.update', props.isp.id), {
        forceFormData: true,
        preserveScroll: true
      })
    }

    const activeTab = ref('data');
    
    return { 
      form,
      submit,
      availablePermissions,
      activeTab 
    }
  },
}
</script>
