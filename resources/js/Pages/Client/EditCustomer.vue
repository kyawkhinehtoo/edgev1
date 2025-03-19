<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Details</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0 md:col-span-2">
          <form @submit.prevent="submit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="inline-flex w-full bg-gray-50 rounded-t-lg">
                <ul id="tabs" class="flex">
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(1)" preserve-state>Genaral</a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(2)" preserve-state>Documents <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{
                          total_documents }}</span></a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider "
                    :class="[tab == 6 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(6)" preserve-state>Text</a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 3 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(3)" preserve-state>History</a></li>
          
              
                </ul>
              </div>
              <!-- Tab Contents -->
              <div id="tab-contents">
                <!-- tab1 -->
                <div class="p-4" :class="[tab == 1 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Customer
                      Basic Information</h6>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                      <div class="col-span-1 sm:col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Customer Name </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-user"></i>
                          </span>
                          <input type="text" v-model="form.name" name="name" id="name"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Customer Name" required :disabled="checkPerm('name')" />
                        </div>
                        <p v-show="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name
                          }}
                        </p>
                      </div>
                    
                      <div class="col-span-1 sm:col-span-1">
                        <label for="township" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Township </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="townships.length !== 0">
                          <multiselect deselect-label="Selected already" :options="townships" track-by="id" label="name"
                            v-model="form.township" placeholder="Select Township" :allow-empty="false" 
                            :disabled="checkPerm('township_id')" required>
                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.township_id" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.township_id }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-2">
                        <label for="phone_1" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Phone No. (e.g. 09-123/09-456)</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-phone"></i>
                          </span>
                          <input type="text" v-model="form.phone_1" name="phone_1" id="phone_1"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="e.g 0945000111" required :disabled="checkPerm('phone_1')" />
                        </div>
                        <p v-show="$page.props.errors.phone_1" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.phone_1 }}</p>
                      </div>
                    
    
                      
                      <div class="col-span-1 sm:col-span-1">
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                            <i class="fas fa-location-arrow"></i>
                          </span>
                          <input type="text" v-model="form.latitude" name="latitude" id="latitude"
                            class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            v-on:keypress="isNumber(event)" :disabled="checkPerm('location')" />
                        </div>
                        <p v-show="$page.props.errors.latitude" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.latitude }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                            <i class="fas fa-location-arrow"></i>
                          </span>
                          <input type="text" v-model="form.longitude" name="longitude" id="longitude"
                            class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            v-on:keypress="isNumber(event)" :disabled="checkPerm('location')" />
                        </div>
                        <p v-show="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.longitude }}</p>
                      </div>
                      
                      <div class="col-span-1 sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Full Address </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-map-marker-alt"></i>
                          </span>
                          <textarea v-model="form.address" name="address" id="address"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            required :disabled="checkPerm('address')" />
                        </div>
                        <p v-show="$page.props.errors.address" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.address }}</p>
                      </div>
                    </div>
                    <hr class="my-4 md:min-w-full" />
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Order
                      Information</h6>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                      <div class="col-span-1 sm:col-span-1 w-full">
                        <label for="ftth_id" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Customer ID </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-id-badge"></i>
                          </span>
                          <input v-model="form.ftth_id" type="text" name="ftth_id" id="ftth_id"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            required :disabled="checkPerm('ftth_id')" />
                        </div>
                        <p v-show="$page.props.errors.ftth_id" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.ftth_id }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="order_date" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span> Order Date </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="date" v-model="form.order_date" name="order_date" id="order_date"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            required :disabled="checkPerm('order_date')" />
                        </div>
                        <p v-show="$page.props.errors.order_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.order_date }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="package" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Package </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="packages">
                          <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="name"
                            v-model="form.package" :allow-empty="false" :disabled="checkPerm('package_id')">
                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.package" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.package }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">
                          Installation Timeline
                        </label>
                        <div class="mt-1 p-2 space-x-2 inline-flex">
                          <div class="flex items-center">
                            <input type="radio" 
                                   id="24hours" 
                                   name="installation_timeline" 
                                   value="24" 
                                   v-model="form.installation_timeline"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                   :disabled="checkPerm('installation_timeline')" />
                            <label for="24hours" class="ml-2 text-sm text-gray-700">24 Hours</label>
                          </div>
                          <div class="flex items-center">
                            <input type="radio" 
                                   id="48hours" 
                                   name="installation_timeline" 
                                   value="48" 
                                   v-model="form.installation_timeline"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                   :disabled="checkPerm('installation_timeline')" />
                            <label for="48hours" class="ml-2 text-sm text-gray-700">48 Hours</label>
                          </div>
                        </div>
                        <p v-show="$page.props.errors.installation_timeline" class="mt-2 text-sm text-red-500">
                          {{ $page.props.errors.installation_timeline }}
                        </p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="prefer_install_date" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span> Prefer
                          Installation Date </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input v-model="form.prefer_install_date" type="date" name="prefer_install_date"
                            id="prefer_install_date"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('prefer_install_date')" />
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Customer Status </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="status_list.length !== 0">
                          <multiselect deselect-label="Selected already" :options="statusList" track-by="id" label="name" v-model="form.status" :allow-empty="false" :disabled="!disableStatus?checkPerm('status_id'):disableStatus" ></multiselect>
                        </div>
                        <p v-show="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.status
                        }}</p>
                      </div>
                     
                     
    
                      
                      <div class="col-span-1 sm:col-span-2">
                        <label for="order_remark" class="block text-sm font-medium text-gray-700"> Order
                          Remark </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-comment"></i>
                          </span>
                          <textarea name="order_remark" v-model="form.order_remark" id="order_remark"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('order_remark')" />
                        </div>
                        <p v-show="$page.props.errors.order_remark" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.order_remark }}</p>
                      </div>
                      
                   
                   
                    </div>
                    <div v-if="$page.props.login_type=='internal'">
                      <hr class="my-4 md:min-w-full" />
                      <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                        ODN Information</h6>
                      
                      <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                        <div class="col-span-1 sm:col-span-1">
                          <label for="isp" class="block text-sm font-medium text-gray-700"> ISP Assignment </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="isps.length !== 0">
                            <multiselect deselect-label="Selected already" :options="isps" track-by="id" label="name"
                              v-model="form.isp_id" :allow-empty="true" :disabled="checkPerm('isp_id')"></multiselect>
                          </div>
                          <p v-show="$page.props.errors.isp_id" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.isp_id
                          }}</p>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="partner" class="block text-sm font-medium text-gray-700"> Partner </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="partners.length !== 0">
                            <multiselect deselect-label="Selected already" :options="partners" track-by="id" label="name"
                              v-model="form.partner_id" :allow-empty="true" :disabled="checkPerm('partner_id')"></multiselect>
                          </div>
                          <p v-show="$page.props.errors.partner_id" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.partner_id
                          }}</p>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="pop_site" class="block text-sm font-medium text-gray-700">Choose POP Site </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="filteredPops.length !== 0">
                            <multiselect deselect-label="Selected already" :options="filteredPops" track-by="id" label="site_name"
                              v-model="form.pop_id" :allow-empty="false" :disabled="checkPerm('sn_id')"
                         >
                            </multiselect>
                          </div>
                          <div v-else class="text-sm text-gray-500 mt-1">
                            Please select a township first to view available POPs
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="pop_device_id" class="block text-sm font-medium text-gray-700"> Choose OLT</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <multiselect deselect-label="Selected already" :options="popDevices" track-by="id"
                            label="device_name" v-model="form.pop_device_id" :allow-empty="false"
                            :disabled="checkPerm('pop_device_id')" >
                            </multiselect>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose DN
                          </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="dnOptions">
                            <multiselect deselect-label="Selected already" :options="dnOptions" track-by="id" label="name"
                            v-model="form.dn_id" :allow-empty="false"  :disabled="checkPerm('sn_id')">
                          </multiselect>
                          </div>
                        </div>
      
                        <div class="col-span-1 sm:col-span-1">
                          <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose SN
                          </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="snOptions">
                            <multiselect deselect-label="Selected already" :options="snOptions" track-by="id"
                            label="name" v-model="form.sn_id" :allow-empty="true" :disabled="checkPerm('sn_id')">
                          </multiselect>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="splitter_no" class="block text-sm font-medium text-gray-700"> SN Port No. </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="snPortNoOptions.length  !== 0">
                            <multiselect deselect-label="Selected already" :options="snPortNoOptions" track-by="id"
                              label="name" v-model="form.splitter_no" :allow-empty="true" :disabled="checkPerm('sn_id')">
                            </multiselect>
                            <p v-show="$page.props.errors.splitter_no" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.splitter_no }}</p>
                          </div>
                        </div>
      
                        <div class="col-span-1 sm:col-span-1">
                          <label for="port_balance" class="block text-sm font-medium text-gray-700"> Port Balance</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.port_balance" type="number" name="port_balance" id="port_balance"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter Port Balance" :disabled="checkPerm('port_balance')" />
                            <p v-show="$page.props.errors.port_balance" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.port_balance }}</p>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="gpon_ontid" class="block text-sm font-medium text-gray-700"> GPON ONTID </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <multiselect deselect-label="Selected already" :options="gponOnuIdOptions" track-by="id"
                              label="name" v-model="form.gpon_ontid" :allow-empty="true" :disabled="checkPerm('gpon_ontid')">
                            </multiselect>
                            <p v-show="$page.props.errors.gpon_ontid" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.gpon_ontid }}</p>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="service_activation_date" class="block text-sm font-medium text-gray-700"> Service
                            Activation
                            Date </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.service_activation_date" type="date" name="service_activation_date"
                              id="service_activation_date"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              :disabled="checkPerm('service_activation_date')" />
                          </div>
                          <p v-show="$page.props.errors.service_activation_date" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.service_activation_date }}</p>
                        </div>
                        <div class="text-sm text-gray-600 mt-2  col-span-4" v-if="dnInfo">
                          GPON INFO : {{ dnInfo }}
                         
                        </div>
                        </div>
                       <div v-if="form.sn_id">
                        
                      
                        <hr class="my-4 md:min-w-full" />
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                          Installation Information</h6>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                          <div class="col-span-1 sm:col-span-1">
                            <label for="subcom" class="block text-sm font-medium text-gray-700"> Assign Installation Team </label>
                            <div class="mt-1 flex rounded-md shadow-sm" v-if="subcoms.length !== 0">
                              <multiselect deselect-label="Selected already" :options="subcoms" track-by="id" label="name"
                                v-model="form.subcom" :allow-empty="true" :disabled="checkPerm('subcom_id')"></multiselect>
                            </div>
                            <p v-show="$page.props.errors.subcom" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.subcom
                            }}</p>
                          </div>
                          <div class="col-span-1 md:col-span-1">
                            <label for="installation_status" class="block text-sm font-medium text-gray-700">
                              Installation Status
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm" v-if="installationStatus.length !== 0">
                              <multiselect deselect-label="Selected already" :options="installationStatus" track-by="id"
                                label="name" v-model="form.installation_status" :allow-empty="false" :disabled="checkPerm('installation_status')"
                                :multiple="false" :taggable="false"></multiselect>
                            </div>
                            <p v-show="$page.props.errors.installation_status" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.installation_status }}</p>
                          </div>
                          <div class="col-span-1 sm:col-span-1">
                            <label for="way_list_date" class="block text-sm font-medium text-gray-700"> Way List
                              Date </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <span
                                class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                                <i class="fas fa-tools"></i>
                              </span>
                              <input v-model="form.way_list_date" type="date" name="way_list_date"
                                id="way_list_date"
                                class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                :disabled="checkPerm('way_list_date')" />
                            </div>
                            <p v-show="$page.props.errors.way_list_date" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.way_list_date }}</p>
                          </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="installation_date" class="block text-sm font-medium text-gray-700"> Actual Installed
                            Date </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.installation_date" type="date" name="installation_date"
                              id="installation_date"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              :disabled="checkPerm('installation_date')" />
                          </div>
                          <p v-show="$page.props.errors.installation_date" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.installation_date }}</p>
                        </div>
        
                        <div class="col-span-1 sm:col-span-1">
                          <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Actual Fiber
                            Distance
                          </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <input v-model="form.fiber_distance" type="number" name="fiber_distance" id="fiber_distance"
                              class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                              :disabled="checkPerm('fiber_distance')" />
                            <span
                              class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                              Meter </span>
                            <p v-show="$page.props.errors.fiber_distance" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.fiber_distance }}</p>
                          </div>
                        </div>
                        <div class="col-span-1 md:col-span-1">
                          <label for="bundle" class="block text-sm font-medium text-gray-700">
                            Devices
                          </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="bundle_equiptments.length !== 0">
                            <multiselect deselect-label="Selected already" :options="bundle_equiptments" track-by="id"
                              label="name" v-model="form.bundles" :allow-empty="false" :disabled="checkPerm('bundle')"
                              :multiple="true" :taggable="false"></multiselect>
                          </div>
                          <p v-show="$page.props.errors.bundles" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.bundles }}</p>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="onu_serial" class="block text-sm font-medium text-gray-700"> ONU Serial </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.onu_serial" type="text" name="onu_serial" id="onu_serial"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              :disabled="checkPerm('onu_serial')" />
                          </div>
                          <p v-show="$page.props.errors.onu_serial" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.onu_serial }}</p>
      
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="onu_power" class="block text-sm font-medium text-gray-700"> ONU Power </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" v-model="form.onu_power" name="onu_power" id="onu_power"
                              class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                              :disabled="checkPerm('onu_power')" />
                            <span
                              class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                              dBi </span>
                            <p v-show="$page.props.errors.onu_power" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.onu_power }}</p>
                          </div>
                        </div>
                     
                      
                       
                        <!--Image upload fields start-->
                        <div class="col-span-1 sm:col-span-1">
                          <label for="route_kmz_image" class="block text-sm font-medium text-gray-700"> Fiber Route KMZ</label>
                          <div class="mt-1 flex">
                            <label for="route_kmz_image" class="block  py-2 text-sm font-medium text-gray-500"> Upload Fiber Route KMZ Below:</label>
                          </div>
                          <div class="mt-2">
                            <ImageUploader
                              v-model="form.route_kmz_image"
                              v-model:imageUrl="routeImagePreview"
                              :existingImage="customer.route_kmz_image ? `/storage/${customer.route_kmz_image}` : null"
                              :id="'route_kmz_image'"
                              upload-text="Upload route image"
                              :error="$page.props.errors.route_kmz_image"
                            />  
                            </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="drum_no" class="block text-sm font-medium text-gray-700"> Fiber Drum No.</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-gear"></i>
                            </span>
                            <input v-model="form.drum_no_txt" type="text" name="drum_no_txt"
                              id="drum_no"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter Fiber Drum Number"
                              :disabled="checkPerm('drum_no_txt')" />
                          </div>
                          <p v-show="$page.props.errors.drum_no_txt" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.drum_no_txt }}</p>
                          
                          <div class="mt-2">
                            <ImageUploader
                              v-model="form.drum_no_image"
                              v-model:imageUrl="drumImagePreview"
                              :existingImage="customer.drum_no_image ? `/storage/${customer.drum_no_image}` : null"
                              upload-text="Upload drum image"
                              :disabled="checkPerm('drum_no_txt')"
                              :error="$page.props.errors.drum_no_image"
                            />
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="start_meter_txt" class="block text-sm font-medium text-gray-700">Start Meter</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-ruler"></i>
                            </span>
                            <input v-model="form.start_meter_txt" type="text" name="start_meter_txt"
                              id="start_meter_txt"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter Fiber Start Meter"
                              :disabled="checkPerm('start_meter_txt')" />
                          </div>
                          <p v-show="$page.props.errors.start_meter_txt" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.start_meter_txt }}</p>
                          
                          <div class="mt-2">
                            <ImageUploader
                              v-model="form.start_meter_image"
                              v-model:imageUrl="startMeterImagePreview"
                              :existingImage="customer.start_meter_image ? `/storage/${customer.start_meter_image}` : null"

                              upload-text="Upload start meter image"
                              :disabled="checkPerm('start_meter_txt')"
                              :error="$page.props.errors.start_meter_image"
                            />
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="end_meter_txt" class="block text-sm font-medium text-gray-700">End Meter</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-ruler-horizontal"></i>
                            </span>
                            <input v-model="form.end_meter_txt" type="text" name="end_meter_txt"
                              id="end_meter_txt"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter Fiber End Meter"
                              :disabled="checkPerm('end_meter_txt')" />
                          </div>
                          <p v-show="$page.props.errors.end_meter_txt" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.end_meter_txt }}</p>
                          
                          <div class="mt-2">
                            <ImageUploader
                              v-model="form.end_meter_image"
                              v-model:imageUrl="endMeterImagePreview"
                               :existingImage="customer.end_meter_image ? `/storage/${customer.end_meter_image}` : null"
                              upload-text="Upload end meter image"
                              :disabled="checkPerm('end_meter_txt')"
                              :error="$page.props.errors.end_meter_image"
                            />
                          </div>
                        </div>
                        <!--Image upload fields end-->
                       
                        <div class="col-span-1 sm:col-span-4">
                          <label for="installation_remark" class="block text-sm font-medium text-gray-700"> Installation
                            Remark </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-comment"></i>
                            </span>
                            <textarea name="installation_remark" v-model="form.installation_remark" id="installation_remark"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              :disabled="checkPerm('installation_remark')" />
                          </div>
                          <p v-show="$page.props.errors.installation_remark" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.installation_remark }}</p>
                        </div>
                      </div>
                    </div>
                    </div>
                   
    
                  </div>

                  <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <Link :href="route('customer.index')"
                      class="inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Back</Link>

                    <button @click="resetForm" type="button"
                      class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent  text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Reset</button>

                    <button type="submit"
                      class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
                  </div>
                </div> <!-- tab1 -->
                <div class="p-4" :class="[tab == 2 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Customer
                      Documents</h6>
                    <hr class="my-4 md:min-w-full" />
                    <keep-alive>
                      <customer-file :data="form.id" :permission="!checkPerm('order_date')" v-if="tab == 2" />
                    </keep-alive>

                  </div>
                </div>
                <div class="p-4" :class="[tab == 3 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Customer
                      History</h6>
                    <hr class="my-4 md:min-w-full" />
                    <keep-alive>
                      <customer-history :data="form.id" :permission="!checkPerm('order_date')" v-if="tab == 3" />
                    </keep-alive>

                  </div>
                </div>
               
                
                <div class="p-4" :class="[tab == 6 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <div class="w-full flex justify-between">
                      <h6 class="text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Text

                      </h6>
                      <span class="gap-2">
                        <a href="#" @click="copyText()">Copy <i
                            class="ml-2 fa text-xl fa-copy text-right text-indigo-500 hover:text-indigo-700 cursor-pointer"></i></a>
                        |
                        <a href="#" @click="shareText()">
                          Share <i
                            class="fa text-xl fa-share-nodes text-right text-indigo-500 hover:text-indigo-700 cursor-pointer"></i>
                        </a>
                      </span>


                    </div>

                    <hr class="my-4 md:min-w-full" />

                    <div id="text-code" ref="textContent">
                      Customer ID – {{ form.ftth_id }}<br />
                      Service Order Date - {{ form.order_date }}<br />
                      Customer Name – {{ form.name }}<br />
                      Contact Number – {{ form.phone_1 }} <br />
                      Township – {{ form.township['name'] }}<br />
                      Fully Address – {{ form.address }}<br />
                      Location – {{ form.latitude }},{{ form.longitude }} <br />
                      Applied Mbps – {{ form.package.name }} ({{ form.package.speed }}
                      Mbps)<br />
                      Installation Timeline – {{ form.installation_timeline }} Hrs<br />
                      Preferred installation date & time – {{ form.prefer_install_date }}<br />
                      <span v-if="form.order_remark">Order Remark : {{ form.order_remark }}</span>
                      <hr />
                      Devices - {{ bundle }}<br />
                      GPON Info - {{ gponInfo }} <br />
                      DN - {{ form.dn_id?.name }}<br />
                      SN - {{ form.sn_id?.name }}<br />
                      SN Port - {{ snPort }}<br />
                    </div>

                  </div>
                </div>

              </div> <!-- Tab Contents -->
            </div>
          </form>
        </div>
        <!-- end of mt-5 md: -->
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Multiselect from "@suadelabs/vue3-multiselect";
import {  ref, onMounted, provide,watch } from "vue";
import CustomerFile from "@/Components/CustomerFile";
import ImageUploader from "@/Components/ImageUploader";
import CustomerHistory from "@/Components/CustomerHistory";
import { router,useForm,Link } from "@inertiajs/vue3";
export default {
  name: "EditCustomer",
  components: {
    AppLayout,
    CustomerFile,
    Multiselect,
    CustomerHistory,
    Link,
    ImageUploader
  },
  props: {
    packages: Object,
    isps: Object,
    townships: Object,
    partners: Object,
    errors: Object,
    customer: Object,
    status_list: Object,
    subcoms: Object,
    user: Object,
    customer_history: Object,
    pops: Object,
    total_documents: Number,
    bundle_equiptments: Object,
    userPerm:Array,
    allStatus:Object,
  },
  setup(props) {
    provide('role', props.role);
    let res_dn = ref("");
    let res_sn = ref("");
    let pop = ref("");
    let pop_devices = ref("");
    let pppoe_auto = ref(false);
    let lat_long = '';
    const bundle = ref(null);
    const dnInfo = ref(null);
    const textContent = ref(null);
    const gponInfo = ref(null);
    const dnName = ref(null);
    const snName = ref(null);
    const snPort = ref(null);
    const filteredPartners = ref([]);
    const filteredPops = ref([]);
    const installationStatus = ref([
      { id: 'team_assigned', name: 'Team Assigned' },
      { id: 'cable_done', name: 'Cable Done' },
      { id: 'config_done', name: 'Config Done' },
      { id: 'completed', name: 'Completed' },
      { id: 'customer_cancel', name: 'Customer Cancel' },
      { id: 'pending_odb_issue', name: 'Pending ODB Issue' },
      { id: 'pending_port_full', name: 'Pending Port Full' },
      { id: 'pending_reappointment', name: 'Pending Reappointment' },
    ]);
    let statusList = ref(props.allStatus);
    let disableStatus = ref(false);
    const routeImagePreview = ref(props.customer.route_kmz_image || null);
    const drumImagePreview = ref(props.customer.drum_no_image || null);
    const startMeterImagePreview = ref(props.customer.start_meter_image || null);
    const endMeterImagePreview = ref(props.customer.end_meter_image || null);

    
    const snPortNoOptions = ref(
      Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }))
    );

    const gponOnuIdOptions = ref(
      Array.from({ length: 127 }, (v, i) => ({ id: i, name: `OnuID${i}` }))
    );
    if (props.customer.location) {
      lat_long = props.customer.location.split(",", 2);
    }

    let tab = ref(1);
    let selected_id = ref("");
    const popDevices = ref([]);
    const dnOptions = ref([]);
    const snOptions = ref([]);    
    function tabClick(val) {
      if (selected_id.value != null)
        tab.value = val;
    }

    const form = useForm({
      id: props.customer.id,
      name: props.customer.name,
      phone_1: props.customer.phone_1,
      address: props.customer.address,
      latitude: (lat_long[0]) ? lat_long[0] : '',
      longitude: (lat_long[1]) ? lat_long[1] : '',
      order_date: props.customer.order_date,
      order_remark: props.customer.order_remark,
      installation_date: props.customer.installation_date,
      service_activation_date: props.customer.service_activation_date,
      ftth_id: props.customer.ftth_id,
      township: props.customer.township,
      package: props.customer.package,
      status: "",
      subcom: "",
      prefer_install_date: props.customer.prefer_install_date,
      splitter_no: props.customer.splitter_no,
      installation_remark: props.customer.installation_remark,
      fc_used: props.customer.fc_used,
      fc_damaged: props.customer.fc_damaged,
      onu_serial: props.customer.onu_serial,
      onu_power: props.customer.onu_power,
      fiber_distance: props.customer.fiber_distance,
      vlan: props.customer.vlan,
      bundles: "",
      gpon_ontid: props.customer.gpon_ontid,
      port_balance: props.customer.port_balance,
      pop_id: props.customer.pop,
      pop_device_id: props.customer.pop_device,
      dn_id: props.customer.dn,
      sn_id: props.customer.sn,
      partner_id : props.customer.partner,
      isp_id: props.customer.isp,
      way_list_date: props.customer.way_list_date,
      installation_status: '',
      route_kmz_image: props.customer.route_kmz_image,
      drum_no_txt: props.customer.drum_no_txt,
      drum_no_image: props.customer.drum_no_image,
      start_meter_txt: props.customer.start_meter_txt,
      start_meter_image: props.customer.start_meter_image,
      end_meter_txt: props.customer.end_meter_txt,
      end_meter_image: props.customer.end_meter_image,
      installation_timeline: props.customer.installation_timeline,
    });

    function submit() {
      // Create FormData object for file uploads
      const formData = new FormData();
      
      // Append all form fields to FormData
      Object.keys(form).forEach(key => {
        // Skip the _method field as we'll add it separately
        if (key === '_method') return;
        
        // Handle file fields specially
        if (key.includes('image') && form[key] instanceof File) {
          formData.append(key, form[key]);
        } 
        // Handle objects (like township, package, etc.)
        else if (typeof form[key] === 'object' && form[key] !== null) {
          formData.append(key, JSON.stringify(form[key]));
        }
        // Handle all other fields
        else if (form[key] !== null && form[key] !== undefined) {
          formData.append(key, form[key]);
        }
      });

      // Add the PUT method
      formData.append('_method', 'PUT');

      // Send the request with FormData
      router.post("/customer/" + form.id, formData, {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          console.error("Error submitting form:", errors);
        },
        headers: {
          'Content-Type': 'multipart/form-data'
        },
      });
    }
    function checkPerm(data) {
      const my_role = props.user?.role;
        if(my_role){
          if (my_role?.read_customer) {
            return true;
          }
        }
      return !props.userPerm.includes(data);
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
    function isCustomerStatusIncluded(roleStatus, customerStatus) {
        if (!roleStatus || !customerStatus) {
            console.error("Invalid inputs: roleStatus or customerStatus is missing");
            return false;
        }

        try {
            // Ensure roleStatus is parsed JSON if it's a string
            if (typeof roleStatus === "string") {
                roleStatus = JSON.parse(roleStatus);
            }

            // Validate roleStatus is an array
            if (!Array.isArray(roleStatus)) {
                console.error("Error: roleStatus is not an array", roleStatus);
                return false;
            }

            // Check if customerStatus exists in roleStatus by ID
            return roleStatus.some(status => status.id == customerStatus.id);
        } catch (error) {
            console.error("Error parsing roleStatus:", error);
            return false;
        }
    }

    function getAbbreviation(name) {
      // Remove any text inside parentheses
      name = name.replace(/\(.*?\)/g, '').trim();

      // Split the name by spaces to get individual words
      const words = name.split(/\s+/);

      // Initialize an abbreviation string
      let abbreviation = '';

      // Loop through the words to construct the abbreviation
      for (let i = 0; i < words.length; i++) {
        // Only add the first letter of each word until abbreviation reaches 3 characters
        abbreviation += words[i][0].toUpperCase();
        if (abbreviation.length === 3) break;
      }

      // Get the current year
      const currentYear = new Date().getFullYear();

      // If the abbreviation is less than 3 characters, pad it (optional) and add the current year
      return abbreviation.padEnd(3, abbreviation[abbreviation.length - 1] || '') + '@' + currentYear + 'FIP';
    }
   
  
 
   
    // Fetch OLTs by POP
    const fetchPopDevices = async () => {
      if (!form.pop_id) {
        popDevices.value = [];
        return;
      }
      try {
        const response = await fetch(`/getOLTByPOP/${form.pop_id['id']}`);
        const data = await response.json();
        popDevices.value = data || [];
        console.log('fetch POP Devices');
      } catch (error) {
        console.error("Failed to fetch POP devices", error);
      }
    };

    // Fetch DNs by OLT
    const fetchDNs = async () => {
      if (!form.pop_device_id) {
        dnOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDNByOLT/${form.pop_device_id['id']}`);
        const data = await response.json();
        dnOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch DNs", error);
      }
    };

    // Fetch SNs by DN
    const fetchSNs = async () => {
      if (!form.dn_id) {
        snOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDnId/${form.dn_id['id']}`);
        const data = await response.json();
        snOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch SNs", error);
      }
    };
    watch(
      () => form.township,
      async (newTownship) => {
        if (newTownship) {
          try {
            // Reset POP and partner related fields
            form.pop_id = null;
            form.pop_device_id = null;
            form.dn_id = null;
            form.sn_id = null;
            form.partner = null;
            if (newTownship.partner_id) {
              form.partner = props.partners.find(p => p.id === newTownship.partner_id);
            }
            // Get POPs for the township
            const popResponse = await fetch(`/getPOPsByTownship/${newTownship.id}`);
            const popData = await popResponse.json();
            filteredPops.value = popData || [];
           
          } catch (error) {
            console.error("Failed to fetch data for township", error);
            filteredPops.value = [];
            
          }
        } else {
          filteredPops.value = [];
          form.partner = null;
        }
      }
    );
    watch(
      () => form.pop_id,
      ()=>{
        fetchPopDevices();
        form.sn_id = null;
        form.dn_id = null;
        form.pop_device_id = null;
        form.gpon_frame = null;
        form.gpon_slot = null;
        form.gpon_port = null;
        form.gpon_ontid = null;
        form.port_balance = null;
        
      }
    );
    watch(
      () => form.pop_device_id,
      () => {
        fetchDNs(); 
        form.dn_id = null;
        form.sn_id = null;
      }
    );
    watch(
      () => form.dn_id,
      () => {
        fetchSNs(); 
        dnInfo.value = `Frame${form.dn_id.gpon_frame}/Slot${form.dn_id.gpon_slot}/Port${form.dn_id.gpon_port}`;
        form.sn_id = null;
      }
    );
    function isEmptyObject(value) {
      // Check if it's an array
      if (Array.isArray(value)) {
        console.log('array');
        // If the array is empty, return true
        if (value.length === 0) {
          console.log('empty array');
          return true;
        }
        // Check if the array contains only empty objects
        return value.every(item => typeof item === 'object' && Object.keys(item).length === 0);
      }

      // Check if it's an object
      if (value && typeof value === 'object') {
        return Object.keys(value).length === 0;
      }

      // If it's neither an object nor an array, return false
      return false;
    }
    const copyText = () => {
      if (textContent.value) {
        // Get the text content from the <code> element
        let text = textContent.value.innerText;
        // Use the Clipboard API to copy the text content
        navigator.clipboard.writeText(text)
          .then(() => {
            // Optionally, you can show a success message or feedback to the user
            alert('Text copied to clipboard!');
          })
          .catch(err => {
            // Handle any errors that occur during the copy
            console.error('Failed to copy text: ', err);
          });
      } else {
        console.error('Text content ref is undefined.');
      }
    };
    const shareText = () => {
      if (textContent.value) {
        // Get the text content from the <code> element
        let text = textContent.value.innerText;
        // Use the Clipboard API to copy the text content
        if (navigator.share) {
          navigator.share({
            title: 'Customer Information',
            text: text
          }).then(() => {
            // Optionally, you can show a success message or feedback to the user
            alert('Text copied to clipboard!');
          })
            .catch(err => {
              // Handle any errors that occur during the copy
              console.error('Failed to copy text: ', err);
            });
        }


      } else {
        console.error('Text content ref is undefined.');
      }
    };
    onMounted(() => {

// Initialize form data
      form.installation_status = props.customer.installation_status?
                installationStatus.value.filter((status) => status.id == props.customer.installation_status)[0] :
                null ;
        if (form.township) {
          fetch(`/getPOPsByTownship/${form.township.id}`)
            .then(response => response.json())
            .then(data => {
              filteredPops.value = data || [];
            })
            .catch(error => {
              console.error("Failed to fetch pop for township", error);
              filteredPops.value = [];
            });
        }
        if(form.pop_id){
          fetchPopDevices();
        }
        if (form.pop_device_id){
        fetchDNs();
      }
       if (form.dn_id) {
        fetchSNs();
        dnInfo.value = `Frame${form.dn_id.gpon_frame}/Slot${form.dn_id.gpon_slot}/Port${form.dn_id.gpon_port}`;
      }
      if (props.customer.pop_device_id && dnInfo.value && props.customer.sn_id) {
        gponInfo.value = `${dnInfo.value}`;
      }
      if (gponInfo.value && props.customer.gpon_ontid) {
        gponInfo.value += '/' + gponOnuIdOptions.value.filter((d) => d.name == props.customer.gpon_ontid)[0].name;
      }
   
      if (props.customer.bundle) {
        let bundleArray = props.customer.bundle.split(",").filter(Boolean); // Filter out empty strings
        let bundleLists = [];
        bundle.value = []; // Initialize as array
        
        bundleArray.forEach(e => {
          const foundEquip = props.bundle_equiptments.find(d => d.id == e);
          if (foundEquip) {
            bundleLists.push(foundEquip);
            bundle.value = bundle.value ? 
              `${bundle.value},${foundEquip.name}` : 
              foundEquip.name;
          }
        });

        form.bundles = bundleLists;
      }
      if (props.customer.splitter_no) {
        form.splitter_no = snPortNoOptions.value.filter((d) => d.id == props.customer.splitter_no)[0];
        snPort.value = form.splitter_no.name;
      }
      if (props.customer.gpon_ontid) {
        form.gpon_ontid = gponOnuIdOptions.value.filter((d) => d.name == props.customer.gpon_ontid)[0];
      }
     
      form.status = props.allStatus.filter((d) => d.id == props.customer.status_id)[0];
      form.subcom = props.subcoms.filter((d) => d.id == props.customer.subcom_id)[0];


      let customerStatus = props.allStatus.filter((d) => d.id == props.customer.status_id)[0];
      form.status = customerStatus;
      let roleStatus = props.status_list;
     
      if (!isCustomerStatusIncluded(roleStatus, customerStatus)) {
        statusList.value = props.allStatus;
        disableStatus.value = true;
      }else{
        statusList.value = roleStatus;
      }
      //DN SN
      // form.pop_id = props.pops.filter((d) => d.id == props.customer.pop_id)[0];
      // console.log('POP ID ', form.pop_id);
      // if (form.pop_id) {
      //   fetchPopDevices();
      // }
     
     
        // Initialize GPON info
      //   if (form.dn_id) {
      //     fetchSNs();
      //   dnInfo.value = `Frame${form.dn_id.gpon_frame}/Slot${form.dn_id.gpon_slot}/Port${form.dn_id.gpon_port}`;
      //   gponInfo.value = dnInfo.value;
        
      //   if (props.customer.gpon_ontid) {
      //     const ontId = gponOnuIdOptions.value.find(d => d.name === props.customer.gpon_ontid);
      //     if (ontId) {
      //       gponInfo.value += '/' + ontId.name;
      //     }
      //   }
      // }
    

    });

    return {
      form, submit, isNumber, checkPerm, tab, tabClick, isEmptyObject, pop_devices, snPortNoOptions, gponOnuIdOptions, dnInfo, bundle, shareText, copyText, textContent,
      gponInfo,
      dnName,
      snName,
      snPort,
      popDevices,
      dnOptions,
      snOptions,
      filteredPops,
      disableStatus,
      statusList,
      routeImagePreview,
      drumImagePreview,
      startMeterImagePreview,
      endMeterImagePreview,
      installationStatus
    };
  },
};
</script>
