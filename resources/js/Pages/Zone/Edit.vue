<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Edit Zone</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Townships</label>
                <div class="mt-1">
                  <select v-model="form.township_ids" multiple
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option v-for="township in townships" :key="township.id" :value="township.id">
                      {{ township.name }}
                    </option>
                  </select>
                </div>
                <div v-if="form.errors.township_ids" class="text-red-500 text-xs mt-1">{{ form.errors.township_ids }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <div class="mt-2">
                  <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2">Active</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
              <Link :href="route('zone.index')" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded mr-2">
                Cancel
              </Link>
              <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Zone
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

export default {
  components: {
    AppLayout,
    Link
  },
  props: {
    zone: Object,
    townships: Array
  },
  setup(props) {
    const form = useForm({
      name: props.zone.name,
      description: props.zone.description,
      is_active: props.zone.is_active,
      township_ids: props.zone.townships.map(t => t.id)
    })

    return { form }
  },
  methods: {
    submit() {
      this.form.put(route('zone.update', this.zone.id))
    }
  }
}
</script>