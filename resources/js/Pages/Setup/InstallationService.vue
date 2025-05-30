<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">Installation Services Setup</h2>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
                    <div class="relative flex flex-wrap z-0">
                        <span
                            class="z-10 h-full leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                                class="fas fa-search"></i></span>
                        <input type="text" placeholder="Search here..."
                            class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
                            id="search" v-model="search" v-on:keyup.enter="searchService" />
                    </div>

                    <button @click="openModal"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="services.data">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No.
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Service Name</th>
                                <th scope="col"
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Service Type</th>
                                <th scope="col"
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Short Code</th>
                                <th scope="col"
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SLA Hours</th>
                                <th scope="col"
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fees</th>
                              
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr v-for="(row, index) in services.data" v-bind:key="row.id"
                                :class="{ 'text-gray-400': !row.status }">
                                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">{{ services.from +
                                    index
                                }}</td>
                                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">{{ row.name }}
                                </td>
                                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">{{ row.type }}
                                </td>
                                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ row.short_code
                                }}</span></td>
                                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">{{ row.sla_hours }}
                                    Hours
                                </td>
                                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ row.fees }} <span class="ml-1 uppercase">{{ row.currency }}</span>
                                    </span>
                                </td>
                              
                                <td class="px-3 py-3 text-md font-medium whitespace-nowrap text-right">
                                    <a href="#" @click="edit(row)"
                                        class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                                    <a href="#" @click="deleteRow(row)"
                                        class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
                        <div
                            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                            ​
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
                                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                                <form @submit.prevent="submit">
                                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">

                                            <div class="grid grid-cols-3 gap-2">
                                                <div class="col-span-2">
                                                    <label for="name"
                                                        class="block text-md font-medium text-gray-700">Service
                                                        Name</label>
                                                    <div class="mt-1 flex rounded-md shadow-sm">
                                                        <input type="text" v-model="form.name" name="name" id="name"
                                                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                                            placeholder="Service Name" required />
                                                    </div>
                                                </div>
                                                <div class="col-span-1">
                                                    <label for="short_code"
                                                        class="block text-md font-medium text-gray-700">Short
                                                        Code</label>
                                                    <div class="mt-1 flex rounded-md shadow-sm">
                                                        <input type="text" v-model="form.short_code"
                                                            name="short_code" id="short_code"
                                                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                                            placeholder="Short Code" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="type"
                                                    class="block text-md font-medium text-gray-700">Service
                                                    Type</label>
                                                <div class="mt-2 flex">
                                                    <label class="flex-auto items-center">
                                                        <input type="radio" class="form-radio h-5 w-5 text-red-600"
                                                            name="type" v-model="form.type" value="new" />
                                                        <span class="ml-2 text-gray-700 text-sm">New
                                                            Installation</span>
                                                    </label>
                                                    <label class="flex-auto items-center">
                                                        <input type="radio"
                                                            class="form-radio h-5 w-5 text-green-600"
                                                            name="type" v-model="form.type" value="relocation" />
                                                        <span class="ml-2 text-gray-700 text-sm">Relocation</span>
                                                    </label>
                                                    </div>
                                            </div>
                                            <div>
                                                <label for="sla_hours"
                                                    class="block text-md font-medium text-gray-700">SLA
                                                    Hours</label>
                                                <div class="mt-1 flex rounded-md shadow-sm">
                                                    <input type="number" v-model="form.sla_hours" name="sla_hours"
                                                        id="sla_hours"
                                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                                        placeholder="SLA Hours" v-on:keypress="isNumber($event)"
                                                        required />
                                                    <span
                                                        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                        Hours </span>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="fees"
                                                    class="block text-md font-medium text-gray-700">Service
                                                    Fees</label>
                                                <div class="mt-1 flex rounded-md shadow-sm">
                                                    <input type="number" v-model="form.fees" name="fees"
                                                        id="fees"
                                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                                        placeholder="Service Fees" v-on:keypress="isNumber($event)"
                                                        required />
                                                    <span
                                                        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm uppercase">
                                                        {{ form.currency }} </span>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="currency"
                                                    class="block text-md font-medium text-gray-700">Currency
                                                </label>
                                                <div class="mt-2 flex">
                                                    <label class="flex-auto items-center">
                                                        <input type="radio" class="form-radio h-5 w-5 text-red-600"
                                                            name="currency" v-model="form.currency" value="mmk" />
                                                        <span class="ml-2 text-gray-700 text-sm">MMK</span>
                                                    </label>
                                                   
                                                    <label class="flex-auto items-center">
                                                        <input type="radio"
                                                            class="form-radio h-5 w-5 text-green-600"
                                                            name="currency" v-model="form.currency" value="usd" />
                                                        <span class="ml-2 text-gray-700 text-sm">USD</span>
                                                    </label>
                                                    <label class="flex-auto items-center">
                                                        <input type="radio" class="form-radio h-5 w-5 text-yellow-600"
                                                            name="currency" v-model="form.currency" value="baht" />
                                                        <span class="ml-2 text-gray-700 text-sm">THB</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="status"
                                                    class="block text-md font-medium text-gray-700">Status</label>
                                                <div class="mt-1 flex">
                                                    <input type="checkbox"
                                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1"
                                                        name="status" v-model="form.status" value="true" />
                                                    <label class="flex-auto items-center ml-1"
                                                        v-if="form.status == true">Enabled</label>
                                                    <label class="flex-auto items-center ml-1"
                                                        v-if="form.status == false">Disabled</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                                    <button type="submit"
                                                        class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                                        v-show="!editMode">Save</button>
                                                </span>
                                                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                                    <button type="submit"
                                                        class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                                        v-show="editMode">Update</button>
                                                </span>
                                                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                                    <button @click="closeModal" type="button"
                                                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <span v-if="services.links">
                    <pagination class="mt-6" :links="services.links" />
                </span>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { reactive, ref } from "vue";
import { router } from '@inertiajs/vue3';

export default {
    name: "installation-service",
    components: {
        AppLayout,
        Pagination
    },
    props: {
        services: Object,
        errors: Object
    },
    setup(props) {
        const form = reactive({
            id: null,
            name: null,
            type: "new", // Default to New Installation
            short_code: null,
            sla_hours: null,
            fees: null,
            currency: "mmk", // Default to MMK
            status: true
        });

        const search = ref("");
        let editMode = ref(false);
        let isOpen = ref(false);

        function resetForm() {
            form.id = null;
            form.name = null;
            form.type = "new"; // Reset to default
            form.short_code = null;
            form.sla_hours = null;
            form.fees = null;
            form.currency = "mmk";
            form.status = true;
        }

        function submit() {
            if (!editMode.value) {
                form._method = "POST";
                router.post("/installation-service", form, {
                    preserveState: true,
                    onSuccess: (page) => {
                        closeModal();
                        resetForm();
                        Toast.fire({
                            icon: "success",
                            title: page.props.flash.message,
                        });
                    },
                    onError: (errors) => {
                        console.log("error ..", errors);
                    },
                });
            } else {
                form._method = "PUT";
                router.post("/installation-service/" + form.id, form, {
                    onSuccess: (page) => {
                        closeModal();
                        resetForm();
                        Toast.fire({
                            icon: "success",
                            title: page.props.flash.message,
                        });
                    },
                    onError: (errors) => {
                        console.log("error ..", errors);
                    },
                });
            }
        }

        function edit(data) {
            form.id = data.id;
            form.name = data.name;
            form.type = data.type || "New Installation"; // Use existing type or default
            form.short_code = data.short_code;
            form.sla_hours = data.sla_hours;
            form.fees = data.fees;
            form.currency = data.currency || "mmk";
            form.status = data.status == 1;
            editMode.value = true;
            openModal();
        }

        function isNumber(evt) {
            evt = evt ? evt : window.event;
            var charCode = evt.which ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                evt.preventDefault();
            } else {
                return true;
            }
        }

        function deleteRow(data) {
            if (!confirm("Are you sure want to remove?")) return;
            data._method = "DELETE";
            router.post("/installation-service/" + data.id, data, {
                preserveState: true,
                onSuccess: (page) => {
                    closeModal();
                    resetForm();
                    Toast.fire({
                        icon: "success",
                        title: page.props.flash.message,
                    });
                },
                onError: (errors) => {
                    closeModal();
                    console.log("error ..", errors);
                },
            });
            closeModal();
            resetForm();
        }

        function openModal() {
            isOpen.value = true;
        }

        const closeModal = () => {
            isOpen.value = false;
            resetForm();
            editMode.value = false;
        };

        const searchService = () => {
            router.get("/installation-service", { service: search.value }, { preserveState: true });
        };

        return {
            form,
            submit,
            editMode,
            isOpen,
            openModal,
            closeModal,
            edit,
            deleteRow,
            searchService,
            isNumber,
            search
        };
    },
};
</script>

<style scoped>
input[type="number"]::-webkit-inner-spin-button {
    opacity: 1;
}
</style>