<template>
  <app-layout>
    <div class="py-1">

      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-2 w-full justify-items-end" v-if="!edit">
          <div class="flex w-full">
            <span
              class="z-10 mt-1 leading-snug font-normal text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" placeholder="ID/Description"
              class="pl-10 pr-12 py-2.5 w-full rounded-lg overflow-hidden text-sm text-gray-700 placeholder-gray-400 transition-colors bg-white border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-500 focus:ring-opacity-10 focus:outline-none dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-500 dark:focus:ring-opacity-20"
              id="search" tabindex="1" v-model="search" @keyup.enter="searchTask" />
          </div>
          <div class="flex w-full items-center justify-end ">
            <span class="mr-3 text-sm font-medium text-gray-700">
              {{ task_user === 'my' ? 'My Tasks' : 'All Tasks' }}
            </span>
            <label class="inline-flex relative cursor-pointer  ">
              
              <input type="checkbox" 
                     class="sr-only peer" 
                     :checked="task_user === 'all'" 
                     @change="task_user === 'my' ? goAllTasks() : goMyTasks()">
              <div class="w-14 h-8 bg-gray-400 peer-focus:outline-none rounded-full peer 
                          peer-checked:after:translate-x-6 peer-checked:after:border-white 
                          after:content-[''] after:absolute after:top-[4px] after:left-[4px] 
                          after:bg-white after:border-gray-300 after:border after:rounded-full 
                          after:h-6 after:w-6 after:transition-all 
                          peer-checked:bg-indigo-600 shadow-sm" ></div>
              
            </label>
          </div>
          <div class="flex w-full ">
            <button
              class="w-32 btn py-3 px-8 bg-gray-400 rounded rounded-r-none focus:ring-0 focus:border-transparent focus:outline-none shadow-lg  text-gray-50 text-xs font-semibold uppercase border-r-gray-300 border-r"
              :class="{ 'bg-yellow-300 text-gray-600 font-bold': search_status == 1 }" @click="goSearch(1)">WIP</button>
              <button
              class="w-32 btn py-3 px-8 bg-gray-400  focus:ring-0 focus:border-transparent focus:outline-none shadow-lg text-gray-50 text-xs font-semibold uppercase border-r-gray-300 border-r"
              :class="{ 'bg-yellow-500 text-gray-600 font-bold': search_status == 3 }"
              @click="goSearch(3)">Pending</button>
            <button
              class="w-32 btn py-3 px-8 bg-gray-400  focus:ring-0 focus:border-transparent focus:outline-none shadow-lg text-gray-50 text-xs font-semibold uppercase border-r-gray-300 border-r"
              :class="{ 'bg-green-300 text-gray-600 font-bold': search_status == 2 }"
              @click="goSearch(2)">Completed</button>
             
            <button
              class=" w-32  btn py-3 px-8 bg-gray-400 rounded rounded-l-none focus:ring-0 focus:border-transparent focus:outline-none shadow-lg text-gray-50 text-xs font-semibold uppercase"
              :class="{ 'bg-indigo-600 font-bold': search_status == 'all' }" @click="goSearch('all')">All</button>
          </div>
         
        </div>

        <div v-if="!edit">
          <div v-if="tasks?.data" class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">
                    Code
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer ID</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Description</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Assigned To</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Assigned By</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Target Date</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 text-sm   w-full text-left">
                <tr v-for="(row, index) in tasks.data" v-bind:key="row.id">
                  <td class="px-2 py-3 whitespace-nowrap"><i class="fa fa-circle mr-2"  :class="{'text-yellow-400': row.priority=='normal','text-yellow-600': row.priority=='high','text-red-600': row.priority=='critical' }"></i> {{ (index += tasks.from) }}</td>

                  <td class="px-6 py-3 whitespace-nowrap">{{ row.code }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.ftth_id }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.description?.substring(0, 50) }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ getName(row.assigned) }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.incharge.match(/\b\w/g).join("") }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.target }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ getStatus(row.status) }}</td>
                  <td class="px-6 py-3 whitespace-nowrap"><a href="#" @click="editTask(row)" class="text-blue-600"><i
                        class="fa fa-edit"></i></a></td>

                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="tasks?.data" class="grid grid-cols-1 sm:grid-cols-2 gap-2 md:hidden">
            <div class="bg-white p-4 rounded-lg shadow mt-2 space-y-1 text-gray-600" v-for="(row, index) in tasks.data"
              v-bind:key="row.id">
              <div class="flex items-center justify-between space-x-2 text-sm">
                <label class="font-bold">
                  {{ row.ftth_id }}
                </label>

                <div>
                  <span class="text-xs font-bold px-2.5 py-0.5 rounded items-center justify-center "
                    :class="{ 'bg-yellow-400': row.status == 1 ,'bg-yellow-500': row.status == 3 , 'bg-green-300': row.status != 1 }">
                    {{ getStatus(row.status) }}</span>
                </div>
              </div>
              <div class="text-sm text-gray-600">
                <label for="details" class="w-full font-bold">
                  Description
                </label>
                <p class="font-sm p-2 capitalize">
                  {{ row.description }}
                </p>
              </div>
              <span class="mt-4">
                <div class="text-xs text-gray-700">
                  Assigned To : {{ row.assigned_to }}
                </div>
                <div class="text-xs text-gray-700">
                  Assigned By : {{ row.incharge }}
                </div>
                <div class="text-xs text-gray-700">
                  Target Date : {{ row.target }}
                </div>
              </span>

              <div class="flex flex-wrap justify-end ">
                <div>
                  <a type="button" @click="editTask(row)" href="#"
                    class="bg-green-100 text-green-800 text-xs font-medium  px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-30 items-center justify-center ">
                    <span class="text-sm">Edit</span>
                    <i class="fa fa-angle-double-right fa-lg ml-2" aria-hidden="true"></i>
                  </a>

                </div>

              </div>
            </div>

          </div>

          <span v-if="tasks.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ tasks.data.length }} Tasks in Current Page. Total Number of
              Tasks : {{ tasks.total }}</label>
          </span>
          <span v-if="tasks.links">
            <pagination class="mt-4" :links="tasks.links" />
          </span>
        </div>
        <!-- Tabs -->
        <div class="inline-flex w-full bg-gray-50 rounded-t-lg" v-if="edit">
          <ul id="tabs" class="flex">
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                @click="tabClick(1)" preserve-state>Genaral</a></li>
            
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                @click="tabClick(2)" preserve-state>Customer Info</a></li>
          
          </ul>
        </div>
        <!-- Tabs -->
        <!-- Tab Contents -->
        <div id="tab-contents">
            <!-- tab1 -->
            <div :class="[tab == 1 ? '' : 'hidden']">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2 w-full bg-white p-4" v-if="edit">
                
                <div class="py-2 col-span-1 w-full ">
                  <div class="flex md:justify-end">
                    <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Code :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 md:col-span-3 col-span-1">
                  <div class="flex">
                    <label for="ftth_id" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2">
                      <i class="fa fa-circle mr-2"  :class="{'text-yellow-400': form.data?.priority=='normal','text-yellow-600': form.data?.priority=='high','text-red-600': form.data?.priority=='critical' }"></i> {{ form.data?.code }}  
                    </label>
                  </div>
                </div>
                <div class="py-2 col-span-1 w-full ">
                  <div class="flex md:justify-end">
                    <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Customer ID :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 md:col-span-3 col-span-1">
                  <div class="flex">
                    <label for="ftth_id" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2">
                      {{ form.data?.ftth_id }}
                    </label>
                  </div>
                </div>
                <div class="py-2 col-span-1 w-full ">
                  <div class="flex md:justify-end">
                    <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Type :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 md:col-span-3 col-span-1">
                  <div class="flex">
                    <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2 capitalize">
                      {{ form.data?.type.replace("_", " ") }}
                    </label>
                  </div>
                </div>
                <div class="py-2 col-span-1 w-full" v-if="form.data?.topic">
                  <div class="flex md:justify-end">
                    <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Topic :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 md:col-span-3 col-span-1" v-if="form.data?.topic">
                  <div class="flex">
                    <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2 capitalize">
                      {{ form.data?.topic.replace("_", " ") }}
                    </label>
                  </div>
                </div>
                <div class="py-2 col-span-1 w-full">
                  <div class="flex md:justify-end">
                    <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Detail :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 md:col-span-3 col-span-1">
                  <div class="flex">
                    <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2  whitespace-normal">
                      {{ form.data?.incident_description }}
                    </label>
                  </div>
                </div>
                <div class="py-2 col-span-1 w-full">
                  <div class="flex md:justify-end">
                    <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Opened At :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 md:col-span-3 col-span-1">
                  <div class="flex">
                    <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2  whitespace-normal">
                      {{ form.data?.date }} : {{ form.data?.time }}
                    </label>
                  </div>
                </div>
                <div class="py-2 col-span-1 w-full ">
                  <div class="flex md:justify-end">
                    <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Assigned : </label>
                  </div>
                </div>
                <div class="md:py-2 md:col-span-3 col-span-1">
                  <div class="flex rounded-md shadow-sm">
                    <div class="flex rounded-md shadow-sm w-full" v-if="subcons.length !== 0">
                      <multiselect deselect-label="Selected already" :options="subcons" track-by="id" label="name"
                        v-model="form.assigned" :allow-empty="false" :multiple="false"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.assigned" class="mt-2 text-sm text-red-500">{{ $page.props.errors.assigned }}
                    </p>
                  </div>
                </div>
                <div class="py-2 col-span-1 sm:col-span-1">
                  <div class="flex md:justify-end">
                    <label for="target" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Target : </label>
                  </div>
                </div>
                <div class="md:py-2 col-span-1 md:col-span-3">
                  <div class="flex rounded-md shadow-sm">
                    <input type="date" v-model="form.target" name="target" id="target"
                      class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />

                  </div>
                  <p v-if="$page.props.errors.target" class="mt-2 text-sm text-red-500">{{ $page.props.errors.target }}</p>
                </div>
                <div class="py-2 col-span-1 sm:col-span-1">
                  <div class="flex md:justify-end">
                    <label for="description" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Description :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 col-span-1 md:col-span-3">
                  <div class="flex rounded-md shadow-sm">
                    <textarea v-model="form.description" name="description" id="description"
                      class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"></textarea>

                  </div>
                  <p v-if="$page.props.errors.description" class="mt-2 text-sm text-red-500">{{ $page.props.errors.description
                  }}
                  </p>
                </div>
              
                <div class="py-2 col-span-1 sm:col-span-1">
                  <div class="flex md:justify-end">
                    <label for="status" class="block text-sm font-medium text-gray-700  md:mr-2"> Status :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 col-span-1 md:col-span-3">
              
                    <div class="flex gap-4">
                      <label class="inline-flex items-center">
                        <input type="radio" v-model="form.status" name="status" value="1" class="form-radio text-yellow-500">
                        <span class="ml-2">WIP</span>
                      </label>
                      <label class="inline-flex items-center">
                        <input type="radio" v-model="form.status" name="status" value="3" class="form-radio text-red-500">
                        <span class="ml-2">Pending</span>
                      </label>
                      <label class="inline-flex items-center">
                        <input type="radio" v-model="form.status" name="status" value="2" class="form-radio text-green-500">
                        <span class="ml-2">Completed</span>
                      </label>
                      <label class="inline-flex items-center">
                        <input type="radio" v-model="form.status" name="status" value="0" class="form-radio text-indigo-500">
                        <span class="ml-2">Deleted</span>
                      </label>
                    </div>
          
              
                  <p v-if="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{ $page.props.errors.status }}</p>
                </div>
                <template v-if="form.status == 3">
                  <div class="py-2 col-span-1">
                    <div class="flex md:justify-end">
                      <label for="root_causes_id" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Root Cause for
                        Pending
                        : </label>
                    </div>
                  </div>
                  <div class="py-2 col-span-1 md:col-span-3">
                    <div class="flex rounded-md shadow-sm w-full" v-if="pendingRootCause?.length !== 0">
                      <multiselect deselect-label="Selected already" :options="pendingRootCause" track-by="id" label="name"
                        v-model="form.root_causes" :allow-empty="false" :multiple="false"
                        @update:model-value="form.root_causes_id = $event?.id"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.root_causes_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.root_causes_id }}</p>
                  </div>

                  <div class="py-2 col-span-1">
                    <div class="flex md:justify-end">
                      <label for="sub_root_causes" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2">Sub Root Cause :
                      </label>
                    </div>
                  </div>
                  <div class="py-2 col-span-1 md:col-span-3">
                    <div class="flex rounded-md shadow-sm w-full" v-if="subRCA?.length !== 0">
                      <multiselect deselect-label="Selected already" :options="subRCA" track-by="id" label="name"
                        v-model="form.sub_root_causes" :allow-empty="false" :multiple="false"
                        @update:model-value="form.sub_root_causes_id = $event?.id"></multiselect>
                    </div>

                    <p v-if="$page.props.errors.sub_root_causes_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.sub_root_causes_id }}</p>
                  </div>
                </template>
                <div class="py-2 col-span-1 sm:col-span-1">
                  <div class="flex md:justify-end">
                    <label for="comment" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Comments :
                    </label>
                  </div>
                </div>
                <div class="md:py-2 col-span-1 md:col-span-3">
                  <div class="flex rounded-md shadow-sm">
                    <textarea v-model="form.comment" name="comment" id="comment"
                      class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"></textarea>

                  </div>
                  <p v-if="$page.props.errors.comment" class="mt-2 text-sm text-red-500">{{ $page.props.errors.comment
                  }}
                  </p>
                </div>
                <div class="col-span-1 md:col-span-4 flex md:justify-end justify-between mt-4">

                  <a href="#" @click="saveTask()"
                    class="text-center px-4 py-3 bg-green-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-400 active:bg-green-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1"><span
                      v-if="!editMode">Save Task</span><span v-if="editMode">Update Task</span><i
                      class="fas fa-save opacity-75 ml-1 text-sm"></i></a>
                  <a href="#" @click="cancelTask()"
                    class="text-center px-4 py-3 bg-gray-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition">Cancel<i
                      class="fas fa-save opacity-75 ml-1 text-sm"></i></a>
                </div>
              </div>
            </div>
            <!-- tab 1-->
            <!--tab 2-->
            <div :class="[tab == 2 ? '' : 'hidden']">
              <customer-detail :data="selected_id" v-if="selected_id && tab ==2 "  />
            </div>
            <!--tab 2-->
        </div>
        <!-- Tab Contents -->
      </div>
    </div>

    <div v-if="loading" wire:loading
      class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
      <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
      <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
      <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/IncidentLayout";
import NoData from "@/Components/NoData";
import Pagination from "@/Components/Pagination";
import { ref, onMounted, reactive, watch, provide } from "vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import { router, Link, useForm } from "@inertiajs/vue3";
import CustomerDetail from "@/Components/CustomerDetail";
export default {
  name: "IncidentTask",
  components: {
    AppLayout,
    Pagination,
    Multiselect,
    NoData,
    Link,
    CustomerDetail
  },
  props: {
    tasks: Object,
    errors: Object,
    task_write: Boolean,
    user: Object,
    noc: Object,
    critical: Number,
    high: Number,
    normal: Number,
    role: Object,
    subcons: Object,
    subcon: Object,
    pendingRootCause: Object,
    subRootCause: Object,
  },
  setup(props) {
    const search = ref("");
    const sort = ref("");
    let loading = ref(false);
    let edit = ref(false);
    let editMode = ref(false);
    let selected_id = ref("");
    let search_status = ref(1);
    let task_user = ref("my");
    let subRCA = ref([]);
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    let isOpen = ref(false);
    provide("user", props.user);

    const form = reactive({
      id: null,
      code:null,
      priority: null,
      assigned: null,
      target: null,
      status: 1,
      description: null,
      comment: null,
      incident_id: null,
      data: null,
      root_causes: null,
      sub_root_causes: null,
      root_causes_id: null,
      sub_root_causes_id: null,
    });
    function resetForm() {
      form.id = null;
      form.code = null;
      form.priority = null;
      form.assigned = null;
      form.target = null;
      form.status = 1;
      form.description = null;
      form.comment = null;
      form.incident_id = props.data;
      form.root_causes = null;
      form.sub_root_causes = null;
      form.root_causes_id = null;
      form.sub_root_causes_id = null;
      selected_id.value = null;
    }
    function editTask(data) {

      form.id = data.id;
      form.code = data.code;
      form.priority = data.priority;
      form.assigned = props.subcons.filter((x) => x.id == data.assigned)[0];
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.comment = data.comment;
      form.status = data.status;
      form.target = data.target;
      form.data = data;
      form.root_causes = data.root_causes_id ? props.pendingRootCause.filter((x) => x.id == data.root_causes_id)[0] : null;
      form.sub_root_causes = null;
      form.root_causes_id = data.root_causes_id;
      form.sub_root_causes_id = data.sub_root_causes_id;
      editMode.value = true;
      edit.value = true;
      selected_id.value = data.incident_id;
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
      form.assigned = props.subcons.filter((x) => x.id == data.assigned)[0];
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
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            console.log("error .." ,errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/editTask/" + form.id, form, {
          onSuccess: (page) => {
            edit.value = false;
            editMode.value = false;
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
    function cancelTask() {
      edit.value = false;
      preserveState = true;
    }
    function getStatus(data) {
      let status = "WIP";
      if (data == 0) {
        status = "Deleted";
      } else if (data == 1) {
        status = "WIP";
      } else if (data == 2) {
        status = "Completed";
      } else if (data == 3) {
        status = "Pending";
      }
      return status;
    }
    function searchTask() {
      let url = "/mytask/";
      router.get(url, { keyword: search.value, status: search_status.value, task: task_user.value }, { preserveState: true });
    }
    function changeStatus() {
      let url = "/mytask/";
      if (search.value != null) {
        url = url + "?keyword=" + search.value;
      }
      if (search_status.value != "") {
        url = url + "&status=" + search_status.value;
      }
      if (task_user.value != "") {
        url = url + "&task=" + task_user.value;
      }
      router.get(url, { keyword: search.value }, { preserveState: true });
    }
   
    function goSearch(data){
      search_status.value = data;
      changeStatus();
    }
    function goMyTasks() {
      task_user.value = 'my';
      changeStatus();
    }
    function goAllTasks() {
      task_user.value = 'all';
      changeStatus();
    }
    const getName = (data) => {
      let subconName = props.subcons.filter((x) => x.id == data)[0];
      return subconName ? subconName['name'] : '';
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
    function getSubRCA(data) {
      subRCA.value = [];
      let RCA = props.pendingRootCause.filter((d) => d.id == data.id);
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
    const tab = ref(1);

    function tabClick(val) {
       tab.value = val;
      }
    return { search, loading, form, formatter, edit, editMode, search_status, task_user, getName, getStatus, editTask, completeIt, saveTask, cancelTask, completeTask, searchTask,  goMyTasks, goAllTasks, changeStatus, subRCA ,goSearch,tabClick,tab,selected_id};
  },
};
</script>