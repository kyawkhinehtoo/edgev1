import{A as M}from"./AppLayout-DQMHUDA-.js";import{P as V}from"./Pagination-BDaU9i64.js";import{K as E,r as _,q as v,c as N,w as S,N as b,o as l,a as t,s as f,v as k,L as O,d as a,g as P,F as U,f as j,h as A,b as T,t as d,e as p,x as C}from"./app-DdoqqyeN.js";import{M as B}from"./vue3-multiselect.umd.min-BtFOAPRA.js";import{_ as D}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-DupIRA2Y.js";const L={name:"Township",components:{AppLayout:M,Multiselect:B,Pagination:V},props:{townships:Object,cities:Object,partners:Object,errors:Object},setup(n){const e=E({id:null,name:null,city:null,city_id:null,partner:null,partner_id:null,short_code:null}),i=_("");let o=_(!1),g=_(!1);function m(){e.name=null,e.city_id=null,e.city=null,e.partner=null,e.partner_id=null,e.short_code=null}function y(){o.value?(e._method="PUT",e._method="PUT",b.post("/township/"+e.id,e,{onSuccess:r=>{c(),m(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{console.log("error ..".errors)}})):(e._method="POST",b.post("/township",e,{preserveState:!0,onSuccess:r=>{c(),m(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{console.log("error ..".errors)}}))}function h(r){e.id=r.id,e.name=r.name,e.city=n.cities.find(u=>u.id===r.city_id),e.city_id=r.city_id,e.partner=n.partners.find(u=>u.id===r.partner_id),e.partner_id=r.partner_id,e.short_code=r.short_code,o.value=!0,s()}function x(r){confirm("Are you sure want to remove?")&&(r._method="DELETE",b.post("/township/"+r.id,r,{preserveState:!0,onSuccess:u=>{c(),m(),Toast.fire({icon:"success",title:u.props.flash.message})},onError:u=>{c(),console.log("error ..".errors)}}),c(),m())}function s(){g.value=!0}const c=()=>{g.value=!1,m(),o.value=!1};return{form:e,submit:y,editMode:o,isOpen:g,openModal:s,closeModal:c,edit:h,deleteRow:x,searchTsp:()=>{console.log("search value is"+i.value),b.get("/township/",{township:i.value},{preserveState:!0})},search:i}}},F={class:"py-12"},K={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},R={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"},q={class:"flex justify-between mb-6"},z={class:"flex items-center flex-1"},G={class:"w-1/3"},H={class:"min-w-full divide-y divide-gray-200"},I={class:"bg-white divide-y divide-gray-200"},J={class:"px-6 py-4 whitespace-nowrap"},Q={class:"px-6 py-4 whitespace-nowrap"},W={class:"px-6 py-4 whitespace-nowrap"},X={class:"px-6 py-4 whitespace-nowrap"},Y={class:"px-6 py-4 whitespace-nowrap"},Z={class:"px-6 py-4 whitespace-nowrap"},$=["onClick"],ee=["onClick"],te={key:0,ref:"isOpen",class:"fixed z-10 inset-0 overflow-y-auto ease-out duration-400"},se={class:"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"},oe={class:"inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full",role:"dialog","aria-modal":"true","aria-labelledby":"modal-headline"},re={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},ie={class:""},ne={key:0,class:"mb-4"},le={key:0,class:"text-red-500"},ae={key:1,class:"mb-4"},de={key:0,class:"text-red-500"},ce={class:"mb-4"},pe={key:0,class:"text-red-500"},me={class:"mb-4"},ue={key:0,class:"text-red-500"},fe={class:"bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"},ge={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},ye={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},be={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},he={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},xe={class:"mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"},we={key:0};function _e(n,e,i,o,g,m){const y=v("multiselect"),h=v("pagination"),x=v("app-layout");return l(),N(x,null,{header:S(()=>e[11]||(e[11]=[t("h2",{class:"font-semibold text-xl text-white leading-tight"},"Township Setup",-1)])),default:S(()=>[t("div",F,[t("div",K,[t("div",R,[t("div",q,[t("div",z,[t("div",G,[f(t("input",{"onUpdate:modelValue":e[0]||(e[0]=s=>o.search=s),type:"text",placeholder:"Search townships...",class:"w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500",onKeyup:e[1]||(e[1]=O((...s)=>o.searchTsp&&o.searchTsp(...s),["enter"]))},null,544),[[k,o.search]])])]),t("button",{onClick:e[2]||(e[2]=(...s)=>o.openModal&&o.openModal(...s)),class:"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"}," Add Township ")]),t("table",H,[e[12]||(e[12]=t("thead",null,[t("tr",null,[t("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"No."),t("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"City"),t("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Township"),t("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Partner"),t("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Short Code"),t("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Actions")])],-1)),t("tbody",I,[(l(!0),a(U,null,P(i.townships.data,(s,c)=>{var w;return l(),a("tr",{key:s.id},[t("td",J,d(i.townships.from+c),1),t("td",Q,d(s.city.name),1),t("td",W,d(s.name),1),t("td",X,d(((w=s.partner)==null?void 0:w.name)||"No Partner"),1),t("td",Y,d(s.short_code),1),t("td",Z,[t("a",{href:"#",onClick:r=>o.edit(s),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"Edit",8,$),t("a",{href:"#",onClick:r=>o.deleteRow(s),class:"text-red-600 hover:text-red-900"},"Delete",8,ee)])])}),128))])]),o.isOpen?(l(),a("div",te,[t("div",se,[e[17]||(e[17]=t("div",{class:"fixed inset-0 transition-opacity"},[t("div",{class:"absolute inset-0 bg-gray-500 opacity-75"})],-1)),e[18]||(e[18]=t("span",{class:"hidden sm:inline-block sm:align-middle sm:h-screen"},null,-1)),e[19]||(e[19]=j("​ ")),t("div",oe,[t("form",{onSubmit:e[10]||(e[10]=A((...s)=>o.submit&&o.submit(...s),["prevent"]))},[t("div",re,[t("div",ie,[i.cities.length!==0?(l(),a("div",ne,[e[13]||(e[13]=t("label",{for:"city",class:"block text-gray-700 text-sm font-bold mb-2"},"City:",-1)),T(y,{"deselect-label":"Selected already",options:i.cities,"track-by":"id",label:"name",modelValue:o.form.city,"onUpdate:modelValue":[e[3]||(e[3]=s=>o.form.city=s),e[4]||(e[4]=s=>o.form.city_id=s==null?void 0:s.id)],"allow-empty":!0},null,8,["options","modelValue"]),n.$page.props.errors.city_id?(l(),a("div",le,d(n.$page.props.errors.city_id),1)):p("",!0)])):p("",!0),i.partners.length!==0?(l(),a("div",ae,[e[14]||(e[14]=t("label",{for:"partner",class:"block text-gray-700 text-sm font-bold mb-2"},"Partner:",-1)),T(y,{"deselect-label":"Selected already",options:i.partners,"track-by":"id",label:"name",modelValue:o.form.partner,"onUpdate:modelValue":[e[5]||(e[5]=s=>o.form.partner=s),e[6]||(e[6]=s=>o.form.partner_id=s==null?void 0:s.id)],"allow-empty":!0},null,8,["options","modelValue"]),n.$page.props.errors.partner_id?(l(),a("div",de,d(n.$page.props.errors.partner_id),1)):p("",!0)])):p("",!0),t("div",ce,[e[15]||(e[15]=t("label",{for:"township",class:"block text-gray-700 text-sm font-bold mb-2"},"Township:",-1)),f(t("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"township",placeholder:"Enter Township","onUpdate:modelValue":e[7]||(e[7]=s=>o.form.name=s)},null,512),[[k,o.form.name]]),n.$page.props.errors.name?(l(),a("div",pe,d(n.$page.props.errors.name),1)):p("",!0)]),t("div",me,[e[16]||(e[16]=t("label",{for:"short_code",class:"block text-gray-700 text-sm font-bold mb-2"},"Short Code:",-1)),f(t("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"short_code",placeholder:"Enter Short Code","onUpdate:modelValue":e[8]||(e[8]=s=>o.form.short_code=s)},null,512),[[k,o.form.short_code]]),n.$page.props.errors.short_code?(l(),a("div",ue,d(n.$page.props.errors.short_code),1)):p("",!0)])])]),t("div",fe,[t("span",ge,[f(t("button",ye," Save ",512),[[C,!o.editMode]])]),t("span",be,[f(t("button",he," Update ",512),[[C,o.editMode]])]),t("span",xe,[t("button",{onClick:e[9]||(e[9]=(...s)=>o.closeModal&&o.closeModal(...s)),type:"button",class:"inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"}," Cancel ")])])],32)])])],512)):p("",!0)]),i.townships.links?(l(),a("span",we,[T(h,{class:"mt-6",links:i.townships.links},null,8,["links"])])):p("",!0)])])]),_:1})}const Ve=D(L,[["render",_e]]);export{Ve as default};
