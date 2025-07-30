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
                      @click="tabClick(1)" preserve-state>General</a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 7 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"
                    v-if="user.role?.installation_supervisor || user.role?.installation_oss"><a href="#" @click="tabClick(7)"
                      preserve-state>Installation</a></li>
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
                        <label for="city" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          City </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="cities.length !== 0">
                          <multiselect deselect-label="Selected already" :options="cities" track-by="id" label="name"
                            v-model="form.city" placeholder="Select City" :allow-empty="false"
                            @update:modelValue="form.city_id = $event?.id" required :disabled="checkPerm('city_id')">

                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.city_id" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.city_id }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-2">
                        <label for="township" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Township </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="filteredTownships.length !== 0">
                          <multiselect deselect-label="Selected already" :options="filteredTownships" track-by="id"
                            label="name" v-model="form.township" placeholder="Select Township" :allow-empty="false"
                            @update:modelValue="form.township_id = $event?.id" required
                            :disabled="checkPerm('city_id')">
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
                            v-on:keypress="isNumber(event)" :disabled="checkPerm('city_id')" />
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
                            v-on:keypress="isNumber(event)" :disabled="checkPerm('city_id')" />
                        </div>
                        <p v-show="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.longitude }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-4">
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
                            required :disabled="checkPerm('city_id')" />
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
                      <div class="col-span-1 sm:col-span-1" v-if="$page.props.login_type == 'isp'">
                        <label for="isp" class="block text-sm font-medium text-gray-700"> Unique ID </label>
                        <span
                          class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-id-badge"></i>
                        </span>
                        <label id="ftth_id"
                          class="pl-10 mt-2  flex-1 block w-full rounded-md sm:text-sm border-gray-300">{{
                            form.ftth_id }}</label>

                      </div>
                      <div class="col-span-1 sm:col-span-1 w-full">
                        <label for="isp_ftth_id" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Customer ID </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-id-badge"></i>
                          </span>
                          <input v-model="form.isp_ftth_id" type="text" name="isp_ftth_id" id="isp_ftth_id"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            required :disabled="checkPerm('isp_ftth_id')" />
                        </div>
                        <p v-show="$page.props.errors.isp_ftth_id" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.isp_ftth_id }}</p>
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
                          <multiselect deselect-label="Selected already" :options="statusList" track-by="id"
                            label="name" v-model="form.status" :allow-empty="false"
                            :disabled="!disableStatus ? checkPerm('status_id') : disableStatus"></multiselect>
                        </div>
                        <p v-show="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.status
                        }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="bandwidth" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Bandwith </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="number" v-model="form.bandwidth" name="bandwidth" id="bandwidth"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                            placeholder="20, 30 e.g. " v-on:keypress="isNumber($event)"
                            :disabled="checkPerm('bandwidth')" required />
                          <span
                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            Mbps </span>
                        </div>
                        <p v-show="$page.props.errors.bandwidth" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.bandwidth }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="service_type" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Service Type </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <select v-model="form.service_type" name="service_type" id="service_type"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('service_type')">
                            <option value="FTTH">FTTH (Default)</option>
                            <option value="DIA">DIA</option>
                            <option value="IPLC">IPLC</option>
                            <option value="DPLC">DPLC</option>
                          </select>
                        </div>
                        <p v-show="$page.props.errors.service_type" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.service_type }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="installation_service_id" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Installation </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="filteredInstallationServices?.length > 0">
                          <multiselect deselect-label="Selected already" :options="filteredInstallationServices"
                            track-by="id" label="name" v-model="form.installation_service" :allow-empty="false"
                            @update:modelValue="form.installation_service_id = $event?.id"
                            :disabled="checkPerm('installation_service_id')">
                          </multiselect>
                        </div>
                        <div class="mt-1 border-gray-200 border p-2 rounded-md text-gray-600" v-else>No Installation
                          Service
                        </div>
                        <p v-show="$page.props.errors.installation_service_id" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.installation_service_id }}</p>
                      </div>
                      <div class="col-span-1 md:col-span-2">
                        <label for="bundle" class="block text-sm font-medium text-gray-700">
                          Additional Materials Request (Leave blank for own materials)
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="bundle_equiptments.length !== 0">
                          <multiselect deselect-label="Selected already" :options="bundle_equiptments" track-by="id"
                            label="name" v-model="form.bundles" :allow-empty="true" :disabled="checkPerm('bundle')"
                            :multiple="true" :taggable="false"></multiselect>
                        </div>
                        <p v-show="$page.props.errors.bundles" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.bundles }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-2">
                        <label for="package" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Maintenance </label>
                        <div class="mt-1 flex rounded-md shadow-sm " v-if="filteredMaintenanceServices?.length > 0">
                          <multiselect deselect-label="Selected already" :options="filteredMaintenanceServices"
                            track-by="id" label="name" v-model="form.maintenance_service" :allow-empty="false"
                            @update:modelValue="form.maintenance_service_id = $event?.id"
                            :disabled="checkPerm('maintenance_service_id')" :class="text - xs">
                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.maintenance_service_id" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.maintenance_service_id }}</p>
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



                      <div class="col-span-1 sm:col-span-1" v-if="$page.props.login_type == 'isp'">
                        <label for="isp" class="block text-sm font-medium text-gray-700"> Installation Date </label>
                        <span
                          class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-calendar"></i>
                        </span>
                        <label id="installation_date"
                          class="pl-10 mt-2  flex-1 block w-full rounded-md sm:text-sm border-gray-300"> {{
                            form.installation_date ?? "NA" }}</label>

                      </div>

                      <div class="col-span-1 sm:col-span-1" v-if="$page.props.login_type == 'isp'">
                        <label for="service_activation_date" class="block text-sm font-medium text-gray-700"> Service
                          Activation Date</label>
                        <span
                          class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-calendar"></i>
                        </span>
                        <label id="service_activation_date"
                          class="pl-10 mt-2  flex-1 block w-full rounded-md sm:text-sm border-gray-300">{{
                            form.service_activation_date ?? "NA" }}</label>

                      </div>


                    </div>
                    <div v-if="$page.props.login_type == 'internal'">
                      <hr class="my-4 md:min-w-full" />
                      <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                        ODN Information</h6>

                      <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                        <div class="col-span-1 sm:col-span-1">
                          <label for="isp" class="block text-sm font-medium text-gray-700"> Customer ID </label>
                          <span
                            class="z-10 mt-1 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-id-badge"></i>
                          </span>
                          <input v-model="form.ftth_id" type="text" name="ftth_id" id="ftth_id"
                            class="mt-1 pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            required :disabled="checkPerm('ftth_id')" />
                          <p v-show="$page.props.errors.ftth_id" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.ftth_id
                          }}</p>
                        </div>
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
                          <label for="pop_site" class="block text-sm font-medium text-gray-700">Choose POP Site </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="filteredPops.length !== 0">
                            <multiselect deselect-label="Selected already" :options="filteredPops" track-by="id"
                              label="site_name" v-model="form.pop_id" :allow-empty="false"
                              :disabled="checkPerm('partner_id')">
                            </multiselect>
                          </div>
                          <div v-else class="text-sm text-gray-500 mt-1">
                            Please select a township first to view available POPs
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="partner" class="block text-sm font-medium text-gray-700"> Partner </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="filteredPartners?.length !== 0">
                            <multiselect deselect-label="Selected already" :options="filteredPartners" track-by="id"
                              label="name" v-model="form.partner" :allow-empty="true"
                              @update:modelValue="form.partner_id = $event?.id" :disabled="checkPerm('partner_id')">
                            </multiselect>
                          </div>
                          <p v-show="$page.props.errors.partner_id" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.partner_id
                          }}</p>
                        </div>

                        <div class="col-span-1 sm:col-span-1">
                          <label for="pop_device_id" class="block text-sm font-medium text-gray-700"> Choose OLT</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <multiselect deselect-label="Selected already" :options="popDevices" track-by="id"
                              label="device_name" v-model="form.pop_device_id" :allow-empty="false"
                              :disabled="checkPerm('partner_id')">
                            </multiselect>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose DN
                          </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="dnOptions">
                            <multiselect deselect-label="Selected already" :options="dnOptions" track-by="id"
                              label="name" v-model="form.dn_id" :allow-empty="false">
                            </multiselect>
                          </div>
                        </div>

                        <div class="col-span-1 sm:col-span-1">
                          <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose SN
                          </label>
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="snOptions">
                            <multiselect deselect-label="Selected already" :options="snOptions" track-by="id"
                              label="name" v-model="form.sn_id" :allow-empty="true">
                            </multiselect>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="splitter_no" class="block text-sm font-medium text-gray-700"> SN Port No. </label>
                          <div class="mt-1 flex rounded-md shadow-sm w-full" v-if="snPortNoOptions.length !== 0">
                            <multiselect deselect-label="Selected already" :options="snPortNoOptions" track-by="id"
                              label="name" v-model="form.splitter_no" :allow-empty="true">
                              <template v-slot:singleLabel="{ option }"><strong>{{ option.name }}
                                  {{ option.language }}</strong></template>
                            </multiselect>

                          </div>
                          <p v-show="$page.props.errors.splitter_no" class="mt-2 text-sm text-red-500 w-full">{{
                            $page.props.errors.splitter_no }}</p>

                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="pppoe_username" class="block text-sm font-medium text-gray-700"> PPPOE
                            Account</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.pppoe_username" type="text" name="pppoe_username" id="pppoe_username"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter PPPOE Account" :disabled="checkPerm('pppoe_username')" />
                            <p v-show="$page.props.errors.pppoe_username" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.pppoe_username }}</p>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="pppoe_password" class="block text-sm font-medium text-gray-700"> PPPOE
                            Password</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.pppoe_password" type="text" name="pppoe_password" id="pppoe_password"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter PPPOE Password" :disabled="checkPerm('pppoe_password')" />
                            <p v-show="$page.props.errors.pppoe_password" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.pppoe_password }}</p>
                          </div>
                        </div>
                        <!-- <div class="col-span-1 sm:col-span-1">
                          <label for="port_balance" class="block text-sm font-medium text-gray-700"> Port
                            Balance</label>
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
                              label="name" v-model="form.gpon_ontid" :allow-empty="true"
                              :disabled="checkPerm('gpon_ontid')">
                            </multiselect>
                            <p v-show="$page.props.errors.gpon_ontid" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.gpon_ontid }}</p>
                          </div>
                        </div> -->
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
                        <div class="col-span-1 sm:col-span-1">
                          <label for="supervisor_id" class="block text-sm font-medium text-gray-700"> Assign Supervisor
                          </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <multiselect deselect-label="Selected already" :options="supervisors" track-by="id"
                              label="name" v-model="form.supervisor" :allow-empty="false"
                              @update:modelValue="form.supervisor_id = $event?.id"
                              :disabled="checkPerm('supervisor_id')" :class="text - xs">
                            </multiselect>
                          </div>
                          <p v-show="$page.props.errors.supervisor_id" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.supervisor_id }}</p>
                        </div>
                        <!-- <div class="text-sm text-gray-600 mt-2  col-span-4" v-if="dnInfo">
                          GPON INFO : {{ dnInfo }}

                        </div> -->
                      </div>
                      <div v-if="form.sn_id">


                        <hr class="my-4 md:min-w-full" />
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                          Team Assign Information</h6>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                          <div class="col-span-1 sm:col-span-1">
                            <label for="subcom" class="block text-sm font-medium text-gray-700"> Assign Installation
                              Team
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm" v-if="subcoms.length !== 0">
                              <multiselect deselect-label="Selected already" :options="subcoms" track-by="id"
                                label="name" v-model="form.subcom" :allow-empty="true"
                                :disabled="checkPerm('subcom_id')"></multiselect>
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
                                label="name" v-model="form.installation_status" :allow-empty="false"
                                :disabled="checkPerm('installation_status')" :multiple="false" :taggable="false">
                              </multiselect>
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
                              <input v-model="form.way_list_date" type="date" name="way_list_date" id="way_list_date"
                                class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                :disabled="checkPerm('way_list_date')" />
                            </div>
                            <p v-show="$page.props.errors.way_list_date" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.way_list_date }}</p>
                          </div>
                        </div>
                        <hr class="my-4 md:min-w-full" />
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                          Installation Information</h6>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">

                          <div class="col-span-1 sm:col-span-1">
                            <label for="installation_date" class="block text-sm font-medium text-gray-700"> Actual
                              Installed
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
                              <input v-model="form.fiber_distance" type="number" name="fiber_distance"
                                id="fiber_distance"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                :disabled="checkPerm('fiber_distance')" />
                              <span
                                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                Meter </span>
                              <p v-show="$page.props.errors.fiber_distance" class="mt-2 text-sm text-red-500">{{
                                $page.props.errors.fiber_distance }}</p>
                            </div>
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
                          <div class="col-span-1 sm:col-span-1">
                            <label for="actual_latitude" class="block text-sm font-medium text-gray-700">Actual Latitude
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <span
                                class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                                <i class="fas fa-location-arrow"></i>
                              </span>
                              <input type="text" v-model="form.actual_latitude" name="actual_latitude"
                                id="actual_latitude"
                                class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                v-on:keypress="isNumber(event)" :disabled="checkPerm('city_id')" />
                            </div>
                            <p v-show="$page.props.errors.actual_latitude" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.actual_latitude }}</p>
                          </div>
                          <div class="col-span-1 sm:col-span-1">
                            <label for="actual_longitude" class="block text-sm font-medium text-gray-700">Actual
                              Longitude
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <span
                                class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                                <i class="fas fa-location-arrow"></i>
                              </span>
                              <input type="text" v-model="form.actual_longitude" name="actual_longitude"
                                id="actual_longitude"
                                class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                v-on:keypress="isNumber(event)" :disabled="checkPerm('city_id')" />
                            </div>
                            <p v-show="$page.props.errors.actual_longitude" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.actual_longitude }}</p>
                          </div>




                          <div class="col-span-1 sm:col-span-2">
                            <label for="installation_remark" class="block text-sm font-medium text-gray-700">
                              Installation
                              Remark </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <span
                                class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                                <i class="fas fa-comment"></i>
                              </span>
                              <textarea name="installation_remark" v-model="form.installation_remark"
                                id="installation_remark"
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
                      class="inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">
                    Back</Link>

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
                      Township – {{ form.township?.name }}<br />
                      Fully Address – {{ form.address }}<br />
                      Location – {{ form.latitude }},{{ form.longitude }} <br />
                      Applied Mbps – {{ form.bandwidth }} Mbps<br />
                      Installation Timeline – {{ customer.installation_service?.name }} <br />
                      Maintenance Timeline – {{ customer.maintenance_service?.name }} <br />
                      Preferred installation date & time – {{ form.prefer_install_date }}<br />
                      <span v-if="form.order_remark">Order Remark : {{ form.order_remark }}</span>
                      <hr />
                      Actual Installation Date - {{ form.installation_date }}<br />
                      Devices - {{ bundle }}<br />
                      DN - {{ form.dn_id?.name }}<br />
                      SN - {{ form.sn_id?.name }}<br />
                      SN Port - {{ snPort }}<br />
                      Fiber Distance - {{ form.fiber_distance }} Meter<br />
                      ONU Serial - {{ form.onu_serial }}<br />  
                      ONU Power - {{ form.onu_power }} <br />
                      Installation Remark - {{ form.installation_remark }}<br />
                      Installation Location – {{ form.actual_latitude }},{{ form.actual_longitude }} <br />
                    </div>

                  </div>
                </div>
                <div class="p-4" :class="[tab == 7 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Installation Information
                    </h6>
                    <hr class="my-4 md:min-w-full" />
                    <div class="overflow-x-auto rounded-lg shadow mb-6 mt-4">
                      <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-indigo-50">
                          <tr>
                            <th
                              class="px-4 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">
                              Group Name</th>
                            <th
                              class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">
                              Total</th>
                            <th
                              class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">
                              Skip</th>
                            <th
                              class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">
                              Requested</th>
                            <th
                              class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">
                              Rejected</th>
                            <th
                              class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">
                              Approved</th>
                            <th
                              class="px-4 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">
                              Remaining</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="group in checkListSummary" :key="group.group_name"
                            @click="selectedGroupId = group.id"
                            :class="['cursor-pointer transition-colors', selectedGroupId === group.id ? 'bg-indigo-100' : 'hover:bg-gray-50']">
                            <td class="px-4 py-2 text-blue-600 underline font-medium">{{ group.group_name }}
                            </td>
                            <td class="px-4 py-2 text-center">
                              <span v-if="group.total > 0"
                                class="px-2 py-1 rounded-xl bg-yellow-100 text-yellow-700 text-xs font-semibold">{{
                                  group.total }}</span>
                              <span v-else class="text-gray-400">{{ group.total }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                              <span v-if="group.skip > 0"
                                class="px-2 py-1 rounded-xl bg-indigo-100 text-indigo-700 text-xs font-semibold">{{
                                  group.skip }}</span>
                              <span v-else class="text-gray-400">{{ group.skip }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                              <span v-if="group.requested > 0"
                                class="px-2 py-1 rounded-xl bg-indigo-100 text-indigo-700 text-xs font-semibold">{{
                                  group.requested }}</span>
                              <span v-else class="text-gray-400">{{ group.requested }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                              <span v-if="group.rejected > 0"
                                class="px-2 py-1 rounded-xl bg-red-100 text-red-700 text-xs font-semibold">{{
                                  group.rejected }}</span>
                              <span v-else class="text-gray-400">{{ group.rejected }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                              <span v-if="group.approved > 0"
                                class="px-2 py-1 rounded-xl bg-green-100 text-green-700 text-xs font-semibold">{{
                                  group.approved }}</span>
                              <span v-else class="text-gray-400">{{ group.approved }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                              <span v-if="group.remaining > 0"
                                class="px-2 py-1 rounded-xl bg-gray-100 text-gray-700 text-xs font-semibold">{{
                                  group.remaining }}</span>
                              <span v-else class="text-gray-400">{{ group.remaining }}</span>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                    </div>
                    <div ref="showModal" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400"
                      v-if="filteredChecklists">
                      <div
                        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity">
                          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                        <div
                          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full"
                          role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                              <div v-if="selectedGroupId" class="mt-2 text-sm text-indigo-600 font-semibold">
                                Showing details for: <span class="underline">{{checkListSummary.find(g => g.id
                                  === selectedGroupId)?.group_name}}</span>
                              </div>
                              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div v-for="(checklist, index) in filteredChecklists" :key="checklist.id"
                                  class="mt-6 sm:mt-0">
                                  <label class="block text-sm font-medium text-gray-700">
                                    {{ checklist.name }}
                                  </label>
                                  <input type="text" v-model="form2[`checklist_${checklist.id}_title`]"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-if="form2[`checklist_${checklist.id}_title`]" disabled />
                                  <span v-else>No Data</span>
                                  <div class="mt-2"
                                    v-if="checklist.has_attachment && customer.checklist_images[checklist.id]">
                                    <div>
                                      <!-- Thumbnail image -->
                                      <img class="h-40 w-40 object-cover cursor-pointer"
                                        :src="customer.checklist_images && customer.checklist_images[checklist.id] ? `/storage/${customer.checklist_images[checklist.id]}` : null"
                                        @click="openFullView(checklist.id)" alt="">

                                      <!-- Full view modal -->
                                      <div v-if="showFullView && selectedImageId === checklist.id"
                                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                        @click="closeFullView">
                                        <div class="relative">
                                          <img class="max-h-[90vh] max-w-[90vw]"
                                            :src="customer.checklist_images && customer.checklist_images[checklist.id] ? `/storage/${customer.checklist_images[checklist.id]}` : null"
                                            alt="">
                                          <button
                                            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-8 h-8 flex items-center justify-center"
                                            @click="closeFullView">
                                            <span class="text-xl">&times;</span>
                                          </button>
                                        </div>
                                      </div>
                                      <!--full view modal-->
                                    </div>
                                  </div>
                                  <div class="mt-2" v-if="form2[`checklist_${checklist.id}_title`]">
                                    <div class="py-2">
                                      <label for="status" class="block text-md font-medium text-gray-700"> Status
                                      </label>
                                      <div class="mt-2 flex">
                                         <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-yellow-600"
                                            :name="form2[`checklist_${checklist.id}`]"
                                            v-model="form2[`checklist_${checklist.id}_status`]"
                                            value="skip" /><span
                                            class="ml-2 text-gray-700 text-sm">Skip</span></label>
                                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-yellow-600"
                                            :name="form2[`checklist_${checklist.id}`]"
                                            v-model="form2[`checklist_${checklist.id}_status`]"
                                            value="requested" /><span
                                            class="ml-2 text-gray-700 text-sm">Request</span></label>
                                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-red-600"
                                            :name="form2[`checklist_${checklist.id}`]"
                                            v-model="form2[`checklist_${checklist.id}_status`]" value="declined" /><span
                                            class="ml-2 text-gray-700 text-sm">Declined</span></label>
                                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-green-600"
                                            :name="form2[`checklist_${checklist.id}`]"
                                            v-model="form2[`checklist_${checklist.id}_status`]" value="approved" /><span
                                            class="ml-2 text-gray-700 text-sm">Approved</span></label>
                                      </div>
                                    </div>

                                  </div>

                                  <p v-show="$page.props.errors[`checklist_${checklist.id}_attachment`]"
                                    class="mt-2 text-sm text-red-500">
                                    {{ $page.props.errors[`checklist_${checklist.id}_attachment`] }}
                                  </p>
                                </div>

                              </div>
                            </div>
                          </div>
                          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button type="button" @click="installationApproval"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                                v-show="!form.id">
                                Save
                              </button>
                              <button type="button" @click="installationApproval"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                                v-show="form.id">
                                Update
                              </button>
                            </span>
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                              <button @click="closeModel" type="button"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                Cancel
                              </button>
                            </span>
                          </div>

                        </div>
                      </div>
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
import { ref, onMounted, provide, watch, computed } from "vue";
import CustomerFile from "@/Components/CustomerFile";
import ImageUploader from "@/Components/ImageUploader";
import CustomerHistory from "@/Components/CustomerHistory";
import { router, useForm, Link } from "@inertiajs/vue3";

export default {
  name: "EditCustomer",
  components: {
    AppLayout,
    CustomerFile,
    Multiselect,
    CustomerHistory,
    Link,
    ImageUploader,

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
    userPerm: Array,
    allStatus: Object,
    subconCheckList: Object,
    popDevices: Object,
    snPort: Object,
    installationServices: Object,
    portSharingServices: Object,
    maintenanceServices: Object,
    supervisors: Object,
    cities: Object,
    checkListSummary: Object,
  },
  setup(props) {
    provide('role', props.role);
    let res_dn = ref("");
    let res_sn = ref("");
    let pop = ref("");
    let pop_devices = ref("");
    let pppoe_auto = ref(false);
    let lat_long = '';
    let actual_lat_long = '';
    const bundle = ref(null);
    // const dnInfo = ref(null);
    const textContent = ref(null);
    // const gponInfo = ref(null);
    const dnName = ref(null);
    const snName = ref(null);
    const snPort = ref(null);
    const filteredPartners = ref([]);
    filteredPartners.value = props.customer.partner_id ? props.partners.filter(partner => partner.id === props.customer.partner_id) : [];
    const filteredPops = ref([]);
    const filteredTownships = ref([]);
    const checklistImagePreviews = ref({});
    const installationStatus = ref([
         { id: 'team_assigned', name: 'Team Assigned'},
            { id: 'installation_start', name: 'Installation Start' },
            { id: 'installation_complete', name: 'Installation Complete' },
            { id: 'photo_upload_complete', name: 'Photo Upload Complete' },
            { id: 'photo_upload_rejected', name: 'Photo Upload Rejected' },
            { id: 'supervisor_approved', name: 'Supervisor Approved' },
            { id: 'customer_cancel', name: 'Customer Cancel' },
            { id: 'port_full', name: 'Port Full' },
    ]);
    let statusList = ref(props.allStatus);
    let disableStatus = ref(false);
    const routeImagePreview = ref(props.customer.route_kmz_image || null);
    const drumImagePreview = ref(props.customer.drum_no_image || null);
    const startMeterImagePreview = ref(props.customer.start_meter_image || null);
    const endMeterImagePreview = ref(props.customer.end_meter_image || null);
    const isOptionDisabled = (option) => option.disabled === true

    const snPortNoOptions = ref(
      Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }))
    );

    const gponOnuIdOptions = ref(
      Array.from({ length: 127 }, (v, i) => ({ id: i, name: `OnuID${i}` }))
    );
    if (props.customer.current_address?.location) {
      lat_long = props.customer.current_address.location.split(",", 2);
    }
    if (props.customer.current_address?.actual_location) {
      actual_lat_long = props.customer.current_address.actual_location.split(",", 2);
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
      address: props.customer.current_address?.address,
      latitude: (lat_long[0]) ? lat_long[0] : '',
      longitude: (lat_long[1]) ? lat_long[1] : '',
      actual_latitude: (actual_lat_long[0]) ? actual_lat_long[0] : '',
      actual_longitude: (actual_lat_long[1]) ? actual_lat_long[1] : '',
      order_date: props.customer.order_date,
      order_remark: props.customer.order_remark,
      installation_date: props.customer.installation_date,
      service_activation_date: props.customer.service_activation_date,
      isp_ftth_id: props.customer.isp_ftth_id,
      ftth_id: props.customer.ftth_id,
      township: props.customer.current_address?.township,
      township_id: props.customer.current_address?.township_id,
      installation_service_id: props.customer.installation_service_id,
      maintenance_service_id: props.customer.maintenance_service_id,
      installation_service: props.customer.installation_service,
      maintenance_service: props.customer.maintenance_service,
      bandwidth: props.customer.bandwidth,
      status: "",
      subcom: "",
      prefer_install_date: props.customer.prefer_install_date,
      splitter_no: props.snPort?.port_number,
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
      pop_id: props.snPort?.pop,
      pop_device_id: props.snPort?.pop_device,
      dn_id: props.snPort?.dn_splitter,
      sn_id: props.snPort?.sn_splitter,
      partner_id: props.customer.partner_id,
      partner: props.customer?.partner,
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
      service_type: props.customer.service_type,
      supervisor_id: props.customer.supervisor_id,
      supervisor: props.customer.supervisor,
      bundles: "",
      city: props.customer.city ?? null,
      city_id: props.customer.city_id ?? null,
      pppoe_username: props.customer.pppoe_username || '',
      pppoe_password: props.customer.pppoe_password || '',
    });
    const form2 = useForm({
      ...Object.fromEntries(
        props.subconCheckList ?
          Object.values(props.subconCheckList).flatMap(checklist => [
            [`checklist_${checklist.id}_title`, props.customer.checklist_values?.[checklist.id]?.title || ''],
            [`checklist_${checklist.id}_attachment`, null],
            [`checklist_${checklist.id}_status`, props.customer.checklist_values?.[checklist.id]?.status || '']
          ]) : []
      ),
    });
    const showFullView = ref(false);
    const selectedImageId = ref(null);

    function openFullView(checklistId) {
      selectedImageId.value = checklistId;
      showFullView.value = true;
    }
    function closeFullView() {
      showFullView.value = false;
      selectedImageId.value = null;
    }
      const selectedGroupId = ref(null);
    const closeModel = () => {
      console.log("close model");
      selectedGroupId.value = null;
    }
    function installationApproval() {
      console.log("installationApproval");
      router.post("/installationApproval/" + form.id, form2, {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeModel();
        },
        onError: (errors) => {
          console.error("Error submitting form:", errors);
        },
        headers: {
          'Content-Type': 'multipart/form-data'
        },
      });
    }
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
      if (my_role) {
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
    // Add this function to fetch available ports by splitter ID
    const fetchAvailablePorts = async () => {
      if (!form.sn_id) {
        // Reset to default ports if no SN is selected
        snPortNoOptions.value = Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }));
        return;
      }
      try {
        const response = await fetch(`/getAvailablePortBySplitterId/${form.sn_id?.id}`);
        const data = await response.json();
        // Update snPortNoOptions with the returned data
        snPortNoOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch available ports", error);
        // Fallback to default ports on error
        snPortNoOptions.value = Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }));
      }
    };
    const getTownshipByCityId = async (cityId) => {
      if (!cityId) {
        filteredTownships.value = [];
        form.township = '';
        return;
      }
      try {
        const response = await fetch(`/getTownshipByCityId/${cityId}`);
        const data = await response.json();
        filteredTownships.value = data || [];
        console.log('fetch Township Data');
      } catch (error) {
        console.error("Failed to fetch Township Data", error);
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
      () => {
        fetchPopDevices();
        form.sn_id = null;
        form.dn_id = null;
        form.pop_device_id = null;
        form.splitter_no = null;
        // form.gpon_frame = null;
        // form.gpon_slot = null;
        // form.gpon_port = null;
        form.gpon_ontid = null;
        form.port_balance = null;
        // Filter partners by pop_device_id
        if (form.pop_id && form.pop_id.partner_id) {
          console.log('Filtering partners by pop', form.pop_id.partner_id);
          filteredPartners.value = props.partners.filter(p => p.id === form.pop_id.partner_id);
          form.partner_id = form.pop_id.partner_id;
        } else {
          console.log('Resetting partners to all partners');
          filteredPartners.value = props.partners;
          form.partner_id = null;
        }
      }
    );
    watch(
      () => form.pop_device_id,
      () => {
        fetchDNs();
        form.dn_id = null;
        form.sn_id = null;
        form.splitter_no = null;
      }
    );
    watch(
      () => form.dn_id,
      () => {
        fetchSNs();
        form.sn_id = null;
        form.splitter_no = null;
      }
    );

    // Add a watcher for form.sn_id
    watch(
      () => form.sn_id,
      () => {
        fetchAvailablePorts();
      }
    );
    watch(
      () => form.city_id,
      (newCity) => {
        getTownshipByCityId(newCity);
        form.township = '';
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
    const filteredInstallationServices = computed(() => {
      if (!props.installationServices || !form.service_type) return [];

      if (props.customer.installation_service?.service_type !== (form.service_type || '').toLowerCase()) {
        form.installation_service_id = null; // Reset installation service ID when service type changes
        form.installation_service = null; // Reset installation service when service type changes
      }

      return props.installationServices.filter(s =>
        (s.service_type || '').toLowerCase() === (form.service_type || '').toLowerCase()
      );
    });
    const filteredMaintenanceServices = computed(() => {
      if (!props.maintenanceServices || !form.service_type) return [];

      if (props.customer.maintenance_service?.service_type !== (form.service_type || '').toLowerCase()) {
        form.maintenance_service_id = null; // Reset maintenance service ID when service type changes
        form.maintenance_service = null; // Reset maintenance service when service type changes
      }

      return props.maintenanceServices.filter(s =>
        (s.service_type || '').toLowerCase() === (form.service_type || '').toLowerCase()
      );
    });

  
    const filteredChecklists = computed(() => {
      if (!selectedGroupId.value) return null;
      const group = props.checkListSummary.find(g => g.id === selectedGroupId.value);
      if (!group || group.total === group.remaining) return null;
      return props.subconCheckList.filter(checklist => checklist.group_id === selectedGroupId.value);
    });
    onMounted(() => {

      // Initialize form data
      form.installation_status = props.customer.installation_status ?
        installationStatus.value.filter((status) => status.id == props.customer.installation_status)[0] :
        null;
      getTownshipByCityId(props.customer.city_id);
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
      if (form.pop_id) {
        fetchPopDevices();
      }
      if (form.pop_device_id) {
        fetchDNs();
      }
      if (form.dn_id) {
        fetchSNs();

      }

      // else{
      //   if (form.pop_id && form.pop_id.partner_id) {
      //     console.log('Filtering partners by pop', form.pop_id.partner_id);
      //     filteredPartners.value = props.partners.filter(p => p.id === form.pop_id.partner_id);
      //     form.partner_id = form.pop_id.partner_id;

      //   } else {
      //     console.log('Resetting partners to all partners');
      //     filteredPartners.value = props.partners;
      //     form.partner_id = null;
      //   }
      // }

      // if (props.customer.pop_device_id && dnInfo.value && props.customer.sn_id) {
      //   gponInfo.value = `${dnInfo.value}`;
      // }
      // if (gponInfo.value && props.customer.gpon_ontid) {
      //   gponInfo.value += '/' + props.customer.gpon_ontid;
      // }

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
      if (props.snPort?.port_number) {
        form.splitter_no = snPortNoOptions.value.filter((d) => d.id == props.snPort.port_number)[0];
        snPort.value = form.splitter_no?.name;
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
      } else {
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
      form, form2, submit, isNumber, checkPerm, tab, tabClick, isEmptyObject, pop_devices, snPortNoOptions, gponOnuIdOptions, bundle, shareText, copyText, textContent,

      dnName,
      snName,
      snPort,
      popDevices,
      dnOptions,
      snOptions,
      filteredPops,
      filteredTownships,
      getTownshipByCityId,
      disableStatus,
      statusList,
      routeImagePreview,
      drumImagePreview,
      startMeterImagePreview,
      endMeterImagePreview,
      installationStatus,
      checklistImagePreviews,
      showFullView,
      selectedImageId,
      openFullView,
      closeFullView,
      installationApproval,
      isOptionDisabled,
      filteredInstallationServices,
      filteredMaintenanceServices,
      filteredPartners,
      selectedGroupId,
      filteredChecklists,
      closeModel
    };
  },
};
</script>
