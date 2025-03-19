import{A as S}from"./AppLayout-DQMHUDA-.js";import{P as T}from"./Pagination-BDaU9i64.js";import{K as M,r as h,q as _,c as k,w as q,N as g,o as l,a as e,s as m,v,L as C,d as c,g as D,F as N,f as P,h as B,t as u,e as x,x as E}from"./app-DdoqqyeN.js";import{_ as O}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-DupIRA2Y.js";const U={name:"Equiptment",components:{AppLayout:S,Pagination:T},props:{equiptments:Object,errors:Object},setup(d){const t=M({id:null,name:null,detail:null}),n=h("");let s=h(!1),p=h(!1);function a(){t.name=null,t.detail=null}function b(){s.value?(t._method="PUT",t._method="PUT",g.post("/equiptment/"+t.id,t,{onSuccess:i=>{r(),a(),Toast.fire({icon:"success",title:i.props.flash.message})},onError:i=>{r(),console.log("error ..".errors)}})):(t._method="POST",g.post("/equiptment",t,{preserveState:!0,onSuccess:i=>{r(),a(),Toast.fire({icon:"success",title:i.props.flash.message})},onError:i=>{r(),console.log("error ..".errors)}}))}function y(i){t.id=i.id,t.name=i.name,t.detail=i.detail,s.value=!0,f()}function o(i){confirm("Are you sure want to remove?")&&(i._method="DELETE",g.post("/equiptment/"+i.id,i,{preserveState:!0,onSuccess:w=>{r(),a(),Toast.fire({icon:"success",title:w.props.flash.message})},onError:w=>{r(),console.log("error ..".errors)}}),r(),a())}function f(){p.value=!0}const r=()=>{p.value=!1,a(),s.value=!1};return{form:t,submit:b,editMode:s,isOpen:p,openModal:f,closeModal:r,edit:y,deleteRow:o,searchTsp:()=>{console.log("search value is"+n.value),g.get("/equiptment/",{equiptment:n.value},{preserveState:!0})},search:n}}},V={class:"py-12"},j={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},A={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"},L={class:"flex justify-between mb-6"},F={class:"flex items-center flex-1"},K={class:"w-1/3"},R={class:"min-w-full divide-y divide-gray-200"},z={class:"bg-white divide-y divide-gray-200"},I={class:"px-6 py-4 whitespace-nowrap"},G={class:"px-6 py-4 whitespace-nowrap"},H={class:"px-6 py-4"},J={class:"px-6 py-4 whitespace-nowrap"},Q=["onClick"],W=["onClick"],X={key:0,ref:"isOpen",class:"fixed z-10 inset-0 overflow-y-auto ease-out duration-400"},Y={class:"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"},Z={class:"inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full",role:"dialog","aria-modal":"true","aria-labelledby":"modal-headline"},$={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},ee={class:""},te={class:"mb-4"},se={key:0,class:"text-red-500"},oe={class:"mb-4"},ie={key:0,class:"text-red-500"},re={class:"bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"},ne={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},le={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},ae={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},de={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},me={class:"mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"};function ce(d,t,n,s,p,a){const b=_("Pagination"),y=_("app-layout");return l(),k(y,null,{header:q(()=>t[7]||(t[7]=[e("h2",{class:"font-semibold text-xl text-white leading-tight"},"Bundle Device Setup",-1)])),default:q(()=>[e("div",V,[e("div",j,[e("div",A,[e("div",L,[e("div",F,[e("div",K,[m(e("input",{"onUpdate:modelValue":t[0]||(t[0]=o=>s.search=o),type:"text",placeholder:"Search device...",class:"w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500",onKeyup:t[1]||(t[1]=C((...o)=>s.searchTsp&&s.searchTsp(...o),["enter"]))},null,544),[[v,s.search]])])]),e("button",{onClick:t[2]||(t[2]=(...o)=>s.openModal&&s.openModal(...o)),class:"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"}," Add Device ")]),e("table",R,[t[8]||(t[8]=e("thead",null,[e("tr",null,[e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"No."),e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Name"),e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Details"),e("th",{class:"px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"},"Actions")])],-1)),e("tbody",z,[(l(!0),c(N,null,D(n.equiptments.data,(o,f)=>(l(),c("tr",{key:o.id},[e("td",I,u(f+1),1),e("td",G,u(o.name),1),e("td",H,u(o.detail),1),e("td",J,[e("a",{href:"#",onClick:r=>s.edit(o),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"Edit",8,Q),e("a",{href:"#",onClick:r=>s.deleteRow(o),class:"text-red-600 hover:text-red-900"},"Delete",8,W)])]))),128))])]),s.isOpen?(l(),c("div",X,[e("div",Y,[t[11]||(t[11]=e("div",{class:"fixed inset-0 transition-opacity"},[e("div",{class:"absolute inset-0 bg-gray-500 opacity-75"})],-1)),t[12]||(t[12]=e("span",{class:"hidden sm:inline-block sm:align-middle sm:h-screen"},null,-1)),t[13]||(t[13]=P("​ ")),e("div",Z,[e("form",{onSubmit:t[6]||(t[6]=B((...o)=>s.submit&&s.submit(...o),["prevent"]))},[e("div",$,[e("div",ee,[e("div",te,[t[9]||(t[9]=e("label",{for:"name",class:"block text-gray-700 text-sm font-bold mb-2"},"Equiptment Name:",-1)),m(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"name",placeholder:"Enter Equiptment Name","onUpdate:modelValue":t[3]||(t[3]=o=>s.form.name=o)},null,512),[[v,s.form.name]]),d.$page.props.errors.name?(l(),c("div",se,u(d.$page.props.errors.name[0]),1)):x("",!0)]),e("div",oe,[t[10]||(t[10]=e("label",{for:"detail",class:"block text-gray-700 text-sm font-bold mb-2"},"Equiptment Detail:",-1)),m(e("textarea",{class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"detail",placeholder:"Enter Details Info","onUpdate:modelValue":t[4]||(t[4]=o=>s.form.detail=o)},null,512),[[v,s.form.detail]]),d.$page.props.errors.detail?(l(),c("div",ie,u(d.$page.props.errors.detail[0]),1)):x("",!0)])])]),e("div",re,[e("span",ne,[m(e("button",le," Save ",512),[[E,!s.editMode]])]),e("span",ae,[m(e("button",de," Update ",512),[[E,s.editMode]])]),e("span",me,[e("button",{onClick:t[5]||(t[5]=(...o)=>s.closeModal&&s.closeModal(...o)),type:"button",class:"inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"}," Cancel ")])])],32)])])],512)):x("",!0)]),n.equiptments.links?(l(),k(b,{key:0,links:n.equiptments.links,class:"mt-6"},null,8,["links"])):x("",!0)])])]),_:1})}const ye=O(U,[["render",ce]]);export{ye as default};
