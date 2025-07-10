<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from "@suadelabs/vue3-multiselect";
import { computed,ref,watch,onMounted } from 'vue';
const props = defineProps({
  coreAssignment: Object,
  fiberCables: Array,
  jcBoxes: Array,
  pops: Object,
  pop_id: {
    type: Number,
    required: true
  },
  pop_device: {
    type: Object,
    required: true
  }
})
const popDevices = ref([]);
const feederCables = ref([]);
const colors = [
  { id: 'blue', name: 'Blue' },
  { id: 'orange', name: 'Orange' },
  { id: 'green', name: 'Green' },
  { id: 'brown', name: 'Brown' },
  { id: 'gray', name: 'Gray' },
  { id: 'white', name: 'White' },
  { id: 'red', name: 'Red' },
  { id: 'black', name: 'Black' }
]

const status = [
  { id: 'active', name: 'Active' },
  { id: 'inactive', name: 'Inactive' }
]

const ports = [
  { id: '1', name: '1' },
  { id: '2', name: '2' },
  { id: '3', name: '3' },
  { id: '4', name: '4' },
  { id: '5', name: '5' },
  { id: '6', name: '6' },
  { id: '7', name: '7' },
  { id: '8', name: '8' },
  { id: '9', name: '9' },
  { id: '10', name: '10' },
  { id: '11', name: '11' },
  { id: '12', name: '12' }
]

const form = useForm({
  source: props.fiberCables?.find(cable => cable.id === props.coreAssignment.source_id),
  source_id: props.coreAssignment.source_id,
  source_color: colors?.find(color => color.id === props.coreAssignment.source_color),
  source_color_id: props.coreAssignment.source_color,
  source_port: ports?.find(port => port.id == props.coreAssignment.source_port),
  source_port_id: props.coreAssignment.source_port,
  dest: props.fiberCables?.find(cable => cable.id === props.coreAssignment.dest_id),
  dest_id: props.coreAssignment.dest_id,
  dest_color: colors?.find(color => color.id === props.coreAssignment.dest_color),
  dest_color_id: props.coreAssignment.dest_color,
  dest_port: ports?.find(port => port.id == props.coreAssignment.dest_port),
  dest_port_id: props.coreAssignment.dest_port,
  jc: props.jcBoxes?.find(jc => jc.id === props.coreAssignment.jc_id),
  jc_id: props.coreAssignment.jc_id,
  status: status?.find(status => status.id === props.coreAssignment.status),
  status_id: props.coreAssignment.status,
  pop: props.pops?.find(pop => pop.id === props.pop_id),
  pop_id: props.pop_id,
  pop_device: props.pop_device,
  pop_device_id: props.pop_device.pop_device_id,
});

const fetchPopDevices = async () => {
      if (!form.pop_id) {
        popDevices.value = [];
        return;
      }
      try {
        const response = await fetch(`/getOLTByPOP/${form.pop_id}`);
        const data = await response.json();
        popDevices.value = data || [];
        console.log('fetch POP Devices');
      } catch (error) {
        console.error("Failed to fetch POP devices", error);
      }
    };
    const fetchFeeders = async () => {
      console.log('pop_device_id',form.pop_device_id);
      if (!form.pop_device_id) {
        feederCables.value = [];
        return;
      }
      try {
        const response = await fetch(`/getFeederByOLT/${form.pop_id}`);
        const data = await response.json();
        feederCables.value = data || [];
        console.log('fetch Feeder Cables');
      } catch (error) {
        console.error("Failed to fetch Feeder Cables", error);
      }
    };
    const changePopDevice = (popDevice) => {
      fetchFeeders();
        form.source = null;
        form.source_id = null;
        form.source_color = null;
        form.source_color_id = null;
        form.source_port = null;
        form.source_port_id = null;
    };
    const changePop = (pop) => {
      fetchPopDevices();
      form.pop_device = null;
      form.pop_device_id = null;
    };
   
   
const submit = () => {
  form.put(route('core-assignments.update', props.coreAssignment.id), {
    onSuccess: (page) => {
      Toast.fire({
        icon: 'success',
        title: page.props.flash.message,
      })
      // Success notification can be added here
    }
  })
}
onMounted(() => {
   fetchPopDevices();
   form.source = props.fiberCables?.find(cable => cable.id === props.coreAssignment.source_id);
   form.source_id =  props.coreAssignment.source_id;
   fetchFeeders();
  
 

});
</script>

<template>
  <AppLayout title="Edit Core Assignment">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Edit Core Assignment
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white  shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Source POP</label>
                  <multiselect deselect-label="Selected already" :options="pops" track-by="id"
                  label="site_name" v-model="form.pop" :allow-empty="false" :multiple="false" tabindex="1" 
                  @update:modelValue="(event) => { form.pop_id = event?.id; changePop(event); }"  v-if="pops?.length > 0">
                </multiselect>
                 <span class="border-gray-200 border p-2 rounded-sm text-gray-600" v-else>No POP Site</span>
                  <div v-if="form.errors.pop_id" class="text-red-500 text-sm mt-1">{{ form.errors.pop_id }}</div>
                </div>
                <div >
                  <label class="block text-sm font-medium text-gray-700">Source OLT (Pop Devices)</label>
                  <multiselect deselect-label="Selected already" :options="popDevices" track-by="id"
                  label="device_name" v-model="form.pop_device" :allow-empty="false" :multiple="false" tabindex="1" 
                  @update:modelValue="{ form.pop_device_id = $event?.id; changePopDevice(event); }" v-if="popDevices?.length > 0">
                </multiselect>
                 <div class="border-gray-200 border p-2 rounded-sm text-gray-600" v-else>Please Select Pop Site</div>
                  <div v-if="form.errors.pop_device_id" class="text-red-500 text-sm mt-1">{{ form.errors.pop_device_id }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Source Cable</label>
                 
                  <multiselect deselect-label="Selected already" :options="feederCables" track-by="id"
                  label="name" v-model="form.source" :allow-empty="false" :multiple="false" tabindex="1" 
                  @update:modelValue="form.source_id = $event?.id" v-if="feederCables?.length > 0">
                </multiselect>
                <div class="border-gray-200 border p-2 rounded-sm text-gray-600" v-else>Please Select OLT</div>
                  <div v-if="form.errors.source_id" class="text-red-500 text-sm mt-1">{{ form.errors.source_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Source Color</label>
                  <multiselect deselect-label="Selected already" :options="colors" track-by="id"
                    label="name" v-model="form.source_color" :allow-empty="false" :multiple="false" tabindex="2"
                    @update:modelValue="form.source_color_id = $event?.id">
                  </multiselect>
                  <div v-if="form.errors.source_color_id" class="text-red-500 text-sm mt-1">{{ form.errors.source_color_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Source Port</label>
                  <multiselect deselect-label="Selected already" :options="ports" track-by="id"
                    label="name" v-model="form.source_port" :allow-empty="false" :multiple="false" tabindex="3"
                    @update:modelValue="form.source_port_id = $event?.id">
                  </multiselect>
                  <div v-if="form.errors.source_port_id" class="text-red-500 text-sm mt-1">{{ form.errors.source_port_id }}</div>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Destination Cable</label>
                  <multiselect deselect-label="Selected already" :options="fiberCables" track-by="id"
                    label="name" v-model="form.dest" :allow-empty="false" :multiple="false" tabindex="4"
                    @update:modelValue="form.dest_id = $event?.id">
                  </multiselect>
                  <div v-if="form.errors.dest_id" class="text-red-500 text-sm mt-1">{{ form.errors.dest_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Destination Color</label>
                  <multiselect deselect-label="Selected already" :options="colors" track-by="id"
                    label="name" v-model="form.dest_color" :allow-empty="false" :multiple="false" tabindex="5"
                    @update:modelValue="form.dest_color_id = $event?.id">
                  </multiselect>
                  <div v-if="form.errors.dest_color_id" class="text-red-500 text-sm mt-1">{{ form.errors.dest_color_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Destination Port</label>
                  <multiselect deselect-label="Selected already" :options="ports" track-by="id"
                    label="name" v-model="form.dest_port" :allow-empty="false" :multiple="false" tabindex="6"
                    @update:modelValue="form.dest_port_id = $event?.id">
                  </multiselect>
                  <div v-if="form.errors.dest_port_id" class="text-red-500 text-sm mt-1">{{ form.errors.dest_port_id }}</div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">JC Box</label>
                <multiselect deselect-label="Selected already" :options="jcBoxes" track-by="id"
                  label="name" v-model="form.jc" :allow-empty="false" :multiple="false" tabindex="7"
                  @update:modelValue="form.jc_id = $event?.id">
                </multiselect>
                <div v-if="form.errors.jc_id" class="text-red-500 text-sm mt-1">{{ form.errors.jc_id }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <multiselect deselect-label="Selected already" :options="status" track-by="id"
                  label="name" v-model="form.status" :allow-empty="false" :multiple="false" tabindex="8"
                  @update:modelValue="form.status_id = $event?.id">
                </multiselect>
                <div v-if="form.errors.status_id" class="text-red-500 text-sm mt-1">{{ form.errors.status_id }}</div>
              </div>

              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('core-assignments.index')"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Update
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>