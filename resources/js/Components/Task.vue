<template>
  <div class="flex w-full justify-end">
    <a v-if="!edit && write_permission == 1 && task_write == 1" href="#" @click="newTask()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Add
      Task<i class="fas fa-plus-circle opacity-75 lg:ml-1 text-sm"></i></a>
    <a v-if="edit && write_permission == 1 && task_write == 1" href="#" @click="saveTask()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-green-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-400 active:bg-green-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1"><span
        v-if="!editMode">Save Task</span><span v-if="editMode">Update Task</span><i
        class="fas fa-save opacity-75 lg:ml-1 text-sm"></i></a>
    <a v-if="edit" href="#" @click="cancelTask()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-gray-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Cancel<i
        class="fas fa-save opacity-75 lg:ml-1 text-sm"></i></a>
  </div>
  <div v-if="!task_list" wire:loading class=" w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="task_list && !edit">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50 text-xs">
        <tr>
          <th scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Instruction</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Assigned</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Target</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Complete At</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Status</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Action</th>
        </tr>
      </thead>
      <tbody
        class="bg-white  text-sm scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-white  text-left">
        <tr v-for="(row, index) in task_list" v-bind:key="row.id">
          <td class="px-6 py-3 whitespace-nowrap">{{ index + 1 }}</td>
          <td class="px-6 py-3 whitespace-nowrap">{{ (row.description.length > 50) ? row.description.substring(0,
            50)
            + ' ...' : row.description }} </td>
          <td class="px-6 py-3 whitespace-nowrap">{{ getName(row.assigned) }}</td>
          <td class="px-6 py-3 whitespace-nowrap">{{ row.target }}</td>
          <td class="px-6 py-3 whitespace-nowrap">{{ row.complete_date??"N/A" }}</td>
          <td class="px-6 py-3 whitespace-nowrap">{{ getStatus(row.status) }}</td>
          <td class="px-6 py-3 whitespace-nowrap" v-if="write_permission == 1"><a href="#"
              @click="editTask(row)" class="text-blue-600"><i class="fa fa-edit"></i></a> <span v-if="task_write"> | <a
                href="#" @click="deleteTask(row)" class="text-red-600"><i class="fa fa-trash"></i></a> </span></td>
          <td class="px-6 py-3 whitespace-nowrap" v-else><a href="#" @click="editTask(row)"
              class="text-blue-600"><i class="fa fa-eye"></i></a> </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div v-if="edit">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 w-full">
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="assigned" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Assigned : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <div class="flex rounded-md shadow-sm w-full" v-if="subcons.length !== 0">
            <multiselect deselect-label="Selected already" :options="subcons" track-by="id" label="name"
              v-model="form.assigned" :allow-empty="false" :multiple="false"></multiselect>
          </div>
          <p v-if="$page.props.errors.assigned" class="mt-2 text-sm text-red-500">{{ $page.props.errors.assigned }}</p>
        </div>
      </div>
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="target" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Target : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <input type="date" v-model="form.target" name="target" id="target"
            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />

        </div>
        <p v-if="$page.props.errors.target" class="mt-2 text-sm text-red-500">{{ $page.props.errors.target }}</p>
      </div>
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="description" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Instruction : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <textarea v-model="form.description" name="description" id="description"
            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"></textarea>

        </div>
        <p v-if="$page.props.errors.description" class="mt-2 text-sm text-red-500">{{ $page.props.errors.description }}
        </p>
      </div>
     
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="status" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Status : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <div class="flex gap-4">
            <label class="inline-flex items-center">
              <input type="radio" v-model="form.status" value="1" class="form-radio text-yellow-500">
              <span class="ml-2">WIP</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" v-model="form.status" value="3" class="form-radio text-red-500">
              <span class="ml-2">Pending</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" v-model="form.status" value="2" class="form-radio text-green-500">
              <span class="ml-2">Completed</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" v-model="form.status" value="0" class="form-radio text-indigo-500">
              <span class="ml-2">Deleted</span>
            </label>
          </div>

        </div>
        <p v-if="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{ $page.props.errors.status }}</p>
      </div>
      <template v-if="form.status == 2">
        <div class="py-2 col-span-1 sm:col-span-1">
          <div class="flex">
            <label for="complete_date" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Complete Date : </label>
          </div>
        </div>
        <div class="py-2 col-span-3 sm:col-span-3">
          <div class="flex rounded-md shadow-sm">
            <input type="date" v-model="form.complete_date" name="complete_date" id="complete_date"
              class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
          </div>
          <p v-if="$page.props.errors.complete_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.complete_date }}
          </p>
        </div>
        </template>
      <template v-if="form.status == 3">
        <div class="py-2 col-span-1 sm:col-span-1">
          <div class="flex">
            <label for="root_causes_id" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Root Cause for Pending
              : </label>
          </div>
        </div>
        <div class="py-2 col-span-3 sm:col-span-3">
          <div class="flex rounded-md shadow-sm w-full" v-if="pendingRootCause?.length !== 0">
            <multiselect deselect-label="Selected already" :options="pendingRootCause" track-by="id" label="name"
              v-model="form.root_causes" :allow-empty="false" :multiple="false"
              @update:model-value="form.root_causes_id = $event?.id"></multiselect>
          </div>
          <p v-if="$page.props.errors.root_causes_id" class="mt-2 text-sm text-red-500">{{
            $page.props.errors.root_causes_id }}</p>
        </div>

        <div class="py-2 col-span-1 sm:col-span-1">
          <div class="flex">
            <label for="sub_root_causes" class="block text-sm font-medium text-gray-700 mt-2 mr-2">Sub Root Cause : </label>
          </div>
        </div>
        <div class="py-2 col-span-3 sm:col-span-3">
          <div class="flex rounded-md shadow-sm w-full" v-if="subRCA?.length !== 0">
            <multiselect deselect-label="Selected already" :options="subRCA" track-by="id" label="name"
              v-model="form.sub_root_causes" :allow-empty="false" :multiple="false"
              @update:model-value="form.sub_root_causes_id = $event?.id"></multiselect>
          </div>

          <p v-if="$page.props.errors.sub_root_causes_id" class="mt-2 text-sm text-red-500">{{
            $page.props.errors.sub_root_causes_id}}</p>
        </div>
      </template>
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="comment" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Comments : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <textarea v-model="form.comment" name="comment" id="comment"
            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"></textarea>

        </div>
        <p v-if="$page.props.errors.comment" class="mt-2 text-sm text-red-500">{{ $page.props.errors.comment }}
        </p>
      </div>
    </div>
  </div>

</template>

<script>
import { ref, onMounted, reactive, inject, watch } from "vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import { router, useForm } from '@inertiajs/vue3';
export default {
  name: "Task",
  components: {
    Multiselect,
  },
  props: ["data","errors"],
  setup(props) {
    const noc = inject("noc");
    const subcons = inject("subcons");
    const write_permission = inject("write_permission");
    const read_permission = inject("read_permission");
    const task_write = inject("task_write");
    const pendingRootCause = inject("pendingRootCause");
    const subRootCause = inject("subRootCause");
    const formErrors = ref({});
    let subRCA = ref([]);
    let task_list = ref();
    let loading = ref(false);
    let edit = ref(false);
    let editMode = ref(false);

    const form = useForm({
      id: null,
      assigned: null,
      target: null,
      status: 1,
      description: null,
      comment: null,
      incident_id: null,
      root_causes: null,
      sub_root_causes: null,
      root_causes_id: null,
      sub_root_causes_id: null,
      complete_date: null,
    });
    function newTask() {
      edit.value = true;
      form.incident_id = props.data;
    }
    function resetForm() {
      form.id = null;
      form.assigned = null;
      form.target = null;
      form.status = 1;
      form.description = null;
      form.comment = null;
      form.incident_id = props.data
      form.root_causes = null;
      form.sub_root_causes = null;
      form.root_causes_id = null;
      form.sub_root_causes_id = null;
      form.complete_date = null;
    }
    function editTask(data) {
      console.log(data);
      form.id = data.id;
      form.assigned = subcons.filter((x) => x.id == data.assigned)[0];
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.status = data.status;
      form.target = data.target;
      form.comment = data.comment;
      form.root_causes = data.root_causes_id ? pendingRootCause.filter((x) => x.id == data.root_causes_id)[0] : null;
      form.sub_root_causes ='';
      form.root_causes_id = data.root_causes_id;
      form.sub_root_causes_id = data.sub_root_causes_id;
      form.complete_date = data.complete_date;

      editMode.value = true;
      edit.value = true;
    }
    function deleteTask(data) {
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
          deleteIt(data)
        } else {
          Toast.fire("Your data is safe!");
        }
      });

    }
    function deleteIt(data) {
      form.id = data.id;
      form.assigned = subcons.filter((x) => x.id == data.assigned)[0];
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.status = 0;
      form.target = data.target;
      editMode.value = true;
      edit.value = false;
      saveTask();
    }
    function completeTask(data) {
      if (data.status != 2) {
        Toast.fire({
          title: "Are you sure?",
          text: "Do you want to mark as completed !",
          icon: "warning",
          timer: false,
          position: "center",
          showCancelButton: true,
          dangerMode: true,
        }).then((x) => {
          if (x.isConfirmed) {
            completeIt(data, 2)
          }
        });
      } else {
        Toast.fire({
          title: "Are you sure?",
          text: "Do you want to mark as WIP !",
          icon: "warning",
          timer: false,
          position: "center",
          showCancelButton: true,
          dangerMode: true,
        }).then((x) => {
          if (x.isConfirmed) {
            completeIt(data, 1)
          }
        });
      }

    }
    function completeIt(data, status) {
      form.id = data.id;
      form.assigned = data.assigned;
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.status = status;
      form.target = data.target;
      editMode.value = true;
      edit.value = false;
      saveTask();
    }
    function saveTask() {
      if (editMode.value != true) {
        form._method = "POST";
        router.post("/addTask", form, {
          preserveState: true,
          onSuccess: (page) => {
            edit.value = false;
            resetForm();
            calculate();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            console.log("error ..",errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/editTask/" + form.id, form, {
          onSuccess: (page) => {
            edit.value = false;
            editMode.value = false;
            resetForm();
            calculate();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
            closeModal();
          },
          onError: (errors) => {
            formErrors.value = errors;
            console.log("error ..",errors);
          },
        });
      }
    }
    function cancelTask() {
      edit.value = false;
      preserveState = true;
    }

    const getTask = async () => {
      let url = "/getTask/" + props.data;
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
    const getName = (data) => {
      let subconName = subcons.filter((x) => x.id == data)[0];
      return subconName ? subconName['name'] : '';
    }
    function getStatus(data) {
      let status = "WIP";
      if (data == 0) {
        status = "Deleted";
      } else if (data == 1) {
        status = "WIP";
      }  else if (data == 2) {
        status = "Completed";
      } else if (data == 3) {
        status = "Pending";
      }
      return status;
    }
    function isJsonString(str) {
      console.log(str);
      try {
        const parsedValue = JSON.parse(str);
        return typeof parsedValue === 'object' && parsedValue !== null;
      } catch (e) {
        console.log('not json');
        return false;
      }

    }
    const calculate = () => {
      getTask().then((d) => {
        task_list.value = d;
      });
    }
    function getSubRCA(data) {

      subRCA.value = [];

      let RCA = pendingRootCause.filter((d) => d.id == data.id);
      subRCA.value = RCA[0]?.sub_root_causes;
      if (form.sub_root_causes_id) {
        form.sub_root_causes = subRCA.value.filter((d) => d.id == form.sub_root_causes_id)[0];
        if (!form.sub_root_causes) {
          form.sub_root_causes_id = null;
        }
      }
    }
    watch(() => form.root_causes, (newVal) => {
      if (newVal)
        getSubRCA(newVal);
    });
    onMounted(() => {
      console.log(task_list.value);
      calculate();
    });
    return { loading, task_list, edit, editMode, newTask, saveTask, cancelTask, getName, getStatus, editTask, deleteTask, completeTask, form, noc, subcons, write_permission, read_permission, task_write, pendingRootCause, subRCA ,formErrors};
  },
};
</script>
