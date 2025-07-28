<template>
    <AppLayout title="Customer Details">
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                ID :{{ customer.ftth_id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="inline-flex w-full bg-white rounded-t-lg font-semibold">
                    <ul id="tabs" class="flex">
                        <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                                @click="tabClick(1)" preserve-state>General</a></li>

                        <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                                @click="tabClick(2)" preserve-state>Attachment</a></li>


                    </ul>
                </div>
                <form @submit.prevent="submit">
                    <div class="bg-white  shadow-xl sm:rounded-lg sm:rounded-t-none p-6"
                        :class="[tab == 1 ? '' : 'hidden']">



                        <!-- Read-only Customer Information -->
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                            Customer Information</h6>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6 mt-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Unique ID</p>
                                <p class="mt-1">{{ customer.ftth_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Customer ID</p>
                                <p class="mt-1">{{ customer.isp_ftth_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Name</p>
                                <p class="mt-1">{{ customer.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Phone</p>
                                <p class="mt-1">{{ customer.phone_1 }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Township</p>
                                <p class="mt-1">{{ customer?.current_address?.township?.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Address</p>
                                <p class="mt-1">{{ customer?.current_address?.address }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Location</p>
                                <p class="mt-1">{{ customer?.current_address?.location }}</p>
                            </div>
                         
                            <div>
                                <p class="text-sm font-medium text-gray-500">Order Date</p>
                                <p class="mt-1"><span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{
                                            customer.order_date }}</span></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Assign Date</p>
                                <p class="mt-1"><span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{
                                            customer.subcom_assign_date }}</span></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Package</p>
                                <p class="mt-1">{{ customer.bandwidth }} Mbps</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Installation Timeline</p>
                                <p class="mt-1"><span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{
                                            customer.installation_service?.sla_hours }} Hours </span></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Prefer Install Date</p>
                                <p class="mt-1">{{ customer.prefer_install_date }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Order Remark</p>
                                <p class="mt-1">{{ customer.order_remark }}</p>
                            </div>






                        </div>
                        <hr class="my-4 md:min-w-full" />
                        <!-- Read-only Customer Information -->
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                            ODN Information</h6>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6 mt-6">



                            <div>
                                <p class="text-sm font-medium text-gray-500">Internet Service Provider</p>
                                <p class="mt-1">{{ customer?.isp?.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">POP ID</p>
                                <p class="mt-1">{{ snPort?.pop.site_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">DN Name</p>
                                <p class="mt-1">{{ snPort?.dn_splitter.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">DN Location</p>
                                <p class="mt-1">{{ snPort?.dn_splitter.location }}</p>
                            </div>



                            <div>
                                <p class="text-sm font-medium text-gray-500">SN Name</p>
                                <p class="mt-1">{{ snPort?.sn_splitter.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">SN Location</p>
                                <p class="mt-1">{{ snPort?.sn_splitter.location }}</p>
                            </div>
                           
                            <div>
                                <p class="text-sm font-medium text-gray-500">SN Port No.</p>
                                <p class="mt-1">SN Port {{ snPort?.port_number }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">PPPOE User Name</p>
                                <p class="mt-1">{{ customer.pppoe_username??'NA' }}</p>
                            </div>
                           
                            <div>
                                <p class="text-sm font-medium text-gray-500">PPPOE Password</p>
                                <p class="mt-1"> {{ customer.pppoe_password??'NA' }}</p>
                            </div>




                        </div>
                        <hr class="my-4 md:min-w-full" />
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                            Installation Information</h6>
                        <!-- Editable Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mt-6">
                            
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700"><span
                                        class="text-red-500">*</span>
                                    Installation Status</label>
                                <div class="mt-1 flex rounded-md shadow-sm" v-if="installationStatus.length !== 0">
                                    <multiselect deselect-label="Selected already" :options="installationStatus"
                                        track-by="id" label="name" v-model="form.installation_status"
                                        :allow-empty="false" :multiple="false" :taggable="false">
                                     <template v-slot:singleLabel="{ option }"><strong>{{ option.name }}
                                 </strong></template>
                                    </multiselect>
                                </div>
                                <p v-show="$page.props.errors.installation_status" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.installation_status }}</p>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700"><span
                                        class="text-red-500">*</span>
                                    Way List
                                    Date</label>
                                <input type="date" v-model="form.way_list_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-show="$page.props.errors.way_list_date" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.way_list_date }}</p>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Installation Date</label>
                                <input type="date" v-model="form.installation_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-show="$page.props.errors.installation_date" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.installation_date }}</p>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Fiber Distance</label>
                                <input type="number" v-model="form.fiber_distance"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-show="$page.props.errors.fiber_distance" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.fiber_distance }}</p>
                            </div>
                            <!-- <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Devices</label>
                                <multiselect deselect-label="Selected already" :options="bundle_equiptments"
                                    track-by="id" label="name" v-model="form.bundles" :allow-empty="false"
                                    :multiple="true" :taggable="false">
                                </multiselect>
                                <p v-show="$page.props.errors.bundles" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.bundles }}</p>
                            </div> -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">ONU Serial</label>
                                <input type="text" v-model="form.onu_serial"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-show="$page.props.errors.onu_serial" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.onu_serial }}</p>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">ONU Power</label>
                                <input type="text" v-model="form.onu_power"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-show="$page.props.errors.onu_power" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.onu_power }}</p>
                            </div>
                            <div class="col-span-1 sm:col-span-1">
                          <label for="actual_latitude" class="block text-sm font-medium text-gray-700">Actual Latitude </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                              <i class="fas fa-location-arrow"></i>
                            </span>
                            <input type="text" v-model="form.actual_latitude" name="actual_latitude" id="actual_latitude"
                              class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              v-on:keypress="isNumber(event)" />
                          </div>
                          <p v-show="$page.props.errors.actual_latitude" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.actual_latitude }}</p>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="actual_longitude" class="block text-sm font-medium text-gray-700">Actual Longitude </label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                              <i class="fas fa-location-arrow"></i>
                            </span>
                            <input type="text" v-model="form.actual_longitude" name="actual_longitude" id="actual_longitude"
                              class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              v-on:keypress="isNumber(event)" />
                          </div>
                          <p v-show="$page.props.errors.actual_longitude" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.actual_longitude }}</p>
                        </div>
                            <div class="col-span-1 sm:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">Installation Remark</label>
                                <textarea v-model="form.installation_remark"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                <p v-show="$page.props.errors.installation_remark" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.installation_remark }}</p>
                            </div>

                        </div>

                    </div>
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg sm:rounded-t-none p-6"
                        :class="[tab == 2 ? '' : 'hidden']">

                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                            Installation Data</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div v-for="(checklist, index) in subconCheckList" :key="checklist.id" class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ checklist.name }}
                                </label>
                                <input type="text" v-model="form[`checklist_${checklist.id}_title`]"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <div class="mt-2" v-if="checklist.has_attachment">
                                    <ImageUploader v-model="form[`checklist_${checklist.id}_attachment`]"
                                        v-model:imageUrl="checklistImagePreviews[checklist.id]"
                                        :existingImage="customer.checklist_images && customer.checklist_images[checklist.id] ? `/storage/${customer.checklist_images[checklist.id]}` : null"
                                        :upload-text="`Upload ${checklist.name} image`"
                                        :submitStatus="customer.checklist_values[checklist.id]?.status" />
                                </div>
                                <div class="py-2">
                                    <label for="status" class="block text-md font-medium text-gray-700"> Status
                                    </label>
                                    <div class="mt-2 flex">
                                        <label class="flex-auto items-center"> <input type="radio"
                                            class="form-radio h-5 w-5 text-blue-600"
                                            :name="form[`checklist_${checklist.id}`]"
                                            v-model="form[`checklist_${checklist.id}_status`]"
                                            value="" :disabled="form[`checklist_${checklist.id}_status`]=='approved'" /><span
                                            class="ml-2 text-gray-700 text-sm">No Data</span></label>
                                        <label class="flex-auto items-center"> <input type="radio"
                                                class="form-radio h-5 w-5 text-yellow-600"
                                                :name="form[`checklist_${checklist.id}`]"
                                                v-model="form[`checklist_${checklist.id}_status`]"
                                                value="requested" :disabled="form[`checklist_${checklist.id}_status`]=='approved'" /><span
                                                class="ml-2 text-gray-700 text-sm">Request</span></label>

                                        <label class="flex-auto items-center"> 
                                            <i class="mt-1 fa fa-lg fas fa-circle-check text-red-600" v-if="form[`checklist_${checklist.id}_status`]=='declined'"></i> 
                                            <i class="mt-1 fa fa-lg fa-regular fa-circle text-red-600" v-else></i>
                                            <span class="ml-2 text-gray-700 text-sm">Declined</span> </label>
                                        <label class="flex-auto items-center"> 
                                            <i class="mt-1 fa fa-lg fas fa-circle-check text-green-600" v-if="form[`checklist_${checklist.id}_status`]=='approved'"></i> 
                                            <i class="mt-1 fa fa-lg fa-regular fa-circle text-green-600" v-else></i>
                                            <span class="ml-2 text-gray-700 text-sm">Approved</span> </label>
                                    </div>
                                </div>

                                <p v-show="$page.props.errors[`checklist_${checklist.id}_attachment`]"
                                    class="mt-2 text-sm text-red-500">
                                    {{ $page.props.errors[`checklist_${checklist.id}_attachment`] }}
                                </p>
                            </div>
                        </div>


                    </div>
                    <div class="mt-6 flex justify-end">
                        <Link :href="route('customer.index')"
                            class="inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">
                        Back</Link>
                        <button type="button" @click="submit"
                            class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { ref, onMounted, provide, watch } from "vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import AppLayout from '@/Layouts/AppLayout.vue';
import ImageUploader from '@/Components/ImageUploader.vue';
import Multiselect from "@suadelabs/vue3-multiselect";
import SNPorts from "./DnsnReport.vue";
export default {
    name: "EditCustomer",
    components: {
        AppLayout,
        Multiselect,
        Link,
        ImageUploader
    },
    props: {
        customer: Object,
        snPort:Object,
        bundle_equiptments: Object,
        subconCheckList: Object,
    },
    setup(props) {

        const routeImagePreview = ref(null);
        const drumImagePreview = ref(null);
        const startMeterImagePreview = ref(null);
        const endMeterImagePreview = ref(null);
        const bundle = ref(null);
        const checklistImagePreviews = ref({});
        let tab = ref(1);
        function tabClick(val) {
            tab.value = val;
        }
        let actual_lat_long = '';
         if (props.customer.current_address?.actual_location) {
            actual_lat_long = props.customer.current_address.actual_location.split(",", 2);
        }
        // Initialize the form with checklist fields
        const form = useForm({
            installation_status: null,
            way_list_date: props.customer.way_list_date,
            installation_date: props.customer.installation_date,
            fiber_distance: props.customer.fiber_distance,
            bundles: "",
            onu_serial: props.customer.onu_serial,
            onu_power: props.customer.onu_power,
            route_kmz_image: null,
            drum_no_txt: props.customer.drum_no_txt,
            drum_no_image: null,
            start_meter_txt: props.customer.start_meter_txt,
            start_meter_image: null,
            end_meter_txt: props.customer.end_meter_txt,
            end_meter_image: null,
            installation_remark: props.customer.installation_remark,
            actual_latitude: (actual_lat_long[0]) ? actual_lat_long[0] : '',
            actual_longitude: (actual_lat_long[1]) ? actual_lat_long[1] : '',
            // Add dynamic checklist form fields
            ...Object.fromEntries(
                props.subconCheckList ?
                    Object.values(props.subconCheckList).flatMap(checklist => [
                        [`checklist_${checklist.id}_title`, props.customer.checklist_values?.[checklist.id]?.title || ''],
                        [`checklist_${checklist.id}_attachment`, null],
                        [`checklist_${checklist.id}_status`, props.customer.checklist_values?.[checklist.id]?.status || '']
                    ]) : []
            ),
        });

        const installationStatus = ref([
            { id: 'team_assigned', name: 'Team Assigned' ,'$isDisabled': true},
            { id: 'installation_start', name: 'Installation Start' },
            { id: 'installation_complete', name: 'Installation Complete' },
            { id: 'customer_cancel', name: 'Customer Cancel' },
            { id: 'port_full', name: 'Port Full' },
        ]);
        onMounted(() => {
            form.installation_status = props.customer.installation_status ?
                installationStatus.value.filter((status) => status.id == props.customer.installation_status)[0] :
                null;
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
        });
        const submit = () => {
            form.post(route('subcom.customer.update', props.customer.id), {
                preserveScroll: true,
                onSuccess: (page) => {
                    Toast.fire({
                        icon: "success",
                        title: page.props.flash.message,
                    });
                },
            });
        };
        return {
            submit,
            installationStatus,
            form,
            routeImagePreview,
            drumImagePreview,
            startMeterImagePreview,
            endMeterImagePreview,
            checklistImagePreviews,  // Add this line
            tab,
            tabClick
        };
    },
};


</script>