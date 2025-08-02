<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Service Request</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap">
            <span
              class="z-10 h-full leading-snug font-normal absolute text-center text-gray-300  bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative  bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search_txt" v-model="search_txt" v-on:keyup.enter="search" />
          </div>
          <div class="flex">
            <button :class="{ 'bg-gray-600': status == 'active' }"
              class="inline-flex btn py-3 px-8 bg-gray-400 rounded rounded-r-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner  text-gray-50 text-xs font-semibold uppercase"
              @click="goActive">Active</button>
            <button :class="{ 'bg-gray-600': status == 'close' }"
              class="inline-flex  btn py-3 px-8 bg-gray-400 rounded rounded-l-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner text-gray-50 text-xs font-semibold uppercase"
              @click="goClose">Closed</button>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="incidents.data">


          <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('id')">No.<span v-if="sort == 'id'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span> </th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('date')">Date <span v-if="sort == 'date'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('cid')">Customer ID <span v-if="sort == 'cid'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('id')">Incident ID <span v-if="sort == 'id'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('type')">Request Type <span v-if="sort == 'type'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('start')">Effective From <span v-if="sort == 'start'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('request')">Requested By <span v-if="sort == 'request'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('status')">Status <span v-if="sort == 'status'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in incidents.data" v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ (index += incidents.from) }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.date }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.code }}</td>
                <td class="px-6 py-3 whitespace-nowrap capitalize">{{ row.type.replace(/_/g, " ") }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.start_date }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.incharge }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ getStatus(row.status) }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" @click="edit(row)" class="text-yellow-600 hover:text-yellow-900"> <i
                      class="fa fas fa-pen">
                    </i> Open</a>
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
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="inline-flex w-full bg-gray-50 rounded-t-lg border-b">
                  <ul id="tabs" class="flex">
                    <li class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                        @click="tabClick(1)" preserve-state>General</a></li>

                    <li class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"
                      v-if="form.type == 'relocation'"><a href="#" @click="tabClick(2)" preserve-state>Assign Team</a>
                    </li>


                  </ul>
                </div>
                <div v-if="tab == 1">

                  <div class="py-4   border-gray-100">
                    <div class="mt-1 px-3 flex w-full justify-between">
                      <div class="flex">
                        <h2 class="font-semibold text-md text-gray-800 leading-tight capitalize">Request for :
                          {{ form.type }}
                        </h2>
                      </div>
                      <div class="flex"><label class="font-semibold text-sm text-gray-800 leading-tight"> Date :
                          {{ form.date }} </label> </div>
                    </div>
                  </div>
                  <div class="bg-white px-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2  gap-2">

                      <!-- general -->
                      <div class="col-span-2 mt-2">
                        <h2 class="text-sm font-semibold">General </h2>
                      </div>
                      <div class="col-span-1 grid grid-cols-2 gap-2">
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label for="code" class="block text-gray-700 text-sm font-bold mr-2">Ticket ID:</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label class="block w-full sm:text-sm ">{{ form.code }}</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label for="request_by" class="block text-gray-700 text-sm font-bold">Requested
                            By:</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label class="block w-full sm:text-sm ">{{ form.request_by.name }}</label>
                        </div>

                      </div>
                      <div class="col-span-1 grid grid-cols-2 gap-2">
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label for="ftth_id" class="block text-gray-700 text-sm font-bold">Customer
                            ID:</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label class="block w-full sm:text-sm">{{ form.ftth_id }}</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label for="status" class="block text-gray-700 text-sm font-bold">Customer
                            Status:</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label class="block w-full sm:text-sm">{{ form.status }}</label>
                        </div>
                      </div>



                      <!-- end of general -->
                       <div class="col-span-1 grid grid-cols-2 gap-2">
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.current_bandwidth">
                        <label for="current_package" class="block text-gray-700 text-sm font-bold">Current
                          Plan:</label>
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.current_bandwidth">

                        <input type="text" :class="{ 'ring-1 ring-blue-200': form.current_bandwidth }"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="current_package" :value='`${form.current_bandwidth}`' :readonly="true" />
                      </div>

                       <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.maintenance_service_name">
                        <label for="current_package" class="block text-gray-700 text-sm font-bold">Current
                          Maintenance Plan:</label>
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.maintenance_service_name">

                        <input type="text" :class="{ 'ring-1 ring-blue-200': form.maintenance_service_name }"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="current_package" :value='`${form.maintenance_service_name}`' :readonly="true" />
                      </div>

                      </div>
                      <div class="col-span-1 grid grid-cols-2 gap-2">
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_bandwidth">
                        <label for="current_package" class="block text-gray-700 text-sm font-bold">New
                          Plan:</label>
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_bandwidth">

                        <input type="text" :class="{ 'ring-1 ring-blue-200': form.new_bandwidth }"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="current_package" :value='`${form.new_bandwidth}`' :readonly="true" />
                      </div>

                       <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_maintenance_service_name">
                        <label for="current_package" class="block text-gray-700 text-sm font-bold">New
                          Maintenance Plan:</label>
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_maintenance_service_name">

                        <input type="text" :class="{ 'ring-1 ring-blue-200': form.new_maintenance_service_name }"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="current_package" :value='`${form.new_maintenance_service_name}`' :readonly="true" />
                      </div>

                      </div>
                    </div>
                    <template v-if="form.new_address">
                      <div class="grid grid-cols-1 sm:grid-cols-2  gap-2">

                        <div class="col-span-2">
                          <hr />
                          <h2 class="mt-2 text-sm font-semibold">Address </h2>
                        </div>
                        <div class="col-span-1 grid grid-cols-2 gap-2">
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label for="current_address" class="block text-gray-700 text-sm font-bold">Current
                              Address:</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label class="block w-full sm:text-sm"> {{ form.current_address }}</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label for="current_township" class="block text-gray-700 text-sm font-bold">Current
                              Township:</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label class="block w-full sm:text-sm">{{ form.current_township }}</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label for="current_township" class="block text-gray-700 text-sm font-bold">Current
                              Lat Long:</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label class="block w-full sm:text-sm">{{ form.current_location }}</label>
                          </div>
                        </div>
                        <div class="col-span-1 grid grid-cols-2 gap-2">
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label for="new_address" class="block text-gray-700 text-sm font-bold">New
                              Address:</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label class="block w-full sm:text-sm"> {{ form.new_address }}</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label for="new_township" class="block text-gray-700 text-sm font-bold">New
                              Township:</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label class="block w-full sm:text-sm">{{ form.new_township.name }}</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label for="current_township" class="block text-gray-700 text-sm font-bold">New
                              Lat Long:</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label class="block w-full sm:text-sm">{{ form.new_location }}</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label for="current_township" class="block text-gray-700 text-sm font-bold"> Relocation Package
                              :</label>
                          </div>
                          <div class="mb-4 col-span-1 sm:col-span-1">
                            <label class="block w-full sm:text-sm">{{ form.relocation_service }}</label>
                          </div>
                        </div>

                      </div>


                    </template>
                    <div>

                      <!-- Effective Date From / To -->
                      <template v-if="form.start_date">
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label for="start_date" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Effective From
                            :</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-2">
                          <input type="text"
                            class="mt-1 ring-1 ring-green-200 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            id="start_date" v-model="form.start_date" :readonly="true" />
                        </div>

                      </template>

                      <template v-if="form.end_date">
                        <div class="mb-4 col-span-1 sm:col-span-1">
                          <label for="end_date" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Effective To
                            :</label>
                        </div>
                        <div class="mb-4 col-span-1 sm:col-span-2">
                          <input type="text"
                            class="mt-1 ring-1 ring-green-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            id="end_date" v-model="form.end_date" :readonly="true" />
                        </div>
                      </template>


                      <!-- End of Effective Date From / To -->
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.description">
                        <label for="description"
                          class="block text-gray-700 text-sm font-bold mt-3 mr-2">Description:</label>
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-5" v-if="form.description">

                        <textarea
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="description" :value='`${form.description}`' :readonly="true" />
                      </div>
                    </div>
                  </div>


                </div>
                <div v-if="tab == 2" class="p-4">
                  <div class="grid grid-cols-1 sm:grid-cols-4  gap-2">
                    <div class="col-span-4">
                      <h2 class="mt-2 text-sm font-semibold">ODN Information </h2>
                    </div>

                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Current Partner :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1">
                      <label class="block w-full sm:text-sm py-2.5 px-2 rounded shadow-sm border border-gray-200">{{
                        form.current_partner ?? 'NA' }}</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold ">Current POP :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1">
                      <label class="block w-full sm:text-sm py-2.5 px-2 rounded shadow-sm border border-gray-200">{{
                        form.current_pop ?? 'NA' }}</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Current OLT :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1">
                      <label class="block w-full sm:text-sm py-2.5 px-2 rounded shadow-sm border border-gray-200">{{
                        form.current_pop_device ?? 'NA' }}</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Current DN Splitter :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1">
                      <label class="block w-full sm:text-sm py-2.5 px-2 rounded shadow-sm border border-gray-200">{{
                        form.current_dn_splitter ?? 'NA' }}</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Current SN Splitter :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1">
                      <label class="block w-full sm:text-sm py-2.5 px-2 rounded shadow-sm border border-gray-200">{{
                        form.current_sn_splitter ?? 'NA' }}</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Current SN Port No. :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1">
                      <label class="block w-full sm:text-sm py-2.5 px-2 rounded shadow-sm border border-gray-200">{{
                        form.current_port_number ? 'SN Port: ' +
                          form.current_port_number : 'NA' }}</label>
                    </div>
                    <hr class="col-span-4 py-2" />

                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">New Partner :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <span v-if="partners.length !== 0">
                        <multiselect deselect-label="Selected already" :options="partners" track-by="id" label="name"
                          v-model="form.new_partner" :allow-empty="true"
                          @update:modelValue="form.new_partner_id = $event?.id">
                        </multiselect>
                      </span>

                      <div v-if="errors?.new_partner_id" class="text-red-500 text-xs mt-1">{{ errors.new_partner_id }}
                      </div>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">New POP :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center flex-wrap text-sm">
                      <span v-if="filteredPops.length !== 0">
                        <multiselect deselect-label="Selected already" :options="filteredPops" track-by="id"
                          label="site_name" v-model="form.new_pop" :allow-empty="false"
                          @update:modelValue="form.new_pop_id = $event?.id">
                        </multiselect>
                      </span>
                      <span v-else>
                        Please select Partner
                      </span>

                      <div v-if="errors?.new_pop_id && !form.new_pop_id" class=" text-red-500 text-xs mt-1">{{
                        errors.new_pop_id }}</div>
                    </div>

                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">New OLT :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <span v-if="popDevices.length !== 0">
                        <multiselect deselect-label="Selected already" :options="popDevices" track-by="id"
                          label="device_name" v-model="form.new_pop_device" :allow-empty="false"
                          @update:modelValue="form.new_pop_device_id = $event?.id">
                        </multiselect>
                      </span>
                      <span v-else>Please select POP site</span>

                      <div v-if="errors?.new_pop_device_id && !form.new_pop_device_id"
                        class="text-red-500 text-xs mt-1">{{
                          errors.new_pop_device_id }}</div>
                    </div>

                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">New DN Splitter :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <span v-if="dnOptions.length !== 0">
                        <multiselect deselect-label="Selected already" :options="dnOptions" track-by="id" label="name"
                          v-model="form.new_dn_splitter" :allow-empty="false"
                          @update:modelValue="form.new_dn_splitter_id = $event?.id">
                        </multiselect>
                      </span>
                      <span v-else>Please select OLT</span>
                      <div v-if="errors?.new_dn_splitter_id && !form.new_dn_splitter_id"
                        class="text-red-500 text-xs mt-1">
                        {{ errors.new_dn_splitter_id }}</div>
                    </div>

                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Current SN Splitter :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <span v-if="snOptions.length !== 0">
                        <multiselect deselect-label="Selected already" :options="snOptions" track-by="id" label="name"
                          v-model="form.new_sn_splitter" :allow-empty="true"
                          @update:modelValue="form.new_sn_splitter_id = $event?.id">
                        </multiselect>
                      </span>
                      <span v-else>Please DN Splitter</span>
                      <div v-if="errors?.new_sn_splitter_id && !form.new_sn_splitter_id"
                        class="text-red-500 text-xs mt-1">
                        {{ errors.new_sn_splitter_id }}</div>
                    </div>


                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">SN Port No :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <span v-if="snPortNoOptions.length !== 0">
                        <multiselect deselect-label="Selected already" :options="snPortNoOptions" track-by="id"
                          label="name" v-model="form.new_port_number" :allow-empty="true"
                          @update:modelValue="form.new_port_number_id = $event?.id">
                          <template v-slot:singleLabel="{ option }"><strong>{{ option.name }}
                              {{ option.language }}</strong></template>
                        </multiselect>
                      </span>
                      <span v-else>Please SN Splitter</span>
                      <div v-if="errors?.new_port_number_id && !form.new_port_number_id"
                        class="text-red-500 text-xs mt-1">
                        {{ errors.new_port_number_id }}</div>
                    </div>
                   
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Assign Subcon :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <span v-if="subcons.length !== 0 && form.new_port_number_id">
                        <multiselect deselect-label="Selected already" :options="subcons" track-by="id" label="name"
                          v-model="form.subcon" :allow-empty="true" @update:modelValue="form.subcon_id = $event?.id">
                        </multiselect>
                      </span>
                      <span v-else>
                        Please Select SN Splitter Port
                      </span>

                      <div v-if="errors?.subcon_id && !form.subcon_id" class="text-red-500 text-xs mt-1">{{
                        errors.subcon_id
                        }}</div>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Assign Date :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <input type="date" v-model="form.assign_date"
                        class="text-gray-600 border-gray-300 rounded-md text-sm">
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Task Status :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <label
                        class="text-gray-600 border-gray-300 rounded-md text-sm">
                         {{ getIncidentStatus(form.task_status) }}
                      </label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex items-center">
                      <label class="block text-gray-700 text-sm font-bold">Task Complete At :</label>
                    </div>
                    <div class="mb-4 col-span-1 sm:col-span-1 flex intems-center flex-wrap text-sm">
                      <label
                        class="text-gray-600 border-gray-300 rounded-md text-sm">
                         {{ form.complete_date ? form.complete_date : 'NA' }}
                      </label>
                    </div>
                  </div>

                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-2">
                  <span class="flex w-full rounded-md shadow-sm  sm:w-auto">
                    <button @click="assign" type="button" v-if="form.type == 'relocation'"
                      class="inline-flex items-center shadow-sm px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Assign</button>
                  </span>
                  <span class="flex w-full rounded-md shadow-sm sm:w-auto" v-if="status == 'active'">
                    <button @click="submit" type="button"
                      class="inline-flex items-center shadow-sm px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Process</button>
                  </span>

                  <span class="flex w-full rounded-md shadow-sm sm:w-auto">
                    <button @click="closeModal" type="button"
                      class="inline-flex items-center shadow-sm px-4 py-2 bg-gray-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-400 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Close</button>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <span v-if="incidents.links">
          <pagination class="mt-6" :links="incidents.links" />
        </span>
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
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { onMounted, reactive, ref, onUpdated, watch } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "ServiceRequest",
  components: {
    AppLayout,
    Multiselect,
    Pagination
  },
  props: {
    incidents: Object,
    townships: Object,
    packages: Object,
    partners: Object,
    subcons: Object,
    users: Object,
    relocationServices: Object,
    errors: Object,
  },
  setup(props) {
    const form = reactive({
      id: null,
      code: null,
      customer_id: null,
      ftth_id: null,
      date: null,
      start_date: null,
      end_date: null,
      type: null,
      request_by: null,
      status: null,
      status_id: null,
      current_package: null,
      new_package: null,
      current_address: null,
      new_address: null,
      current_location: null,
      new_location: null,
      current_township: null,
      new_township: null,
      description: null,
      current_bandwidth: null,
      maintenance_service_name: null,
      new_bandwidth: null,
      new_maintenance_service_name: null,
      current_partner: null,
      current_pop: null,


      current_sn_splitter_id: null,
      current_dn_splitter_id: null,
      current_pop_device_id: null,

      current_sn_splitter: null,
      current_dn_splitter: null,
      current_pop_device: null,

      current_port_number: null,

      new_partner: null,
      new_partner_id: null,
      new_pop: null,
      new_pop_id: null,
      new_pop_device: null,
      new_pop_device_id: null,
      new_sn_splitter: null,
      new_sn_splitter_id: null,
      new_dn_splitter: null,
      new_dn_splitter_id: null,
      new_port_number: null,
      new_port_number_id: null,

      subcon: null,
      subcon_id: null,
      assign_date: null,
      task_status: null,
      complete_date: null,
      relocation_service: null,

    });
    //For new ODN 
    const filteredPops = ref([]);
    const popDevices = ref([]);
    const dnOptions = ref([]);
    const snOptions = ref([]);
    const snPortNoOptions = ref([]);

    const search_txt = ref("");
    const sort = ref("");
    const order = ref("desc");
    const status = ref("active");
    let editMode = ref(false);
    let isOpen = ref(false);
    let loading = ref(false);
    const tab = ref(1);
    function tabClick(val) {
      tab.value = val;
    }
    function resetForm() {
      form.id = null;
      form.code = null;
      form.customer_id = null;
      form.ftth_id = null;
      form.date = null;
      form.start_date = null;
      form.end_date = null;
      form.type = null;
      form.request_by = null;
      form.status = null;
      form.status_id = null;
      form.current_package = null;
      form.new_package = null;
      form.current_address = null;
      form.new_address = null;
      form.current_location = null;
      form.new_location = null;
      form.current_township = null;
      form.new_township = null;
      form.description = null;

      form.current_bandwidth = null;
      form.maintenance_service_name = null;
      form.new_bandwidth = null;
      form.new_maintenance_service_name = null; 
      form.current_partner = null;
      form.current_pop = null;

      form.current_sn_splitter_id = null;
      form.current_dn_splitter_id = null;
      form.current_pop_device_id = null;

      form.current_sn_splitter = null;
      form.current_dn_splitter = null;
      form.current_pop_device = null;


      form.current_port_number = null;

      form.new_partner = null;
      form.new_partner_id = null;
      form.new_pop = null;
      form.new_pop_id = null;
      form.new_pop_device = null;
      form.new_pop_device_id = null;
      form.new_sn_splitter = null;
      form.new_sn_splitter_id = null;
      form.new_dn_splitter = null;
      form.new_dn_splitter_id = null;
      form.new_port_number = null;
      form.new_port_number_id = null;

      form.subcon = null;
      form.subcon_id = null;

      form.assign_date = null;
      form.task_status = null;
      form.complete_date = null;
      form.relocation_service = null;

    }
    function getStatus(data) {
      let status = "WIP";
      if (data == 1) {
        status = "WIP";
      } else if (data == 2) {
        status = "Escalated";
      } else if (data == 3) {
        status = "Closed";
      } else if (data == 4) {
        status = "Deleted";
      } else if (data == 5) {
        status = "Resolved Open";
      }
      return status;
    }
    function assign() {

      form._method = "POST";
      router.post("/assign-subcon/" + form.id, form, {
        onSuccess: (page) => {
          closeModal();
          resetForm();
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },

        onError: (errors) => {

          console.log("error ..".errors);
        },
      });

    }
    function submit() {

      form._method = "PUT";
      router.post("/servicerequest/" + form.id, form, {
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

    }
    function edit(data) {
      form.id = data.id;
      form.code = data.code;
      form.customer_id = data.customer_id;
      form.ftth_id = data.ftth_id;
      form.date = data.date;
      form.start_date = data.start_date;
      form.end_date = data.end_date;
      form.type = data.type.replace(/_/g, " ");
      form.request_by = props.users.filter((d) => d.id == data.incharge_id)[0];
      form.status = data.status_name;
      form.status_id = data.status_id;
      form.current_package = props.packages.filter((d) => d.id == data.current_package)[0];
      form.new_package = props.packages.filter((d) => d.id == data.new_package)[0];
      form.current_address = data.customer.current_address?.address;
      form.new_address = data.new_address;
      form.current_location = data.customer.current_address?.location;
      form.new_location = data.location;
      form.current_township = data.customer.current_address?.township.name
      form.new_township = props.townships.filter((d) => d.id == data.new_township)[0];
      form.description = data.description;
      form.current_bandwidth = data.current_bandwidth;
      form.maintenance_service_name = data.maintenance_service_name;
      form.new_bandwidth = data.new_bandwidth;
      form.new_maintenance_service_name = data.new_maintenance_service_name;
      form.current_partner = data.customer.sn_port?.pop?.partner.name;
      form.current_pop = data.customer.sn_port?.pop?.site_name;

      form.current_sn_splitter_id = data.customer.sn_port?.sn_splitter_id;
      form.current_dn_splitter_id = data.customer.sn_port?.dn_splitter_id;
      form.current_pop_device_id = data.customer.sn_port?.pop_device_id;

      form.current_sn_splitter = data.customer.sn_port?.sn_splitter?.name;
      form.current_dn_splitter = data.customer.sn_port?.dn_splitter?.name;
      form.current_pop_device = data.customer.sn_port?.pop_device?.device_name;

      form.current_port_number = data.customer.sn_port?.port_number;

      form.new_partner = data.new_partner_id ? props.partners.filter((d) => d.id === data.new_partner_id) : null;
      form.new_partner_id = data.new_partner_id;
      form.new_pop = null;
      form.new_pop_id = data.new_pop_id;
      form.new_pop_device = null;
      form.new_pop_device_id = data.new_pop_device_id;
      form.new_sn_splitter = null;
      form.new_sn_splitter_id = data.new_sn_splitter_id;
      form.new_dn_splitter = null;
      form.new_dn_splitter_id = data.new_dn_splitter_id;
      form.new_port_number = null;
      form.new_port_number_id = data.new_port_number;
      // Open the modal  
      form.subcon = data?.subcon_id ? props.subcons.find(d => d.id = data?.subcon_id) : null;
      form.subcon_id = data?.subcon_id;
      form.assign_date = data?.assign_date;
      form.task_status = data?.task_status;
      form.complete_date = data?.complete_date;
      form.relocation_service = data?.relocation_service_id ? props.relocationServices.find(d => d.id == data.relocation_service_id)['name'] : null;
      editMode.value = true;
      openModal();
    }

    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/equiptment/" + data.id, data);
      closeModal();
      resetForm();
    }
    function openModal() {
      isOpen.value = true;
    }
    const closeModal = () => {
      isOpen.value = false;
      tab.value = 1;
      resetForm();
      editMode.value = false;
    };
    const goActive = () => {
      status.value = 'active';
      search();
    }
    const goClose = () => {
      status.value = 'close';
      search();
    }
    const getIncidentStatus = ($status) => {
      switch (Number($status)) {
        case 1:
          return 'WIP';
        case 2:
          return 'Completed';
        case 3:
          return 'Pending';
        default:
          return 'WIP';
      }
    }
    const search = () => {

      router.get("/servicerequest/",
        { status: status.value, sort: sort.value, order: order.value, general: search_txt.value },
        {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
          },
          onError: (errors) => {
            loading.value = false;
          },
          onStart: (pending) => {
            loading.value = true;
          }
        });
    };
    const sortBy = (d) => {
      sort.value = d;

      if (order.value == 'asc') {
        order.value = 'desc';
      } else {
        order.value = 'asc';
      }
      console.log("search value is" + sort.value);
      router.get('/servicerequest/',
        { status: status.value, sort: sort.value, order: order.value, general: search_txt.value },
        {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
          },
          onError: (errors) => {
            loading.value = false;
          },
          onStart: (pending) => {
            loading.value = true;
          }
        });
    };

    //Fetch POP by Partner

    const fetchPop = async () => {
      if (!form.new_partner_id) {
        console.log('No Partner');
        filteredPops.value = [];
        return;
      }
      try {
        const response = await fetch(`/getPop/${form.new_partner_id}`);
        const data = await response.json();
        filteredPops.value = data || [];
        console.log('fetch POPs');

      } catch (error) {
        console.error("Failed to fetch POPs", error);
      }
    }
    

    // Fetch OLTs by POP
    const fetchPopDevices = async () => {
      console.log('fetch POP Devices');
      if (!form.new_pop_id) {
        console.log('No POP Selected');
        popDevices.value = [];
        return;
      }
      try {
        const response = await fetch(`/getOLTByPOP/${form.new_pop_id}`);
        const data = await response.json();
        popDevices.value = data || [];
        console.log('fetch POP Devices');
      } catch (error) {
        console.error("Failed to fetch POP devices", error);
      }
    };

    // Fetch DNs by OLT
    const fetchDNs = async () => {
      if (!form.new_pop_device_id) {
        dnOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDNByOLT/${form.new_pop_device_id}`);
        const data = await response.json();
        dnOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch DNs", error);
      }
    };

    // Fetch SNs by DN
    const fetchSNs = async () => {
      if (!form.new_dn_splitter_id) {
        snOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDnId/${form.new_dn_splitter_id}`);
        const data = await response.json();
        snOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch SNs", error);
      }
    };
    // Add this function to fetch available ports by splitter ID
    const fetchAvailablePorts = async () => {
      if (!form.new_sn_splitter_id) {
        // Reset to default ports if no SN is selected
        snPortNoOptions.value = Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }));
        return;
      }
      try {
        const response = await fetch(`/getAvailablePortBySplitterId/${form.new_sn_splitter_id}`);
        const data = await response.json();
        // Update snPortNoOptions with the returned data
        snPortNoOptions.value = data || [];
      } catch (error) {
        snPortNoOptions.value = Array.isArray(data) ? data : [];
        // Fallback to default ports on error
        snPortNoOptions.value = Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }));
      }
    };

    watch(
      () => form.new_partner_id,
      () => {
        (async () => {
          await fetchPop();
          if (form.new_pop_id && filteredPops.value) {
            form.new_pop = filteredPops.value.find(d => d.id === form.new_pop_id);
          }
        })();
        // form.new_pop = null;
        // form.new_pop_id = null;


        form.new_sn_splitter = null;
        // form.new_sn_splitter_id = null;
        form.new_dn_splitter = null;
        // form.new_dn_splitter_id = null;
        form.new_pop_device = null;
        //form.new_pop_device_id = null;
        // form.new_port_number = null;


      }
    );
    watch(
      () => form.new_pop_id,
      () => {
        (async () => {
          await fetchPopDevices();
          if (form.new_pop_device_id && popDevices.value) {
            form.new_pop_device = popDevices.value.find(d => d.id === form.new_pop_device_id);
          }

        })();
        form.new_dn_splitter = null;
        // form.new_dn_splitter_id = null;
        form.new_sn_splitter = null;
        // form.new_sn_splitter_id = null;
        // form.new_port_number = null;
      }
    );
    watch(
      () => form.new_pop_device_id,
      () => {
        (async () => {
          await fetchDNs();
          if (form.new_dn_splitter_id && dnOptions.value) {
            form.new_dn_splitter = dnOptions.value.find(d => d.id === form.new_dn_splitter_id);
          }
        })();
        //form.new_dn_splitter = null;
        // form.new_dn_splitter_id = null;
        form.new_sn_splitter = null;
        // form.new_sn_splitter_id = null;
        // form.new_port_number = null;
      }
    );
    watch(
      () => form.new_dn_splitter_id,
      () => {
        (async () => {
          await fetchSNs();
          if (form.new_sn_splitter_id && snOptions.value) {
            form.new_sn_splitter = snOptions.value.find(d => d.id === form.new_sn_splitter_id);
          }
        })();
        // form.new_sn_splitter = null;
        // form.new_sn_splitter_id = null;
        // form.new_port_number = null;
      }
    );

    // Add a watcher for form.sn_id
    watch(
      () => form.new_sn_splitter_id,
      () => {
        (async () => {
          await fetchAvailablePorts();
          if (form.new_port_number_id && snPortNoOptions.value?.length > 0) {
            form.new_port_number = snPortNoOptions.value.find(d => d.id == form.new_port_number_id); // <-- loose compare
          } else {
            form.new_port_number = null;
          }
        })();
      }
    );
    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.name} - ${x.contract_period} Months Contract`);
      });


    });
    onUpdated(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.name} - ${x.contract_period} Months Contract`);
      });

    });
    return {
      form, submit, editMode, isOpen, goActive, goClose, openModal, closeModal, edit, deleteRow, search, search_txt, getStatus, sortBy, sort, order, status, loading,
      popDevices,
      dnOptions,
      snOptions,
      filteredPops,
      snPortNoOptions,
      tab,
      tabClick,
      assign,
      getIncidentStatus
    };
  },
};
</script>
<style>
.multiselect__single {
  font-size: small;
}
</style>