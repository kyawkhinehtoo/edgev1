<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Subcon Checklist Groups</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <div class="flex items-center flex-1">
              <div class="w-1/3 flex gap-2">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search group..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @keyup.enter="searchGroups"
                >
                <select v-model="categoryFilter" @change="searchGroups"
                  class="w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <option value="">All Categories</option>
                  <option value="installation">Installation</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="termination">Termination</option>
                </select>
              </div>
            </div>
            <button @click="searchGroups"
              class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
              Search
            </button>
            <button @click="openModal"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              Add Group
            </button>
          </div>

         
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Required</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(group, index) in groups.data" :key="group.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ group.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ group.category }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ group.description }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ group.required ? 'Yes' : 'No' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <a href="#" @click="editGroup(group)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                  <a href="#" @click="deleteGroup(group.id)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

          <div ref="showModal" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="showModal">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
              <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="">
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Group Name:</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="name" placeholder="Enter Group Name" v-model="form.name">
                        <div v-if="$page.props.errors?.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>
                      </div>
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Group Category:</label>
                        <select
                          v-model="form.category"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                          <option value="installation">Installation</option>
                          <option value="maintenance">Maintenance</option>
                          <option value="termination">Termination</option>
                        </select>
                        <div v-if="$page.props.errors?.category" class="text-red-500">{{ $page.props.errors.category[0] }}</div>
                      </div>
                      <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                        <textarea
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="description" placeholder="Enter Description" v-model="form.description"></textarea>
                        <div v-if="$page.props.errors?.description" class="text-red-500">{{ $page.props.errors.description[0] }}</div>
                      </div>
                      <div class="mb-4">
                        <label for="required" class="block text-gray-700 text-sm font-bold mb-2">Required:</label>
                        <input
                          type="checkbox"
                          id="required"
                          v-model="form.required"
                          class="mr-2 leading-tight rounded-sm"
                        >
                        <label for="required" class="text-gray-700 text-sm font-bold">
                          Required
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="!form.id">
                        Save
                      </button>
                      <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="form.id">
                        Update
                      </button>
                    </span>
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                      <button @click="closeModal" type="button"
                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Cancel
                      </button>
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <span v-if="groups.links">
          <pagination class="mt-6" :links="groups.links" />
        </span>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

export default {
  name: 'SubconChecklistsGroupIndex',
  components: { AppLayout, Pagination },
  props: {
    groups: Object,
  },
  setup(props) {
    const form = useForm({
      id: null,
      name: '',
      category: 'installation',
      description: '',
      required: false,
    });
    const categoryFilter = ref(props.category || '');
    const showModal = ref(false);
    const search = ref(props.search || '');
    function openModal() {
      resetForm();
      showModal.value = true;
    }
    function closeModal() {
      showModal.value = false;
      resetForm();
    }
    function submit() {
      if (form.id) {
        router.put(`/subcon-checklists-group/${form.id}`, form, {
          onSuccess: (page) => {
            closeModal()
            Toast.fire(
              {
                icon: 'success',
                title: page.props.flash.message
              })
          },
        });
      } else {
        router.post('/subcon-checklists-group', form, {
          onSuccess: (page) => {
            closeModal()
            Toast.fire(
              {
                icon: 'success',
                title: page.props.flash.message
              })
          },
        });
      }
    }
    function editGroup(group) {
      form.id = group.id;
      form.name = group.name;
      form.category = group.category;
      form.description = group.description;
      form.required = group.required ? true : false;
      showModal.value = true;
    }
    function resetForm() {
      form.id = null;
      form.name = '';
      form.category = 'installation';
      form.description = '';
      form.required = false;
    }
    function deleteGroup(id) {
      if (confirm('Are you sure you want to delete this group?')) {
        router.delete(`/subcon-checklists-group/${id}`);
      }
    }
    function searchGroups() {
      const params = { group: search.value, category: categoryFilter.value };
      router.get('/subcon-checklists-group', params, { preserveState: true });
    }
    
    return { form, submit, editGroup, resetForm, deleteGroup, showModal, openModal, closeModal, search, searchGroups, categoryFilter };
  },
};
</script>
