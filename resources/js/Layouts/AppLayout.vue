<template>
  <div id="root">
    <!-- Head Section -->
    <Head>
      <title>{{ ($page.props.isp)?$page.props.isp.name + ' |' :"" }} {{ $page.props.application_name }}</title>
      <meta name="description" content="ISP Manager OSS BSS SYSTEM" />
     
      <link rel="icon" type="image/png" href="/storage/logos/favicon.png" />
    </Head>

    <!-- Sidebar Navigation -->
    <nav :class="[
      'sm:h-screen overflow-y-auto sm:fixed sm:top-0 sm:left-0 transition-all duration-300 md:overflow-y-auto md:overflow-hidden scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-200',
      isCollapsed ? 'sm:w-20 z-10' : 'sm:w-64',
      'dark:bg-gray-900 p-4 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-200'
    ]">
    
      <div class="w-full flex justify-between items-center shadow-none shadow-gray-300 shadow-left">
       
        <a href="javascript:void(0)" class="text-gray-400 flex items-center w-full">
         
          <span v-if="$page.props.login_type === 'isp'">
            <img v-if="$page.props.isp?.logo" :src="`/storage/${$page.props.isp.logo}`" alt="Logo" class="w-16" />
          </span>
          <span v-else-if="$page.props.login_type === 'partner'">
            <img v-if="$page.props.partner?.logo" :src="`/storage/${$page.props.partner.logo}`" alt="Logo" class="w-16" />
          </span>
          <span v-else>
            <img v-if="$page.props.logo_small" :src="`/storage/${$page.props.logo_small}`" alt="Logo" class="w-16" />
          </span>
          <span v-if="!isCollapsed" class="font-bold text-md self-center text-center w-full">
            {{ $page.props.application_name }}
          </span>
        </a>
        <button class="text-black md:hidden" @click="toggleSidebar">
          <i class="fas fa-bars"></i>
        </button>
        <button @click="toggleCollapse" class="text-blue-900 hidden w-4 md:block focus:ring-0 focus:outline-none -mr-4">
          <i :class="isCollapsed ? 'fas fa-caret-right' : 'fas fa-caret-left'"></i>
        </button>
      </div>
      
      <!-- Menu Links -->
      <div :class="{ hidden: !sidebarOpen }" class="md:flex md:flex-col mt-4 w-full grid grid-cols-1 gap-2">
        <ExpandableMenu
          v-for="panel in filteredPanels"
          :key="panel.name"
          :name="panel.name"
          :label="panel.label"
          :icon="panel.icon"
          :isOpen="panel.isOpen"
          :isCollapsed="isCollapsed"
          @toggle="togglePanel(panel.name)"
        >
          <jet-nav-link v-for="link in panel.links" :key="link.name"
            :href="route(link.route)"
            :active="checkActive(link.route)"
            :isCollapsed="isCollapsed" 
            :show = link.show
            class="flex items-center px-3"
          >
            <i :class="[link.icon, 'min-w-[20px]']"></i> 
            <span>
              {{ link.name }}
            </span>
          </jet-nav-link>
        </ExpandableMenu>
      </div>
    </nav>

    <!-- Main Content -->
    <div :class="[
      'relative transition-all duration-300',
      isCollapsed ? 'md:ml-20' : 'md:ml-64',
      'bg-gray-100 dark:bg-gray-900 h-screen overflow-auto scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-gray-100'
    ]">
      <!-- Page Header -->
      <header class="relative w-full bg-blue-900 md:flex-row md:flex-nowrap md:justify-start flex items-center py-4" :style="{ backgroundColor: $page.props.theme_color }"
      v-if="$slots.header">
      <div class="px-4 md:px-10 mx-auto w-full my-4">
        <slot name="header"> </slot>
      </div>
      <div class="ml-3 absolute top-8 right-8">
        <jet-dropdown align="right" width="48">
          <template #trigger>
            <button v-if="$page.props.jetstream.managesProfilePhotos"
              class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
              <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                :alt="$page.props.auth.user.name" />
            </button>

            <span v-else class="inline-flex rounded-md">
              <button type="button"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                {{ $page.props.auth.user.name }}

                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                  fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </button>
            </span>
          </template>

          <template #content>
            <!-- Account Management -->
            <div class="block px-4 py-2 text-xs text-gray-400">Manage Account</div>

            <jet-dropdown-link :href="route('profile.show')"> Profile </jet-dropdown-link>

            <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures"> API
              Tokens </jet-dropdown-link>

            <div class="border-t border-gray-100"></div>

            <!-- Authentication -->
            <form @submit.prevent="logout">
              <jet-dropdown-link as="button"> Log Out </jet-dropdown-link>
            </form>
          </template>
        </jet-dropdown>
      </div>
    </header>
      
      <!-- Page Content -->
      <main class="p-2">
        <slot></slot>
      </main>

      <!-- Footer -->
      <footer class="block py-4">
        <div class="container mx-auto px-4">
          <hr class="mb-4 border-b-1 border-blueGray-200" />
          <div class="flex flex-wrap items-center md:justify-between justify-center">
            <div class="w-full md:w-4/12 px-4">
              <div class="text-sm text-blueGray-500 font-semibold py-1">
                &copy; {{ new Date().getFullYear() }}
              </div>
            </div>
            <div class="w-full md:w-8/12 px-4">
              <ul class="flex flex-wrap list-none md:justify-end justify-center">
                <li class="text-sm text-blueGray-500 font-semibold py-1">
                  {{ $page.props.application_name }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>
<script>
import JetNavLink from "@/Jetstream/NavLink.vue";
import { ref } from 'vue';

import ExpandableMenu from "@/Components/ExpandableMenu.vue";
import { Head } from "@inertiajs/vue3";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
export default {
  components: {
  
    ExpandableMenu,
    Head,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
  'jet-nav-link': JetNavLink,
  },
  
  provide() {
    const activeSubmenu = ref(null);
    return {
      activeSubmenu,
      setActiveSubmenu: (name) => {
        activeSubmenu.value = name;
      },
    };
  },
  data() {
    return {
      sidebarOpen: false,
      isCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
      panels: [
        {
          name: "admin",
          label: "Admin",
          icon:"fas fa-user-tie",
          isOpen: false, // Tracks if the panel is open
          links: [
            { name: "User Setup", route: "user.index", icon: "fas fa-user mr-2" ,show : true},
            { name: "Role Setup", route: "role.index", icon: "fas fa-user-tag mr-2" ,show : true},
            { name: "Partner Setup", route: "partner.index", icon: "fas fa-user-tag mr-2" ,show : true},
            { name: "ISP Setup", route: "isps.index", icon: "fas fa-user-tag mr-2",show : true },
            { name: "Subcon Setup", route: "subcom.index", icon: "fas fa-handshake mr-2" ,show : true},
            { name: "CheckList Group", route: "subcon-checklists-group.index", icon: "fas fa-handshake mr-2" ,show : true},
            { name: "CheckList", route: "subcon-checklists.index", icon: "fas fa-handshake mr-2" ,show : true},
            { name: "Main RCA Setup", route: "root-causes.index", icon: "fas fa-handshake mr-2" ,show : true},
            { name: "Sub RCA Setup", route: "sub-root-causes.index", icon: "fas fa-handshake mr-2" ,show : true},
            




            { name: "City Setup", route: "city.index", icon: "fas fa-city mr-2" ,show : true},
            { name: "Township Setup", route: "township.index", icon: "fas fa-city mr-2" ,show : true},
            { name: "Zone Setup", route: "zone.index", icon: "fas fa-handshake mr-2" ,show : true},
            { name: "Project Setup", route: "project.index", icon: "fas fa-handshake mr-2" ,show : true},
       

           
   

            { name: "Customer Status", route: "status.index", icon: "fas fa-user-tag mr-2" ,show : true},
            { name: "Template", route: "template.index", icon: "fas fa-envelope mr-2" ,show : true},
            { name: "Announcement", route: "announcement.list", icon: "fas fa-bullhorn mr-2" ,show : true},
            { name: "SMS Gateway", route: "smsgateway.index", icon: "fas fa-sms mr-2" ,show : true},
            // { name: "Radius Config", route: "radiusconfig.index", icon: "fas fa-sms mr-2" },
           
          
            { name: "System Setting", route: "setting.index", icon: "fas fa-screwdriver-wrench mr-2" ,show : true},
          ],
        },
         {
          name: "product_catalog",
          label: "Product Catalog",
          icon:"fas fa-money-check-dollar",
          isOpen: false, // Tracks if the panel is open
          links: [
            
            { name: "Material Setup", route: "equiptment.index", icon: "fas fa-warehouse mr-2" ,show : true,show : true},

           
           
            { name: "Port Sharing Setup", route: "port-sharing-service.index", icon: "fas fa-cube mr-2" ,show : true,show : true},
            { name: "Maintenance Setup", route: "maintenance-service.index", icon: "fas fa-cube mr-2" ,show : true,show : true},
            { name: "Installation Setup", route: "installation-service.index", icon: "fas fa-cube mr-2" ,show : true,show : true},
            { name: "Discount Setup", route: "discount-setup.index", icon: "fas fa-percentage mr-2" ,show : true,show : true},

          
            { name: "Suspense Config", route: "billconfig.index", icon: "fas fa-cube mr-2" ,show : true,show : true},
          
          ],
        },
        {
          name: "odn",
          label: "ODN ",
          icon:"fas fa-route",
          isOpen: false,
          links: [
            { name: "POP Setup", route: "pop.index", icon: "fas fa-building mr-2" ,show : true},
            { name: "ODF", route: "odfs.index", icon: "fas fa-network-wired mr-2" ,show : true},
            { name: "ODB", route: "odbs.index", icon: "fas fa-network-wired mr-2" ,show : true},
            { name: "ODB-Detail", route: "odb-fiber-cables.index", icon: "fas fa-network-wired mr-2" ,show : true},
            { name: "Fiber Core", route: "fiber-cables.index", icon: "fas fa-network-wired mr-2",show : true },
           
            { name: "Connection Node", route: "jc-boxes.index", icon: "fas fa-link mr-2" ,show : true},
            { name: "Core Assignment", route: "core-assignments.index", icon: "fas fa-equals mr-2",show : true },
            { name: "Distribution Node", route: "dn-boxes.index", icon: "fas fa-tv mr-2",show : true },
            { name: "DN Splitter", route: "dn-splitters.index", icon: "fas fa-network-wired mr-2" ,show : true},
            { name: "SN Box", route: "sn-boxes.index", icon: "fas fa-tv mr-2",show : true },
            { name: "SN Splitter", route: "sn-splitters.index", icon: "fas fa-network-wired mr-2" ,show : true},
          ],
        },
        {
          name: "user",
          label: "Operation ",
          icon:"fas fa-users",
          isOpen: false,
          links: [
            { name: "Dashboard", route: "dashboard", icon: "fas fa-tv mr-2" , show : true},
            { name: "Customer", route: "customer.index", icon: "fas fa-users mr-2",show : true },
            { name: "Nearby SN ", route: "sn.nearby", icon: "fas fa-circle-info mr-2" ,show: this.$page.props?.role?.check_sn == 1 },
            { name: "Service Request", route: "servicerequest.index", icon: "fas fa-tasks mr-2", show: this.$page.props?.role?.service_request == 1 },
            { name: "Incident Panel", route: "incident.index", icon: "fas fa-arrow-up-right-from-square mr-2 text-blue-600" ,show : true},
          ],
        },
        {
          name: "billing",
          label: "Billing Panel",
          icon:"fas fa-file-invoice",
          isOpen: false,
          links: [
            { name: "Bill Generator", route: "billGenerator", icon: "fas fa-cogs mr-2" ,show : true},
            { name: "Temp Bill List", route: "tempBilling", icon: "fas fa-clipboard-list mr-2" ,show : true},
            { name: "Final Bill List", route: "showbill", icon: "fas fa-coins mr-2",show : true },
            // { name: "Bill Receipt", route: "receipt.index", icon: "fas fa-file-invoice-dollar mr-2" },
          ],
        },
        {
          name: "partner",
          label: "Partner ",
          icon:"fas fa-users",
          isOpen: false,
          links: [
            { name: "Home", route: "home", icon: "fas fa-home mr-2",show : true },
            { name: "Customer", route: "customer.index", icon: "fas fa-users mr-2",show : true },
            { name: "DN SN Report", route: "dnSnReport", icon: "fas fa-tower-broadcast mr-2",show : true },
          ],
        },
        {
          name: "isp",
          label: "ISP ",
          icon:"fas fa-users",
          isOpen: false,
          links: [
            { name: "Home", route: "home", icon: "fas fa-home mr-2" ,show : true},
            { name: "Customer", route: "customer.index", icon: "fas fa-users mr-2" ,show : true},
            { name: "Incident Panel", route: "incident.index", icon: "fas fa-arrow-up-right-from-square mr-2 text-blue-600" ,show : true},
            { name: "Incident Report", route: "incidentReport", icon: "fas fa-users mr-2",show : true },
          ],
        },
        {
          name: "subcon",
          label: "Fiber Team ",
          icon:"fas fa-users",
          isOpen: false,
          links: [
            { name: "Home", route: "home", icon: "fas fa-home mr-2" ,show : true},
            { name: "Customer", route: "customer.index", icon: "fas fa-users mr-2",show : true },
            { name: "Incident Panel", route: "incident.index", icon: "fas fa-arrow-up-right-from-square mr-2 text-blue-600",show : true },
          ],
        },
        {
          name: "report",
          label: "Report Panel",
          icon:"fas fa-chart-line",
          isOpen: false,
          links: [
            { name: "Incident Report", route: "incidentReport", icon: "fas fa-users mr-2" ,show : true},
            { name: "Bill Report", route: "dailyreceipt", icon: "fa fa-money-bill mr-2",show : true },
            // { name: "Radius User Report", route: "showRadius", icon: "fas fa-server mr-2" },
            // { name: "IP Usages Report", route: "publicIpReport", icon: "fas fa-server mr-2" },
            { name: "DN SN Report", route: "dnSnReport", icon: "fas fa-tower-broadcast mr-2" ,show : true},
            { name: "Activity Log", route: "activity-log.index", icon: "fas fa-circle-info mr-2" ,show : true},
          
          ],
        },
      ],
    };
  },
  computed: {
    isAdmin() {
      return this.$page.props?.role?.id === 1 || this.$page.props?.role?.id === 2;
    },
    filteredPanels() {
      return this.panels.filter(panel => {
        switch (panel.name) {
          case 'admin':
            return this.$page.props?.role?.admin_panel;
          case 'user':
            return this.$page.props?.role?.customer_panel;
          case 'odn':
            return this.$page.props?.role?.dn_panel;
          case 'product_catalog':
            return this.$page.props?.role?.product_catalog;
          case 'billing':
            return this.$page.props?.role?.billing_panel;
          case 'report':
            return this.$page.props?.role?.report_panel;
          case 'partner':
            return this.$page.props.login_type == 'partner';
          case 'isp':
            return this.$page.props.login_type == 'isp';
          case 'subcon':
            return this.$page.props.login_type == 'subcon';
          case 'servicerequest':
            return this.$page.props?.role?.service_request;
          return false;
        }
      });
    }
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
    toggleCollapse() {
      this.isCollapsed = !this.isCollapsed;
      localStorage.setItem("sidebarCollapsed", this.isCollapsed);
    },
    togglePanel(panelName) {
      this.panels = this.panels.map((panel) => ({
        ...panel,
        isOpen: panel.name === panelName ? !panel.isOpen : false,
      }));
    },
    openPanelForRoute() {
      console.log(route().current());
      const currentRoute = route().current().split(".")[0];
      
      this.panels.forEach((panel) => {
        panel.isOpen = panel.links.some((link) => currentRoute === link.route.split(".")[0]);
      });
    },
    checkActive(link) {
      const currentRoute = route().current().split(".")[0];
      return currentRoute === link.split(".")[0];
    },
    logout() {
      this.$inertia.post(route("logout"));
    },
  },
  created() {
    this.openPanelForRoute();
  },
  watch: {
    "$page.props": {
      deep: true,
      handler() {
        this.openPanelForRoute();
      },
    },
  },
};
</script>



