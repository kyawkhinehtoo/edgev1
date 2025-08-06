<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Create Zone</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">City</label>
                <select v-model="form.city_id" @change="filterTownships" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <option value="">Select a City</option>
                  <option v-for="city in cities" :key="city.id" :value="city.id">
                    {{ city.name }}
                  </option>
                </select>
                <div v-if="form.errors.city_id" class="text-red-500 text-xs mt-1">{{ form.errors.city_id }}</div>
              </div>

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
                          :disabled="!form.city_id"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                    <option v-for="township in filteredTownships" :key="township.id" :value="township.id">
                      {{ township.name }}
                    </option>
                  </select>
                  <p v-if="!form.city_id" class="text-sm text-gray-500 mt-1">Please select a city first</p>
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
                Create Zone
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
    cities: Array,
    townships: Array
  },
  setup() {
    const form = useForm({
      name: '',
      description: '',
      is_active: true,
      city_id: '',
      township_ids: []
    })

    return { form }
  },
  data() {
    return {
      filteredTownships: []
    }
  },
  methods: {
    submit() {
      this.form.post(route('zone.store'))
    },
    filterTownships() {
      if (this.form.city_id) {
        this.filteredTownships = this.townships.filter(township => 
          township.city_id == this.form.city_id
        )
        // Clear selected townships when city changes
        this.form.township_ids = []
      } else {
        this.filteredTownships = []
        this.form.township_ids = []
      }
    }
  }
}
</script>