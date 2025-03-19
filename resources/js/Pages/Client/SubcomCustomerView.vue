<template>
    <AppLayout title="Customer Details">
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                ID :{{ customer.ftth_id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <!-- Read-only Customer Information -->
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                            Customer Information</h6>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6 mt-6">
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
                                <p class="mt-1">{{ customer?.township?.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Address</p>
                                <p class="mt-1">{{ customer.address }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Location</p>
                                <p class="mt-1">{{ customer.location }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Customer ID</p>
                                <p class="mt-1">{{ customer.ftth_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Order Date</p>
                                <p class="mt-1">{{ customer.order_date }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Package</p>
                                <p class="mt-1">{{ customer.package?.name }}, {{ customer.package?.speed }} Mbps</p>
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
                                <p class="mt-1">{{ customer?.pop?.site_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">DN Name</p>
                                <p class="mt-1">{{ customer?.dn?.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">DN Location</p>
                                <p class="mt-1">{{ customer?.dn?.location }}</p>
                            </div>



                            <div>
                                <p class="text-sm font-medium text-gray-500">SN Name</p>
                                <p class="mt-1">{{ customer?.sn?.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">SN Location</p>
                                <p class="mt-1">{{ customer?.sn?.location }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">SN Description</p>
                                <p class="mt-1">{{ customer?.sn?.description }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">SN Port No.</p>
                                <p class="mt-1">SN Port {{ customer.splitter_no }}</p>
                            </div>




                        </div>
                        <hr class="my-4 md:min-w-full" />
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                            Installation Information</h6>
                        <!-- Editable Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700"><span
                                    class="text-red-500">*</span> Installation Status</label>
                                <div class="mt-1 flex rounded-md shadow-sm" v-if="installationStatus.length !== 0">
                                    <multiselect deselect-label="Selected already" :options="installationStatus"
                                        track-by="id" label="name" v-model="form.installation_status"
                                        :allow-empty="false" :multiple="false" :taggable="false"></multiselect>
                                </div>
                                <p v-show="$page.props.errors.installation_status" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.installation_status }}</p>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700"><span
                                    class="text-red-500">*</span> Way List Date</label>
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
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Devices</label>
                                <multiselect deselect-label="Selected already" :options="bundle_equiptments"
                                    track-by="id" label="name" v-model="form.bundles" :allow-empty="false"
                                    :multiple="true" :taggable="false">
                                </multiselect>
                                <p v-show="$page.props.errors.bundles" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.bundles }}</p>
                            </div>
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
                            <div class="col-span-1 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Installation Remark</label>
                                <textarea v-model="form.installation_remark"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                <p v-show="$page.props.errors.installation_remark" class="mt-2 text-sm text-red-500">{{
                                        $page.props.errors.installation_remark }}</p>
                            </div>

                        </div>
                        <hr class="my-4 md:min-w-full" />
                        <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                            Installation Data</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-4 mt-6 gap-4">
                            <!-- Fiber Route KMZ -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Fiber Route</label>
                                <div class="mt-2 flex">
                                    <label for="route_kmz_image" class="block  py-3 text-sm font-medium text-gray-500">
                                        Upload
                                        Fiber Route KMZ Below:</label>
                                </div>
                                <ImageUploader v-model="form.route_kmz_image" v-model:imageUrl="routeImagePreview"
                                    :existingImage="customer.route_kmz_image ? `/storage/${customer.route_kmz_image}` : null"
                                    upload-text="Upload route image" />
                                <p v-show="$page.props.errors.route_kmz_image" class="mt-2 text-sm text-red-500">{{
                                        $page.props.errors.route_kmz_image }}</p>
                            </div>

                            <!-- Drum Number -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Fiber Drum Number</label>
                                <input type="text" v-model="form.drum_no_txt"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <div class="mt-2">
                                    <ImageUploader v-model="form.drum_no_image" v-model:imageUrl="drumImagePreview"
                                        :existingImage="customer.drum_no_image ? `/storage/${customer.drum_no_image}` : null"
                                        upload-text="Upload drum image" />
                                </div>
                                <p v-show="$page.props.errors.drum_no_image" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.drum_no_image }}</p>
                            </div>

                            <!-- Start Meter -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Start Meter</label>
                                <input type="text" v-model="form.start_meter_txt"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <div class="mt-2">
                                    <ImageUploader v-model="form.start_meter_image"
                                        v-model:imageUrl="startMeterImagePreview"
                                        :existingImage="customer.start_meter_image ? `/storage/${customer.start_meter_image}` : null"
                                        upload-text="Upload start meter image" />
                                </div>
                                <p v-show="$page.props.errors.start_meter_image" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.start_meter_image }}</p>
                            </div>

                            <!-- End Meter -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">End Meter</label>
                                <input type="text" v-model="form.end_meter_txt"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <div class="mt-2">
                                    <ImageUploader v-model="form.end_meter_image"
                                        v-model:imageUrl="endMeterImagePreview"
                                        :existingImage="customer.end_meter_image ? `/storage/${customer.end_meter_image}` : null"
                                        upload-text="Upload end meter image" />
                                </div>
                                <p v-show="$page.props.errors.end_meter_image" class="mt-2 text-sm text-red-500">{{
                                    $page.props.errors.end_meter_image }}</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Link :href="route('customer.index')"
                            class="inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Back</Link>
                            <button type="button" @click="submit"
                                class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
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
        bundle_equiptments: Object,
    },
    setup(props) {

        const routeImagePreview = ref(null);
        const drumImagePreview = ref(null);
        const startMeterImagePreview = ref(null);
        const endMeterImagePreview = ref(null);
        const bundle = ref(null);
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
        });
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
        onMounted(() => {
            form.installation_status = props.customer.installation_status?
                installationStatus.value.filter((status) => status.id == props.customer.installation_status)[0] :
                null ;
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
        };
    },
};


</script>