<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Material Device Setup</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <div class="flex items-center flex-1">
              <div class="w-1/3">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search device..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @keyup.enter="searchTsp"
                >
              </div>
            </div>
            <button @click="openModal" 
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              Add Device
            </button>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Details</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in equiptments.data" :key="row.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap uppercase">{{ row.type }}</td>
                <td class="px-6 py-4">{{ row.detail }}</td>
                <td class="px-6 py-4">{{ row.price }} MMK</td>
                <td class="px-6 py-4">{{ row.is_active?'Active':'Disabled' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

          <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              <!-- This element is to trick the browser into centering the modal contents. -->
              <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
              <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="">
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Equiptment Name:</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="name" placeholder="Enter Equiptment Name" v-model="form.name">
                        <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Equiptment Usage:</label>
                        <div class="flex space-x-4">
                          <label class="inline-flex items-center">
                          <input
                            type="radio"
                            class="form-radio"
                            value="ftth"
                            v-model="form.type"
                          >
                          <span class="ml-2">FTTH</span>
                          </label>
                          <label class="inline-flex items-center">
                          <input
                            type="radio"
                            class="form-radio"
                            value="dia"
                            v-model="form.type"
                          >
                          <span class="ml-2">DIA</span>
                          </label>
                          <label class="inline-flex items-center">
                          <input
                            type="radio"
                            class="form-radio"
                            value="iplc"
                            v-model="form.type"
                          >
                          <span class="ml-2">IPLC</span>
                          </label>
                          <label class="inline-flex items-center">
                          <input
                            type="radio"
                            class="form-radio"
                            value="dplc"
                            v-model="form.type"
                          >
                          <span class="ml-2">DPLC</span>
                          </label>
                        </div>
                        <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="detail" class="block text-gray-700 text-sm font-bold mb-2">Equiptment
                          Detail:</label>
                        <textarea
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="detail" placeholder="Enter Details Info" v-model="form.detail"></textarea>
                        <div v-if="$page.props.errors.detail" class="text-red-500">{{ $page.props.errors.detail[0] }}
                        </div>

                      </div>
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Price :</label>
                        <div class=" flex rounded-md shadow-sm">
                        <input type="number"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-md"
                          id="name" placeholder="Enter Price" v-model="form.price">
                         <span class="mt-1 inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm uppercase">
                                  MMK </span>
                          </div>
                        <div v-if="$page.props.errors.price" class="text-red-500">{{ $page.props.errors.price[0] }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Is Active :</label>
                        <input
                          type="checkbox"
                          id="is_active"
                          v-model="form.is_active"
                          class="mr-2 leading-tight rounded-sm"
                        >
                        <label for="is_active" class="text-gray-700 text-sm font-bold">
                          Active
                        </label>
                        <div v-if="$page.props.errors.price" class="text-red-500">{{ $page.props.errors.price[0] }}</div>

                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center r px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="!editMode">
                        Save
                      </button>
                    </span>
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="editMode">
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
        </div>
        <Pagination :links="equiptments.links" class="mt-6" v-if="equiptments.links" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3'
export default {
  name: 'Equiptment',
  components: {
    AppLayout,
    Pagination
  },
  props: {
    equiptments: Object,
    errors: Object
  },
  setup(props) {

    const form = reactive({
      id: null,
      name: null,
      detail: null,
    })
    const search = ref('')
    let editMode = ref(false)
    let isOpen = ref(false)

    function resetForm() {
      form.name = null
      form.detail = null
      form.type = 'ftth'
      form.price = null
      form.is_active = true
    }
    function submit() {
      if (!editMode.value) {
        form._method = 'POST';
        router.post('/equiptment', form, {
          preserveState: true,
          onSuccess: (page) => {
            closeModal()
            resetForm()
            Toast.fire(
              {
                icon: 'success',
                title: page.props.flash.message
              }
            )

          },
          onError: (errors) => {
            closeModal()
            console.log('error ..'.errors)
          }
        });
      } else {

        form._method = 'PUT'; form._method = 'PUT';

        router.post('/equiptment/' + form.id, form, {
          onSuccess: (page) => {
            closeModal()
            resetForm()
            Toast.fire(
              {
                icon: 'success',
                title: page.props.flash.message
              })
          },

          onError: (errors) => {
            closeModal()
            console.log('error ..'.errors)
          }

        })

      }

    }
    function edit(data) {
      form.id = data.id
      form.name = data.name
      form.type = data.type
      form.detail = data.detail
      form.price = data.price
      form.is_active = data.is_active ? true : false
      editMode.value = true
      openModal()
    }

    function deleteRow(data) {
      if (!confirm('Are you sure want to remove?')) return;
      data._method = 'DELETE';
      router.post("/equiptment/" + data.id, data, {
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
          console.log("error ..".errors);
        },
      });
      closeModal()
      resetForm()
    }
    function openModal() {
      isOpen.value = true
    }
    const closeModal = () => {
      isOpen.value = false
      resetForm()
      editMode.value = false
    }
    const searchTsp = () => {
      console.log('search value is' + search.value)
      router.get('/equiptment/', { equiptment: search.value }, { preserveState: true })
    }

    return { form, submit, editMode, isOpen, openModal, closeModal, edit, deleteRow, searchTsp, search }
  }




}
</script>
