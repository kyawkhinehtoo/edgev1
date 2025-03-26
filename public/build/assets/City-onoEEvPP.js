import{A as T}from"./AppLayout-Dk4wOMIn.js";import{P as M}from"./Pagination-MUHNSnsW.js";import{Q as E,r as h,q as _,c as k,w as C,N as g,o as l,a as e,s as c,v as w,M as N,d as m,g as O,F as P,f as V,h as j,t as u,e as y,x as S}from"./app-XjIQHOyI.js";import{_ as A}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-XC1d7OME.js";const U={name:"city",components:{AppLayout:T,Pagination:M},props:{cities:Object,errors:Object},setup(a){const t=E({id:null,name:null,short_code:null}),n=h("");let s=h(!1),p=h(!1);function d(){t.name=null,t.city="Tachileik",t.short_code=null}function x(){s.value?(t._method="PUT",g.post("/city/"+t.id,t,{onSuccess:r=>{i(),d(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{i(),console.log("error ..".errors)}})):(t._method="POST",g.post("/city",t,{preserveState:!0,onSuccess:r=>{i(),d(),Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{i(),console.log("error ..".errors)}}))}function b(r){t.id=r.id,t.name=r.name,t.short_code=r.short_code,s.value=!0,f()}function o(r){confirm("Are you sure want to remove?")&&(r._method="DELETE",g.post("/city/"+r.id,r,{preserveState:!0,onSuccess:v=>{i(),d(),Toast.fire({icon:"success",title:v.props.flash.message})},onError:v=>{i(),console.log("error ..".errors)}}),i(),d())}function f(){p.value=!0,console.log(a.cities.name)}const i=()=>{p.value=!1,d(),s.value=!1};return{form:t,submit:x,editMode:s,isOpen:p,openModal:f,closeModal:i,edit:b,deleteRow:o,searchTsp:()=>{console.log("search value is"+n.value),g.get("/city/",{city:n.value},{preserveState:!0})},search:n}}},B={class:"py-12"},D={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},F={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"},L={class:"flex justify-between mb-6"},K={class:"flex items-center flex-1"},R={class:"w-1/3"},q={class:"min-w-full divide-y divide-gray-200"},z={class:"bg-white divide-y divide-gray-200"},Q={class:"px-6 py-4 whitespace-nowrap"},G={class:"px-6 py-4 whitespace-nowrap"},H={class:"px-6 py-4 whitespace-nowrap"},I={class:"px-6 py-4 whitespace-nowrap"},J=["onClick"],W=["onClick"],X={key:0,ref:"isOpen",class:"fixed z-10 inset-0 overflow-y-auto ease-out duration-400"},Y={class:"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"},Z={class:"inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full",role:"dialog","aria-modal":"true","aria-labelledby":"modal-headline"},$={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},ee={class:""},te={class:"mb-4"},se={key:0,class:"text-red-500"},oe={class:"mb-4"},re={key:0,class:"text-red-500"},ie={class:"bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"},ne={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},le={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},ae={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},de={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},ce={class:"mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"};function me(a,t,n,s,p,d){const x=_("Pagination"),b=_("app-layout");return l(),k(b,null,{header:C(()=>t[7]||(t[7]=[e("h2",{class:"font-semibold text-xl text-white leading-tight"},"City Setup",-1)])),default:C(()=>[e("div",B,[e("div",D,[e("div",F,[e("div",L,[e("div",K,[e("div",R,[c(e("input",{"onUpdate:modelValue":t[0]||(t[0]=o=>s.search=o),type:"text",placeholder:"Search cities...",class:"w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500",onKeyup:t[1]||(t[1]=N((...o)=>s.searchTsp&&s.searchTsp(...o),["enter"]))},null,544),[[w,s.search]])])]),e("button",{onClick:t[2]||(t[2]=(...o)=>s.openModal&&s.openModal(...o)),class:"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"}," Add City ")]),e("table",q,[t[8]||(t[8]=e("thead",null,[e("tr",null,[e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"No."),e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"City"),e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Short Code"),e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Actions")])],-1)),e("tbody",z,[(l(!0),m(P,null,O(n.cities.data,(o,f)=>(l(),m("tr",{key:o.id},[e("td",Q,u(n.cities.from+f),1),e("td",G,u(o.name),1),e("td",H,u(o.short_code),1),e("td",I,[e("a",{href:"#",onClick:i=>s.edit(o),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"Edit",8,J),e("a",{href:"#",onClick:i=>s.deleteRow(o),class:"text-red-600 hover:text-red-900"},"Delete",8,W)])]))),128))])]),s.isOpen?(l(),m("div",X,[e("div",Y,[t[11]||(t[11]=e("div",{class:"fixed inset-0 transition-opacity"},[e("div",{class:"absolute inset-0 bg-gray-500 opacity-75"})],-1)),t[12]||(t[12]=e("span",{class:"hidden sm:inline-block sm:align-middle sm:h-screen"},null,-1)),t[13]||(t[13]=V("​ ")),e("div",Z,[e("form",{onSubmit:t[6]||(t[6]=j((...o)=>s.submit&&s.submit(...o),["prevent"]))},[e("div",$,[e("div",ee,[e("div",te,[t[9]||(t[9]=e("label",{for:"city",class:"block text-gray-700 text-sm font-bold mb-2"},"city:",-1)),c(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"city",placeholder:"Enter city","onUpdate:modelValue":t[3]||(t[3]=o=>s.form.name=o)},null,512),[[w,s.form.name]]),a.$page.props.errors.name?(l(),m("div",se,u(a.$page.props.errors.name[0]),1)):y("",!0)]),e("div",oe,[t[10]||(t[10]=e("label",{for:"short_code",class:"block text-gray-700 text-sm font-bold mb-2"},"Short Code:",-1)),c(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"short_code",placeholder:"Enter Short Code","onUpdate:modelValue":t[4]||(t[4]=o=>s.form.short_code=o)},null,512),[[w,s.form.short_code]]),a.$page.props.errors.short_code?(l(),m("div",re,u(a.$page.props.errors.short_code[0]),1)):y("",!0)])])]),e("div",ie,[e("span",ne,[c(e("button",le," Save ",512),[[S,!s.editMode]])]),e("span",ae,[c(e("button",de," Update ",512),[[S,s.editMode]])]),e("span",ce,[e("button",{onClick:t[5]||(t[5]=(...o)=>s.closeModal&&s.closeModal(...o)),type:"button",class:"inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"}," Cancel ")])])],32)])])],512)):y("",!0)]),n.cities.links?(l(),k(x,{key:0,links:n.cities.links,class:"mt-6"},null,8,["links"])):y("",!0)])])]),_:1})}const be=A(U,[["render",me]]);export{be as default};
