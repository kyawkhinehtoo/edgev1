<template>
  <div class="flex w-full justify-end" v-if="permission">
    <a v-if="!add" href="#" @click="addFile()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Add
      File<i class="fas fa-plus-circle opacity-75 lg:ml-1 text-sm"></i></a>
    <a v-if="add" href="#" @click="closeFile()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Close<i
        class="fas fa-times-circle opacity-75 lg:ml-1 text-sm"></i></a>
  </div>
  <div v-if="!file_list" wire:loading class=" w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="file_list && !add">
    <div class="overflow-x-auto rounded shadow">
      <table class="min-w-full divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-50">
        <tr>
        <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 uppercase">#</th>
        <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 uppercase">File</th>
        <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 uppercase">Type</th>
        <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 uppercase">Created By</th>
        <th class="px-4 py-2 text-xs font-semibold  text-gray-600 uppercase text-right">Action</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr v-for="(row, index) in file_list" :key="row.id" class="hover:bg-gray-50 transition">
        <td class="px-4 py-2 whitespace-nowrap">{{ index + 1 }}</td>
        <td class="px-4 py-2 whitespace-nowrap flex items-center gap-2">
          <a :href="row.path" target="_blank" class="text-indigo-600 hover:underline">
          <i class="fas fa-paperclip mr-1"></i>
          {{ row.name }}
          </a>
        </td>
        <td class="px-4 py-2 whitespace-nowrap">{{ row.user?.name }}</td>
        <td class="px-4 py-2 whitespace-nowrap text-right">
          <button @click="deleteFile(row)" class="text-red-600 hover:text-red-800 transition" title="Delete">
          <i class="fa fa-trash"></i>
          </button>
        </td>
        </tr>
        <tr v-if="file_list.length === 0">
        <td colspan="4" class="px-4 py-6 text-center text-gray-400">No files found.</td>
        </tr>
      </tbody>
      </table>
    </div>
  </div>
  <div v-if="add">
    <upload :data="incident_id" @status="checkUpload" />
  </div>
</template>

<script>
import { ref, onMounted, reactive } from "vue";
import Upload from "@/Components/Upload";
import { router } from '@inertiajs/vue3';
export default {
  name: "File",
  components: {
    Upload,
  },
  props: ["data", "permission"],
  setup(props) {
    let loading = ref(false);
    let file_list = ref();
    let add = ref(false);

    let incident_id = ref("");

    function addFile() {
      add.value = true;
      incident_id.value = {
        'customer_id': props.data
      };
    }
    function closeFile() {
      add.value = false;
      calculate();
    }
    function deleteFile(data) {
      console.log(props.permission);
      if (props.permission) {
        Toast.fire({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this data !",
          icon: "warning",
          timer: false,
          position: "center",
          showCancelButton: true,
          dangerMode: true,
        }).then((x) => {
          if (x.isConfirmed) {
            deleteIt(data);

          } else {
            Toast.fire("Your data is safe!");
          }
        });
      }


    }
    function deleteIt(data) {
      data._method = "DELETE";
      router.post("/deleteFile/" + data.id, data, {
        onSuccess(page) {
          Toast.fire({
            icon: "success",
            title: "Your File has been Deleted !",
          });
          calculate();
        }
      });
    }
    const getFile = async () => {
      let url = "/getCustomerFile/" + props.data;
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        // console.log(data);
        return data;
      } catch (err) {
        console.error(err);
      }
    };
    const checkUpload = (s) => {
      if (s) {
        add.value = false;
        calculate();
        Toast.fire({
          icon: "success",
          title: "Successfully Uploaded the File",
        });
      }
    }
    const calculate = () => {
      getFile().then((d) => {
        file_list.value = d;
      });
    }
    onMounted(() => {
      calculate();
    });
    return { file_list, add, deleteFile, addFile, closeFile, checkUpload, incident_id, loading };
  },
};
</script>
