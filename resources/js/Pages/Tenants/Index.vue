<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    tenants: Array,
    message: String
})

const confirmingTenantDeletion = ref(false)
const tenantIdBeingDeleted = ref(null)

const confirmTenantDeletion = (tenantId) => {
    confirmingTenantDeletion.value = true
    tenantIdBeingDeleted.value = tenantId
}

const deleteTenant = () => {
    router.delete(route('tenant.destroy', tenantIdBeingDeleted.value), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => closeModal(),
        onFinish: () => closeModal(),
    })
}

const closeModal = () => {
    confirmingTenantDeletion.value = false
    tenantIdBeingDeleted.value = null
}
</script>

<template>
    <AppLayout title="Tenants">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tenants
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div v-if="message" class="mb-4 font-medium text-sm text-green-600">
                                {{ message }}
                            </div>
                            <Link :href="route('tenant.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Create Tenant
                            </Link>
                        </div>

                        <div class="mt-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="tenant in tenants" :key="tenant.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ tenant.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ tenant.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ tenant.domain }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link :href="route('tenant.edit', tenant.id)" 
                                                  class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                Edit
                                            </Link>
                                            <button class="text-red-600 hover:text-red-900"
                                                    @click="confirmTenantDeletion(tenant.id)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Tenant Confirmation Modal -->
        <div v-if="confirmingTenantDeletion" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
            <div class="fixed inset-0 transform transition-all" @click="closeModal">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto">
                <div class="px-6 py-4">
                    <div class="text-lg font-medium text-gray-900">
                        Delete Tenant
                    </div>

                    <div class="mt-4 text-sm text-gray-600">
                        Are you sure you want to delete this tenant? Once deleted, all of the tenant's resources and data will be permanently deleted.
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-100 text-right">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" @click="closeModal">
                        Cancel
                    </button>

                    <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-3" @click="deleteTenant">
                        Delete Tenant
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>