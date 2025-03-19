import{A as T}from"./AppLayout-DQMHUDA-.js";import{P}from"./Pagination-BDaU9i64.js";import{K as M,r as w,q as v,c as S,w as _,N as g,o as i,a as e,s as c,v as y,L as E,d as a,F as R,g as V,f as k,h as N,t as l,e as p,x as C,b as U}from"./app-DdoqqyeN.js";import{_ as j}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-DupIRA2Y.js";const A={name:"Township",components:{AppLayout:T,Pagination:P},props:{Payments:Object,errors:Object},setup(n){const t=M({id:null,remark:null}),u=w("");let s=w(!1),f=w(!1);function m(){t.remark=null}function h(){s.value?(t._method="PUT",t._method="PUT",g.post("/PaymentRecord/"+t.id,t,{onSuccess:r=>{d(),m(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{d(),console.log("error ..".errors)}})):(t._method="POST",g.post("/PaymentRecord",t,{preserveState:!0,onSuccess:r=>{d(),m(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{d(),console.log("error ..".errors)}}))}function b(r){t.id=r.id,t.remark=r.remark,s.value=!0,x()}function o(r){confirm("Are you sure want to remove?")&&(r._method="DELETE",g.post("/PaymentRecord/"+r.id,r),d(),m())}function x(){f.value=!0}const d=()=>{f.value=!1,m(),s.value=!1};return{form:t,submit:h,editMode:s,isOpen:f,openModal:x,closeModal:d,edit:b,deleteRow:o,searchTsp:()=>{console.log("search value is"+u.value),g.get("/PaymentRecord/",{township:u.value},{preserveState:!0})},search:u}}},O={class:"py-2"},B={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},D={class:"flex justify-between space-x-2 items-end mb-2 px-1 md:px-0"},L={class:"relative flex flex-wrap"},F={key:0,class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},K={class:"min-w-full divide-y divide-gray-200"},z={class:"bg-white divide-y divide-gray-200"},I={class:"px-6 py-3 whitespace-nowrap"},q={class:"px-6 py-3 whitespace-nowrap"},Y={class:"px-6 py-3 whitespace-nowrap"},G={class:"px-6 py-3 whitespace-nowrap"},H={class:"px-6 py-3 whitespace-nowrap"},J={class:"px-6 py-3 whitespace-nowrap text-right text-sm font-medium"},Q=["onClick"],W=["onClick"],X={key:0,ref:"isOpen",class:"fixed z-10 inset-0 overflow-y-auto ease-out duration-400"},Z={class:"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"},$={class:"inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full",role:"dialog","aria-modal":"true","aria-labelledby":"modal-headline"},ee={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},te={class:""},se={class:"mb-4"},oe={key:0,class:"text-red-500"},re={class:"mb-4"},ne={key:0,class:"text-red-500"},ie={class:"mb-4"},ae={key:0,class:"text-red-500"},le={class:"bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"},de={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},ce={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},pe={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},me={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},ue={class:"mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"},fe={key:1};function xe(n,t,u,s,f,m){const h=v("pagination"),b=v("app-layout");return i(),S(b,null,{header:_(()=>t[8]||(t[8]=[e("h2",{class:"font-semibold text-xl text-white leading-tight"}," Payment Record View ",-1)])),default:_(()=>[e("div",O,[e("div",B,[e("div",D,[e("div",L,[t[9]||(t[9]=e("span",{class:"z-10 h-full leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"},[e("i",{class:"fas fa-search"})],-1)),c(e("input",{type:"text",placeholder:"Search here...",class:"border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10",id:"search","onUpdate:modelValue":t[0]||(t[0]=o=>s.search=o),onKeyup:t[1]||(t[1]=E((...o)=>s.searchTsp&&s.searchTsp(...o),["enter"]))},null,544),[[y,s.search]])]),e("button",{onClick:t[2]||(t[2]=(...o)=>s.openModal&&s.openModal(...o)),class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"}," Create")]),n.PaymentRecord.data?(i(),a("div",F,[e("table",K,[t[11]||(t[11]=e("thead",{class:"bg-gray-50"},[e("tr",null,[e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," No. "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Customer ID"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Phone "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Month "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Year "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Issue Amount"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Received Amount"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Bill Number"),e("th",{scope:"col",class:"relative px-6 py-3"},[e("span",{class:"sr-only"},"Action")])])],-1)),e("tbody",z,[(i(!0),a(R,null,V(n.townships.data,o=>(i(),a("tr",{key:o.id},[e("td",I,l(o.id),1),e("td",q,[e("a",null,l(o.ftth_id),1)]),e("td",Y,l(o.phone),1),e("td",G,l(o.name),1),e("td",H,l(o.short_code),1),e("td",J,[e("a",{href:"#",onClick:x=>s.edit(o),class:"text-indigo-600 hover:text-indigo-900"},"Edit",8,Q),t[10]||(t[10]=k(" | ")),e("a",{href:"#",onClick:x=>s.deleteRow(o),class:"text-red-600 hover:text-red-900"},"Delete",8,W)])]))),128))])]),s.isOpen?(i(),a("div",X,[e("div",Z,[t[15]||(t[15]=e("div",{class:"fixed inset-0 transition-opacity"},[e("div",{class:"absolute inset-0 bg-gray-500 opacity-75"})],-1)),t[16]||(t[16]=e("span",{class:"hidden sm:inline-block sm:align-middle sm:h-screen"},null,-1)),t[17]||(t[17]=k("​ ")),e("div",$,[e("form",{onSubmit:t[7]||(t[7]=N((...o)=>s.submit&&s.submit(...o),["prevent"]))},[e("div",ee,[e("div",te,[e("div",se,[t[12]||(t[12]=e("label",{for:"city",class:"block text-gray-700 text-sm font-bold mb-2"},"City:",-1)),c(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"city",placeholder:"Enter City","onUpdate:modelValue":t[3]||(t[3]=o=>s.form.city=o)},null,512),[[y,s.form.city]]),n.$page.props.errors.city?(i(),a("div",oe,l(n.$page.props.errors.city[0]),1)):p("",!0)]),e("div",re,[t[13]||(t[13]=e("label",{for:"township",class:"block text-gray-700 text-sm font-bold mb-2"},"Township:",-1)),c(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"township",placeholder:"Enter Township","onUpdate:modelValue":t[4]||(t[4]=o=>s.form.name=o)},null,512),[[y,s.form.name]]),n.$page.props.errors.name?(i(),a("div",ne,l(n.$page.props.errors.name[0]),1)):p("",!0)]),e("div",ie,[t[14]||(t[14]=e("label",{for:"short_code",class:"block text-gray-700 text-sm font-bold mb-2"},"Short Code:",-1)),c(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"short_code",placeholder:"Enter Short Code","onUpdate:modelValue":t[5]||(t[5]=o=>s.form.short_code=o)},null,512),[[y,s.form.short_code]]),n.$page.props.errors.short_code?(i(),a("div",ae,l(n.$page.props.errors.short_code[0]),1)):p("",!0)])])]),e("div",le,[e("span",de,[c(e("button",ce," Save ",512),[[C,!s.editMode]])]),e("span",pe,[c(e("button",me," Update ",512),[[C,s.editMode]])]),e("span",ue,[e("button",{onClick:t[6]||(t[6]=(...o)=>s.closeModal&&s.closeModal(...o)),type:"button",class:"inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"}," Cancel ")])])],32)])])],512)):p("",!0)])):p("",!0),n.townships.links?(i(),a("span",fe,[U(h,{class:"mt-6",links:n.townships.links},null,8,["links"])])):p("",!0)])])]),_:1})}const _e=j(A,[["render",xe]]);export{_e as default};
