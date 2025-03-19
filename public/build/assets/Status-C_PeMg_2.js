import{A as M}from"./AppLayout-DQMHUDA-.js";import{P as E}from"./Pagination-BDaU9i64.js";import{K as V,r as v,q as k,c as N,w as S,N as x,o as n,a as e,s as i,v as h,L as U,d,F as P,g as j,f as w,h as D,t as m,e as u,A as O,B as C,x as T,b as A,n as z}from"./app-DdoqqyeN.js";import{_ as B}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-DupIRA2Y.js";const L={name:"SalePerson",components:{AppLayout:M,Pagination:E},props:{status:Object,errors:Object},setup(c){const t=V({id:null,name:null,color:null,start_date:null,end_date:null,type:null}),l=v("");let s=v(!1),f=v(!1);function p(){t.name=null,t.color=null,t.start_date=null,t.end_date=null,t.type=null}function b(){s.value?(t._method="PUT",t._method="PUT",x.post("/status/"+t.id,t,{onSuccess:r=>{a(),p(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{a(),console.log("error ..".errors)}})):(t._method="POST",x.post("/status",t,{preserveState:!0,onSuccess:r=>{a(),p(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{a(),console.log("error ..".errors)}}))}function y(r){t.id=r.id,t.name=r.name,t.color=r.color,t.start_date=!!r.start_date,t.end_date=!!r.end_date,t.type=r.type,s.value=!0,g()}function o(r){confirm("Are you sure want to remove?")&&(r._method="DELETE",x.post("/status/"+r.id,r,{preserveState:!0,onSuccess:_=>{a(),p(),Toast.fire({icon:"success",title:_.props.flash.message})},onError:_=>{a(),console.log("error ..".errors)}}),a(),p())}function g(){f.value=!0}const a=()=>{f.value=!1,p(),s.value=!1};return{form:t,submit:b,editMode:s,isOpen:f,openModal:g,closeModal:a,edit:y,deleteRow:o,searchTsp:()=>{console.log("search value is"+l.value),x.get("/status/",{status:l.value},{preserveState:!0})},search:l}}},F={class:"py-2"},K={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},q={class:"flex justify-between space-x-2 items-end mb-2 px-1 md:px-0"},R={class:"relative flex flex-wrap z-0"},G={key:0,class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},H={class:"min-w-full divide-y divide-gray-200"},I={class:"bg-white divide-y divide-gray-200"},J={class:"px-6 py-3 whitespace-nowrap"},Q={class:"px-6 py-3 whitespace-nowrap"},W={class:"px-6 py-3 whitespace-nowrap"},X={class:"px-6 py-3 whitespace-nowrap capitalize"},Y={class:"px-6 py-3 whitespace-nowrap text-right text-sm font-medium"},Z=["onClick"],$=["onClick"],ee={key:0,ref:"isOpen",class:"fixed z-10 inset-0 overflow-y-auto ease-out duration-400"},te={class:"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"},se={class:"inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full",role:"dialog","aria-modal":"true","aria-labelledby":"modal-headline"},oe={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},re={class:""},ne={class:"mb-4"},le={key:0,class:"text-red-500"},ae={class:"mb-4"},ie={key:0,class:"text-red-500"},de={class:"mb-4"},ce={class:"mt-1 flex rounded-md"},me={key:0,class:"text-red-500"},pe={class:"mb-4 flex justify-between"},ue={class:"flex"},fe={class:"flex"},ge={class:"bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"},xe={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},be={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},ye={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},ve={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},he={class:"mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"},we={key:1};function _e(c,t,l,s,f,p){const b=k("pagination"),y=k("app-layout");return n(),N(y,null,{header:S(()=>t[10]||(t[10]=[e("h2",{class:"font-semibold text-xl text-white leading-tight"}," Status Setup ",-1)])),default:S(()=>[e("div",F,[e("div",K,[e("div",q,[e("div",R,[t[11]||(t[11]=e("span",{class:"z-10 h-full leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"},[e("i",{class:"fas fa-search"})],-1)),i(e("input",{type:"text",placeholder:"Search here...",class:"border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10",id:"search","onUpdate:modelValue":t[0]||(t[0]=o=>s.search=o),onKeyup:t[1]||(t[1]=U((...o)=>s.searchTsp&&s.searchTsp(...o),["enter"]))},null,544),[[h,s.search]])]),e("button",{onClick:t[2]||(t[2]=(...o)=>s.openModal&&s.openModal(...o)),class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"}," Create")]),l.status.data?(n(),d("div",G,[e("table",H,[t[13]||(t[13]=e("thead",{class:"bg-gray-50"},[e("tr",null,[e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," No. "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Name "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Color "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Type "),e("th",{scope:"col",class:"relative px-6 py-3"},[e("span",{class:"sr-only"},"Action")])])],-1)),e("tbody",I,[(n(!0),d(P,null,j(l.status.data,o=>(n(),d("tr",{key:o.id},[e("td",J,m(o.id),1),e("td",Q,m(o.name),1),e("td",W,[e("span",{class:z("text-sm fas fa-circle text-"+o.color)},null,2),w(" "+m(o.color),1)]),e("td",X,m(o.type),1),e("td",Y,[e("a",{href:"#",onClick:g=>s.edit(o),class:"text-indigo-600 hover:text-indigo-900"},"Edit",8,Z),t[12]||(t[12]=w(" | ")),e("a",{href:"#",onClick:g=>s.deleteRow(o),class:"text-red-600 hover:text-red-900"},"Delete",8,$)])]))),128))])]),s.isOpen?(n(),d("div",ee,[e("div",te,[t[20]||(t[20]=e("div",{class:"fixed inset-0 transition-opacity"},[e("div",{class:"absolute inset-0 bg-gray-500 opacity-75"})],-1)),t[21]||(t[21]=e("span",{class:"hidden sm:inline-block sm:align-middle sm:h-screen"},null,-1)),t[22]||(t[22]=w("​ ")),e("div",se,[e("form",{onSubmit:t[9]||(t[9]=D((...o)=>s.submit&&s.submit(...o),["prevent"]))},[e("div",oe,[e("div",re,[e("div",ne,[t[14]||(t[14]=e("label",{for:"name",class:"block text-gray-700 text-sm font-bold mb-2"},"Status :",-1)),i(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"name",placeholder:"Enter Status","onUpdate:modelValue":t[3]||(t[3]=o=>s.form.name=o)},null,512),[[h,s.form.name]]),c.$page.props.errors.name?(n(),d("div",le,m(c.$page.props.errors.name[0]),1)):u("",!0)]),e("div",ae,[t[15]||(t[15]=e("label",{for:"color",class:"block text-gray-700 text-sm font-bold mb-2"},"Color :",-1)),i(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"color",placeholder:"e.g: red, blue","onUpdate:modelValue":t[4]||(t[4]=o=>s.form.color=o)},null,512),[[h,s.form.color]]),c.$page.props.errors.color?(n(),d("div",ie,m(c.$page.props.errors.color[0]),1)):u("",!0)]),e("div",de,[t[17]||(t[17]=e("label",{for:"type",class:"block text-gray-700 text-sm font-bold mb-2"},"Type :",-1)),e("div",ce,[i(e("select",{name:"type",id:"type","onUpdate:modelValue":t[5]||(t[5]=o=>s.form.type=o),class:"block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm",required:""},t[16]||(t[16]=[e("option",{value:"",selected:""},"Please Choose Type",-1),e("option",{value:"new"},"New Order",-1),e("option",{value:"active"},"Active",-1),e("option",{value:"suspense"},"Suspended",-1),e("option",{value:"terminate"},"Terminated",-1),e("option",{value:"cancel"},"Cancel",-1),e("option",{value:"pending"},"Pending",-1),e("option",{value:"disabled"},"Disabled",-1)]),512),[[O,s.form.type]])]),c.$page.props.errors.type?(n(),d("div",me,m(c.$page.props.errors.type[0]),1)):u("",!0)]),e("div",pe,[e("div",ue,[t[18]||(t[18]=e("label",{for:"start_date",class:"text-gray-700 text-sm font-bold mb-2 inline-flex"},"Start Date :",-1)),i(e("input",{type:"checkbox",class:"inline-flex ml-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md",id:"start_date","onUpdate:modelValue":t[6]||(t[6]=o=>s.form.start_date=o)},null,512),[[C,s.form.start_date]])]),e("div",fe,[t[19]||(t[19]=e("label",{for:"end_date",class:"ml-2 text-gray-700 text-sm font-bold mb-2 inline-flex"},"End Date :",-1)),i(e("input",{type:"checkbox",class:"inline-flex ml-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md",id:"end_date","onUpdate:modelValue":t[7]||(t[7]=o=>s.form.end_date=o)},null,512),[[C,s.form.end_date]])])])])]),e("div",ge,[e("span",xe,[i(e("button",be," Save ",512),[[T,!s.editMode]])]),e("span",ye,[i(e("button",ve," Update ",512),[[T,s.editMode]])]),e("span",he,[e("button",{onClick:t[8]||(t[8]=(...o)=>s.closeModal&&s.closeModal(...o)),type:"button",class:"inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"}," Cancel ")])])],32)])])],512)):u("",!0)])):u("",!0),l.status.links?(n(),d("span",we,[A(b,{class:"mt-6",links:l.status.links},null,8,["links"])])):u("",!0)])])]),_:1})}const Ve=B(L,[["render",_e]]);export{Ve as default};
