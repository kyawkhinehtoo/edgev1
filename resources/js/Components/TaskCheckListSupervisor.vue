<template>
  <div>
    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
      Installation Data
     
    </h6>
    <div class="overflow-x-auto rounded-lg shadow mb-6 mt-4">
      <table class="min-w-full divide-y divide-gray-200 bg-white">
        <thead class="bg-indigo-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Group Name</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Total</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Skip</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Requested</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Rejected</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Approved</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Remaining</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="group in checkListSummary"
            :key="group.group_name"
            @click="selectGroup(group.id)"
            :class="['cursor-pointer transition-colors', selectedGroupId === group.id ? 'bg-indigo-100' : 'hover:bg-gray-50']"
          >
            <td class="px-4 py-2 text-blue-600 underline font-medium">{{ group.group_name }}</td>
            <td class="px-4 py-2 text-center">
              <span v-if="group.total > 0" class="px-2 py-1 rounded-xl bg-yellow-100 text-yellow-700 text-xs font-semibold">{{ group.total }}</span>
              <span v-else class="text-gray-400">{{ group.total }}</span>
            </td>
            <td class="px-4 py-2 text-center">
              <span v-if="group.skip > 0" class="px-2 py-1 rounded-xl bg-indigo-100 text-indigo-700 text-xs font-semibold">{{ group.skip }}</span>
              <span v-else class="text-gray-400">{{ group.skip }}</span>
            </td>
            <td class="px-4 py-2 text-center">
              <span v-if="group.requested > 0" class="px-2 py-1 rounded-xl bg-indigo-100 text-indigo-700 text-xs font-semibold">{{ group.requested }}</span>
              <span v-else class="text-gray-400">{{ group.requested }}</span>
            </td>
            <td class="px-4 py-2 text-center">
              <span v-if="group.rejected > 0" class="px-2 py-1 rounded-xl bg-red-100 text-red-700 text-xs font-semibold">{{ group.rejected }}</span>
              <span v-else class="text-gray-400">{{ group.rejected }}</span>
            </td>
            <td class="px-4 py-2 text-center">
              <span v-if="group.approved > 0" class="px-2 py-1 rounded-xl bg-green-100 text-green-700 text-xs font-semibold">{{ group.approved }}</span>
              <span v-else class="text-gray-400">{{ group.approved }}</span>
            </td>
            <td class="px-4 py-2 text-center">
              <span v-if="group.remaining > 0" class="px-2 py-1 rounded-xl bg-gray-100 text-gray-700 text-xs font-semibold">{{ group.remaining }}</span>
              <span v-else class="text-gray-400">{{ group.remaining }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div ref="showModal" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="filteredChecklists && showModal">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full"
          role="dialog" aria-modal="true" aria-labelledby="modal-headline"
        >
          <form @submit.prevent="submit">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div>
                <div v-if="selectedGroupId" class="mt-2 text-sm text-indigo-600 font-semibold">
                  Showing details for: <span class="underline">{{ checkListSummary.find(g => g.id === selectedGroupId)?.group_name }}</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div v-for="(checklist, index) in filteredChecklists" :key="checklist.id" class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">{{ checklist.name }}</label>
                    <input
                      type="text"
                      v-model="form[`checklist_${checklist.id}_title`]"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <div class="mt-2" v-if="checklist.has_attachment">
                       <ImageUploader
                                                            v-model="form[`checklist_${checklist.id}_attachment`]"
                                                            v-model:imageUrl="checklistImagePreviews[checklist.id]"
                                                            :existingImage="taskCheckList.checklist_images && taskCheckList.checklist_images[checklist.id] ? `/storage/${taskCheckList.checklist_images[checklist.id]}` : null"
                                                            :upload-text="`Upload ${checklist.name} image`"
                                                            :submitStatus="taskCheckList.checklist_values[checklist.id]?.status" />
                    </div>
                    <div class="py-2">
                      <label for="status" class="block text-md font-medium text-gray-700">Status</label>
                      <div class="mt-2 flex">
                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-yellow-600"
                                            :name="form[`checklist_${checklist.id}`]"
                                            v-model="form[`checklist_${checklist.id}_status`]"
                                            value="skip" :disabled="user.role?.incident_supervisor" /><span
                                            class="ml-2 text-gray-700 text-sm">Skip</span></label>
                                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-yellow-600"
                                            :name="form[`checklist_${checklist.id}`]"
                                            v-model="form[`checklist_${checklist.id}_status`]"
                                            value="requested" :disabled="user.role?.incident_supervisor"  /><span
                                            class="ml-2 text-gray-700 text-sm">Request</span></label>
                                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-red-600"
                                            :name="form[`checklist_${checklist.id}`]"
                                            v-model="form[`checklist_${checklist.id}_status`]" value="declined" :disabled="user.role?.installation_oss"  /><span
                                            class="ml-2 text-gray-700 text-sm">Declined</span></label>
                                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-green-600"
                                            :name="form[`checklist_${checklist.id}`]"
                                            v-model="form[`checklist_${checklist.id}_status`]" value="approved" :disabled="user.role?.installation_oss" /><span
                                            class="ml-2 text-gray-700 text-sm">Approved</span></label>
                      </div>
                    </div>
                  </div>
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
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import ImageUploader from '@/Components/ImageUploader.vue';
import CustomerFile from "@/Components/CustomerFile";
export default {
  name: "TaskCheckList",
  components: {
    ImageUploader,
    CustomerFile
  },
  props: {
    taskId: {
      type: [String, Number],
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const subconCheckList = ref([]);
    const checkListSummary = ref([]);
    const taskCheckList = ref([]);
    const selectedGroupId = ref(null);
    const showModal = ref(false);
   
    const checklistImagePreviews = ref({})
    const form = ref(null);

    const initializeForm = () => {
      form.value = useForm({
        task_id: props.taskId,
        ...Object.fromEntries(
          Object.values(subconCheckList.value).flatMap(checklist => [
            [`checklist_${checklist.id}_title`, taskCheckList.value.checklist_values?.[checklist.id]?.title || ''],
            [`checklist_${checklist.id}_attachment`, null],
            [`checklist_${checklist.id}_status`, taskCheckList.value.checklist_values?.[checklist.id]?.status || '']
          ])
        ),
      });
    };

    const fetchCheckListData = async () => {
      try {
        const response = await fetch(`/getTaskCheckList/${props.taskId}`);
        const data = await response.json();
        subconCheckList.value = data.subconCheckList || [];
        checkListSummary.value = data.checkListSummary || [];
        taskCheckList.value = data.taskCheckList || [];
        initializeForm(); // Re-initialize form after data is loaded
      } catch (error) {
        console.error("Failed to fetch checklist data:", error);
      }
    };

    onMounted(() => {
      fetchCheckListData();
    });

    const filteredChecklists = computed(() => {
     if (!selectedGroupId.value) return null;
      const group = checkListSummary.value.find(g => g.id === selectedGroupId.value);
      if (!group || group.total === group.remaining) return null;
      return subconCheckList.value.filter(checklist => checklist.group_id === selectedGroupId.value);
    });

    function selectGroup(id) {
      selectedGroupId.value = id;
      showModal.value = true;
    }
    function closeModal() {
      selectedGroupId.value = null;
      showModal.value = false;
    }
    function handleFileUpload(event, checklistId) {
      const file = event.target.files[0];
      if (form.value) {
        form.value[`checklist_${checklistId}_attachment`] = file;
      }
    }
    function submit() {
      if (!form.value) return;
      form.value.post(route('taskCheckList.update', props.taskId), {
        preserveScroll: true,
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeModal();
        },
      });
    }
    return {
      subconCheckList,
      checkListSummary,
      selectedGroupId,
      showModal,
      form,
      filteredChecklists,
      taskCheckList,
      checklistImagePreviews,
      selectGroup,
      closeModal,
      handleFileUpload,
      submit,
    };
  },
};
</script>
<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>