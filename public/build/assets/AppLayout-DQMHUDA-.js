import{o as a,d as n,a as t,n as u,t as p,e as c,l as v,s as R,x as B,Z as A,r as L,q as b,b as y,w as m,F as $,g as P,J as I,f as x,c as _,h as N}from"./app-DdoqqyeN.js";import{b as D,_ as F,a as M}from"./DropdownLink-DupIRA2Y.js";import{_ as O}from"./_plugin-vue_export-helper-DlAUqK2U.js";const T={props:{label:String,icon:String,isOpen:Boolean,isCollapsed:Boolean,name:String},data(){return{showSubmenu:!1}},inject:["activeSubmenu","setActiveSubmenu"],methods:{toggleSubmenu(){this.isCollapsed?this.showSubmenu&&this.activeSubmenu===this.name?(this.showSubmenu=!1,this.setActiveSubmenu(null)):(this.showSubmenu=!0,this.setActiveSubmenu(this.name)):this.$emit("toggle")}},watch:{activeSubmenu(s){s!==this.name&&(this.showSubmenu=!1)}},emits:["toggle"]},q={class:"relative group"},G={class:"flex items-center min-w-max"},E={class:"px-2 py-2 text-xs font-bold text-gray-600 uppercase bg-indigo-50"},H={key:1,class:"mt-1 space-y-1"};function z(s,e,o,d,l,r){return a(),n("div",q,[t("button",{onClick:e[0]||(e[0]=(...f)=>r.toggleSubmenu&&r.toggleSubmenu(...f)),class:u(["w-full flex items-center justify-between p-2 transition-colors text-gray-600 hover:gray-blue-500 text-xs uppercase py-3 font-bold rounded-md bg-blueGray-100 shadow-sm cursor-pointer focus:border-none",[o.isCollapsed?"px-3":"px-4"]])},[t("div",G,[t("i",{class:u(["w-5",o.icon])},null,2),t("span",{class:u([o.isCollapsed?"opacity-0 group-hover:opacity-100":"opacity-100","ml-3"])},p(o.isCollapsed?"":o.label),3)]),o.isCollapsed?c("",!0):(a(),n("i",{key:0,class:u(["fas transition-transform",o.isOpen?"fa-chevron-down":"fa-chevron-right"])},null,2))],2),o.isCollapsed?(a(),n("div",{key:0,class:u(["fixed left-20 top-5 min-w-[200px] bg-indigo-50 shadow-lg",{hidden:!l.showSubmenu||r.activeSubmenu!==o.name}])},[t("div",null,[t("h6",E,p(o.label),1),v(s.$slots,"default",{},void 0,!0)])],2)):R((a(),n("div",H,[v(s.$slots,"default",{},void 0,!0)],512)),[[B,o.isOpen]])])}const J=O(T,[["render",z],["__scopeId","data-v-90c65adb"]]),V={components:{JetNavLink:D,ExpandableMenu:J,Head:A,JetDropdown:F,JetDropdownLink:M},provide(){const s=L(null);return{activeSubmenu:s,setActiveSubmenu:e=>{s.value=e}}},data(){return{sidebarOpen:!1,isCollapsed:localStorage.getItem("sidebarCollapsed")==="true",panels:[{name:"admin",label:"Admin",icon:"fas fa-user-tie",isOpen:!1,links:[{name:"User Setup",route:"user.index",icon:"fas fa-user mr-2"},{name:"Role Setup",route:"role.index",icon:"fas fa-user-tag mr-2"},{name:"Partner Setup",route:"partner.index",icon:"fas fa-user-tag mr-2"},{name:"ISP Setup",route:"isps.index",icon:"fas fa-user-tag mr-2"},{name:"Subcon Setup",route:"subcom.index",icon:"fas fa-handshake mr-2"},{name:"City Setup",route:"city.index",icon:"fas fa-city mr-2"},{name:"Township Setup",route:"township.index",icon:"fas fa-city mr-2"},{name:"Zone Setup",route:"zone.index",icon:"fas fa-handshake mr-2"},{name:"Project Setup",route:"project.index",icon:"fas fa-handshake mr-2"},{name:"Bundle Setup",route:"equiptment.index",icon:"fas fa-gamepad mr-2"},{name:"POP Setup",route:"pop.index",icon:"fas fa-building mr-2"},{name:"DN Setup",route:"port.index",icon:"fas fa-server mr-2"},{name:"SN Setup",route:"snport.index",icon:"fas fa-network-wired mr-2"},{name:"SLA Setup",route:"sla.index",icon:"fas fa-percentage mr-2"},{name:"Package Setup",route:"package.index",icon:"fas fa-cube mr-2"},{name:"Customer Status",route:"status.index",icon:"fas fa-user-tag mr-2"},{name:"Template",route:"template.index",icon:"fas fa-envelope mr-2"},{name:"Announcement",route:"announcement.list",icon:"fas fa-bullhorn mr-2"},{name:"SMS Gateway",route:"smsgateway.index",icon:"fas fa-sms mr-2"},{name:"Radius Config",route:"radiusconfig.index",icon:"fas fa-sms mr-2"},{name:"Billing Config",route:"billconfig.index",icon:"fas fa-sms mr-2"},{name:"Activity Log",route:"activity-log.index",icon:"fas fa-circle-info mr-2"},{name:"System Setting",route:"setting.index",icon:"fas fa-screwdriver-wrench mr-2"}]},{name:"user",label:"Operation ",icon:"fas fa-users",isOpen:!1,links:[{name:"Dashboard",route:"dashboard",icon:"fas fa-tv mr-2"},{name:"Customer",route:"customer.index",icon:"fas fa-users mr-2"},{name:"Service Request",route:"servicerequest.index",icon:"fas fa-tasks mr-2"},{name:"Incident Panel",route:"incident.index",icon:"fas fa-arrow-up-right-from-square mr-2 text-blue-600"}]},{name:"billing",label:"Billing Panel",icon:"fas fa-file-invoice",isOpen:!1,links:[{name:"Bill Generator",route:"billGenerator",icon:"fas fa-cogs mr-2"},{name:"Temp Bill List",route:"tempBilling",icon:"fas fa-clipboard-list mr-2"},{name:"Final Bill List",route:"showbill",icon:"fas fa-coins mr-2"},{name:"Bill Receipt",route:"receipt.index",icon:"fas fa-file-invoice-dollar mr-2"}]},{name:"partner",label:"Partner ",icon:"fas fa-users",isOpen:!1,links:[{name:"Home",route:"home",icon:"fas fa-home mr-2"},{name:"Customer",route:"customer.index",icon:"fas fa-users mr-2"},{name:"DN SN Report",route:"dnSnReport",icon:"fas fa-tower-broadcast mr-2"}]},{name:"isp",label:"ISP ",icon:"fas fa-users",isOpen:!1,links:[{name:"Home",route:"home",icon:"fas fa-home mr-2"},{name:"Customer",route:"customer.index",icon:"fas fa-users mr-2"},{name:"Incident Panel",route:"incident.index",icon:"fas fa-arrow-up-right-from-square mr-2 text-blue-600"},{name:"Incident Report",route:"incidentReport",icon:"fas fa-users mr-2"}]},{name:"subcon",label:"Fiber Team ",icon:"fas fa-users",isOpen:!1,links:[{name:"Home",route:"home",icon:"fas fa-home mr-2"},{name:"Customer",route:"customer.index",icon:"fas fa-users mr-2"},{name:"Incident Panel",route:"incident.index",icon:"fas fa-arrow-up-right-from-square mr-2 text-blue-600"}]},{name:"report",label:"Report Panel",icon:"fas fa-chart-line",isOpen:!1,links:[{name:"Incident Report",route:"incidentReport",icon:"fas fa-users mr-2"},{name:"Bill Report",route:"dailyreceipt",icon:"fa fa-money-bill mr-2"},{name:"DN SN Report",route:"dnSnReport",icon:"fas fa-tower-broadcast mr-2"}]}]}},computed:{isAdmin(){var s,e,o,d;return((e=(s=this.$page.props)==null?void 0:s.role)==null?void 0:e.id)===1||((d=(o=this.$page.props)==null?void 0:o.role)==null?void 0:d.id)===2},filteredPanels(){return this.panels.filter(s=>{var e,o,d,l,r,f,h,w;switch(s.name){case"admin":return(o=(e=this.$page.props)==null?void 0:e.role)==null?void 0:o.admin_panel;case"user":return(l=(d=this.$page.props)==null?void 0:d.role)==null?void 0:l.customer_panel;case"billing":return(f=(r=this.$page.props)==null?void 0:r.role)==null?void 0:f.billing_panel;case"report":return(w=(h=this.$page.props)==null?void 0:h.role)==null?void 0:w.report_panel;case"partner":return this.$page.props.login_type=="partner";case"isp":return this.$page.props.login_type=="isp";case"subcon":return this.$page.props.login_type=="subcon"}})}},methods:{toggleSidebar(){this.sidebarOpen=!this.sidebarOpen},toggleCollapse(){this.isCollapsed=!this.isCollapsed,localStorage.setItem("sidebarCollapsed",this.isCollapsed)},togglePanel(s){this.panels=this.panels.map(e=>({...e,isOpen:e.name===s?!e.isOpen:!1}))},openPanelForRoute(){console.log(route().current());const s=route().current().split(".")[0];this.panels.forEach(e=>{e.isOpen=e.links.some(o=>s===o.route.split(".")[0])})},checkActive(s){return route().current().split(".")[0]===s.split(".")[0]},logout(){this.$inertia.post(route("logout"))}},created(){this.openPanelForRoute()},watch:{"$page.props":{deep:!0,handler(){this.openPanelForRoute()}}}},Y={id:"root"},Z={class:"w-full flex justify-between items-center shadow-none shadow-gray-300 shadow-left"},U={href:"javascript:void(0)",class:"text-gray-400 flex items-center w-full"},K={key:0},Q=["src"],W={key:1},X=["src"],ee={key:2},se=["src"],te={key:3,class:"font-bold text-md self-center text-center w-full"},oe={class:"px-4 md:px-10 mx-auto w-full my-4"},ae={class:"ml-3 absolute top-8 right-8"},ne={key:0,class:"flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"},re=["src","alt"],ie={key:1,class:"inline-flex rounded-md"},le={type:"button",class:"inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"},ue={class:"p-2"},pe={class:"block py-4"},me={class:"container mx-auto px-4"},de={class:"flex flex-wrap items-center md:justify-between justify-center"},ce={class:"w-full md:w-4/12 px-4"},fe={class:"text-sm text-blueGray-500 font-semibold py-1"},ge={class:"w-full md:w-8/12 px-4"},be={class:"flex flex-wrap list-none md:justify-end justify-center"},he={class:"text-sm text-blueGray-500 font-semibold py-1"};function we(s,e,o,d,l,r){var k,C;const f=b("Head"),h=b("jet-nav-link"),w=b("ExpandableMenu"),S=b("jet-dropdown-link"),j=b("jet-dropdown");return a(),n("div",Y,[y(f,null,{default:m(()=>[t("title",null,p(s.$page.props.isp?s.$page.props.isp.name+" |":"")+" "+p(s.$page.props.application_name),1),e[3]||(e[3]=t("meta",{name:"description",content:"ISP Manager OSS BSS SYSTEM"},null,-1)),e[4]||(e[4]=t("link",{rel:"icon",type:"image/png",href:"/storage/logos/favicon.png"},null,-1))]),_:1}),t("nav",{class:u(["sm:h-screen overflow-y-auto sm:fixed sm:top-0 sm:left-0 transition-all duration-300 md:overflow-y-auto md:overflow-hidden scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-200",l.isCollapsed?"sm:w-20 z-10":"sm:w-64","dark:bg-gray-900 p-4 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-200"])},[t("div",Z,[t("a",U,[s.$page.props.login_type==="isp"?(a(),n("span",K,[(k=s.$page.props.isp)!=null&&k.logo?(a(),n("img",{key:0,src:`/storage/${s.$page.props.isp.logo}`,alt:"Logo",class:"w-16"},null,8,Q)):c("",!0)])):s.$page.props.login_type==="partner"?(a(),n("span",W,[(C=s.$page.props.partner)!=null&&C.logo?(a(),n("img",{key:0,src:`/storage/${s.$page.props.partner.logo}`,alt:"Logo",class:"w-16"},null,8,X)):c("",!0)])):(a(),n("span",ee,[s.$page.props.logo_small?(a(),n("img",{key:0,src:`/storage/${s.$page.props.logo_small}`,alt:"Logo",class:"w-16"},null,8,se)):c("",!0)])),l.isCollapsed?c("",!0):(a(),n("span",te,p(s.$page.props.application_name),1))]),t("button",{class:"text-black md:hidden",onClick:e[0]||(e[0]=(...i)=>r.toggleSidebar&&r.toggleSidebar(...i))},e[5]||(e[5]=[t("i",{class:"fas fa-bars"},null,-1)])),t("button",{onClick:e[1]||(e[1]=(...i)=>r.toggleCollapse&&r.toggleCollapse(...i)),class:"text-blue-900 hidden w-4 md:block focus:ring-0 focus:outline-none -mr-4"},[t("i",{class:u(l.isCollapsed?"fas fa-caret-right":"fas fa-caret-left")},null,2)])]),t("div",{class:u([{hidden:!l.sidebarOpen},"md:flex md:flex-col mt-4 w-full grid grid-cols-1 gap-2"])},[(a(!0),n($,null,P(r.filteredPanels,i=>(a(),_(w,{key:i.name,name:i.name,label:i.label,icon:i.icon,isOpen:i.isOpen,isCollapsed:l.isCollapsed,onToggle:g=>r.togglePanel(i.name)},{default:m(()=>[(a(!0),n($,null,P(i.links,g=>(a(),_(h,{key:g.name,href:s.route(g.route),active:r.checkActive(g.route),isCollapsed:l.isCollapsed,class:"flex items-center px-3"},{default:m(()=>[t("i",{class:u([g.icon,"min-w-[20px]"])},null,2),t("span",null,p(g.name),1)]),_:2},1032,["href","active","isCollapsed"]))),128))]),_:2},1032,["name","label","icon","isOpen","isCollapsed","onToggle"]))),128))],2)],2),t("div",{class:u(["relative transition-all duration-300",l.isCollapsed?"md:ml-20":"md:ml-64","bg-gray-100 dark:bg-gray-900 h-screen overflow-auto scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-gray-100"])},[s.$slots.header?(a(),n("header",{key:0,class:"relative w-full bg-blue-900 md:flex-row md:flex-nowrap md:justify-start flex items-center py-4",style:I({backgroundColor:s.$page.props.theme_color})},[t("div",oe,[v(s.$slots,"header")]),t("div",ae,[y(j,{align:"right",width:"48"},{trigger:m(()=>[s.$page.props.jetstream.managesProfilePhotos?(a(),n("button",ne,[t("img",{class:"h-8 w-8 rounded-full object-cover",src:s.$page.props.auth.user.profile_photo_url,alt:s.$page.props.auth.user.name},null,8,re)])):(a(),n("span",ie,[t("button",le,[x(p(s.$page.props.auth.user.name)+" ",1),e[6]||(e[6]=t("svg",{class:"ml-2 -mr-0.5 h-4 w-4",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20",fill:"currentColor"},[t("path",{"fill-rule":"evenodd",d:"M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z","clip-rule":"evenodd"})],-1))])]))]),content:m(()=>[e[10]||(e[10]=t("div",{class:"block px-4 py-2 text-xs text-gray-400"},"Manage Account",-1)),y(S,{href:s.route("profile.show")},{default:m(()=>e[7]||(e[7]=[x(" Profile ")])),_:1},8,["href"]),s.$page.props.jetstream.hasApiFeatures?(a(),_(S,{key:0,href:s.route("api-tokens.index")},{default:m(()=>e[8]||(e[8]=[x(" API Tokens ")])),_:1},8,["href"])):c("",!0),e[11]||(e[11]=t("div",{class:"border-t border-gray-100"},null,-1)),t("form",{onSubmit:e[2]||(e[2]=N((...i)=>r.logout&&r.logout(...i),["prevent"]))},[y(S,{as:"button"},{default:m(()=>e[9]||(e[9]=[x(" Log Out ")])),_:1})],32)]),_:1})])],4)):c("",!0),t("main",ue,[v(s.$slots,"default")]),t("footer",pe,[t("div",me,[e[12]||(e[12]=t("hr",{class:"mb-4 border-b-1 border-blueGray-200"},null,-1)),t("div",de,[t("div",ce,[t("div",fe," © "+p(new Date().getFullYear()),1)]),t("div",ge,[t("ul",be,[t("li",he,p(s.$page.props.application_name),1)])])])])])],2)])}const Se=O(V,[["render",we]]);export{Se as A};
