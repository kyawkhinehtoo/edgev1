import{A as E}from"./AppLayout-Dk4wOMIn.js";import{P as L}from"./Pagination-MUHNSnsW.js";import{_ as A}from"./PrimaryButton-CfKDG-Mv.js";import{_ as M}from"./DialogModal-DcBCRWlh.js";import{_ as I}from"./SecondaryButton-D1HMiT4j.js";import{_ as J}from"./DangerButton-D0ciigTi.js";import{_ as q,a as B}from"./TextInput-BfTbiG1D.js";import{_ as F}from"./ConfirmationModal-BZHjOuqp.js";import{T as D,r as k,q as f,d as p,b as u,w as c,F as O,N as j,o as m,a as e,s as b,v as _,M as z,g as U,e as g,t as a,f as x}from"./app-XjIQHOyI.js";import{M as K}from"./vue3-multiselect.umd.min-DTqb1Lss.js";import{_ as R}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-XC1d7OME.js";const G={name:"pop",components:{AppLayout:E,Pagination:L,JetButton:A,JetDialogModal:M,JetSecondaryButton:I,JetDangerButton:J,JetConfirmationModal:F,JetInput:q,JetInputError:B,useForm:D,Multiselect:K},props:{pops:Object,pop_devices:Object,errors:Object,partners:Array,townships:Array},setup(i){let t=k(!1),d=k(""),s=k(!1);const y=k([{id:"",device_name:"",qty:1,remark:"",partner_id:null,partner:[],townships:[]}]),n=D({id:null,site_name:null,site_description:null,site_location:null,devices:null,partner_id:null,townships:[]});function P(o){n.id=o}function h(){n.id=null,n.site_name=null,n.site_description=null,n.site_location=null,n.devices=null,y.value=[],n.partner_id=null,n.partner=[],n.townships=[]}function w(o){n.id=o.id,n.site_name=o.site_name,n.site_description=o.site_description,n.site_location=o.site_location,n.partner=i.partners.filter(r=>r.id==o.partner_id)[0],n.partner_id=o.partner_id,n.townships=o.townships||[],t.value=!0,s.value=!0,i.pop_devices&&(y.value.shift(),i.pop_devices.forEach(r=>{if(r.pop_id==o.id){var l={id:r.id,device_name:r.device_name,qty:r.qty,remark:r.remark};y.value.push(l)}}))}function C(){h(),t.value=!1,s.value=!1}function S(){s.value?(n._method="PUT",n.devices=y,j.post("/pop/"+n.id,n,{preserveState:!0,onSuccess:o=>{t.value=!1,h(),Toast.fire({icon:"success",title:o.props.flash.message})},onError:o=>{console.log("error ..".errors)}})):(n._method="POST",n.devices=y,n.post("/pop",{preserveState:!0,onSuccess:o=>{t.value=!1,h(),Toast.fire({icon:"success",title:o.props.flash.message})},onError:o=>{console.log("error ..".errors)}}))}function v(){j.get("/pop/",{keyword:d.value},{preserveState:!0})}const V=o=>{const r=/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/,l=o.target.value;r.test(l)&&(console.log(l),n.site_location=l)};function N(){let o=Object({});o.id=n.id,o._method="DELETE",j.post("/pop/"+o.id,o,{preserveScroll:!0,preserveState:!0,onSuccess:r=>{n.id=null,Toast.fire({icon:"success",title:r.props.flash.message})},onError:r=>{console.log("error ..".errors)}})}return{show:t,form:n,editMode:s,search:d,devices:y,onInputLocation:V,cancel:C,deleteNode:N,confirmDelete:P,searchPort:v,save:S,edit:w}}},H={class:"py-2"},Q={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},W={class:"flex justify-between space-x-2 items-end mb-2 px-1 md:px-0"},X={class:"relative flex flex-wrap z-0"},Y={class:"col-1"},Z={key:0,class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},$={class:"min-w-full divide-y divide-gray-200 table-auto"},tt={class:"bg-white divide-y divide-gray-200 max-h-64"},et={class:"px-6 py-3 font-medium"},ot={class:"px-6 py-3 font-medium"},st={class:"px-6 py-3 font-medium"},nt={class:"px-6 py-3 font-medium"},rt={class:"px-6 py-3 font-medium"},lt={class:"px-6 py-3 font-medium"},it={class:"px-6 py-3 font-medium text-right"},at=["onClick"],dt=["onClick"],pt={key:1,class:"w-full block mt-4"},mt={class:"text-xs text-gray-600"},ct={key:2},ut={class:"mb-4 md:col-span-1"},ft={key:0,class:"text-red-500"},gt={class:"mb-4 md:col-span-1"},xt={key:0,class:"text-red-500"},yt={class:"mb-4 md:col-span-1"},bt={key:0,class:"text-red-500"},_t={class:"mb-4 md:col-span-1"},ht={key:0,class:"text-red-500"},wt={class:"mb-4 md:col-span-1"},vt={key:0,class:"text-red-500"},kt={class:"overflow-hidden h-max"},Pt={class:"min-w-full text-center"},Ct={class:"text-sm text-gray-900 font-light w-10 py-4 whitespace-nowrap"},St={class:"text-sm text-gray-900 font-light w-96 py-4 whitespace-nowrap"},Vt=["onUpdate:modelValue"],Nt={class:"text-sm text-gray-900 font-light w-20 py-4 whitespace-nowrap"},Ot=["onUpdate:modelValue"],jt={class:"text-sm text-gray-900 font-light w-28 py-4 whitespace-nowrap"},Dt=["onUpdate:modelValue"],Ut={class:"text-sm text-gray-900 font-light w-28 py-4 whitespace-nowrap"},Tt=["onClick"];function Et(i,t,d,s,y,n){const P=f("pagination"),h=f("app-layout"),w=f("jet-secondary-button"),C=f("jet-danger-button"),S=f("jet-confirmation-modal"),v=f("Multiselect"),V=f("jet-button"),N=f("jet-dialog-modal");return m(),p(O,null,[u(h,null,{header:c(()=>t[14]||(t[14]=[e("h2",{class:"font-semibold text-xl text-white leading-tight"},"POP Setup",-1)])),default:c(()=>[e("div",H,[e("div",Q,[e("div",W,[e("div",X,[t[15]||(t[15]=e("span",{class:"z-10 h-full leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"},[e("i",{class:"fas fa-search"})],-1)),b(e("input",{type:"text",placeholder:"Search here...",class:"border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10",id:"search","onUpdate:modelValue":t[0]||(t[0]=o=>s.search=o),onKeyup:t[1]||(t[1]=z((...o)=>s.searchPort&&s.searchPort(...o),["enter"]))},null,544),[[_,s.search]])]),e("button",{onClick:t[2]||(t[2]=()=>{s.show=!0,s.editMode=!1}),class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},"Create")]),e("div",Y,[d.pops.data?(m(),p("div",Z,[e("table",$,[t[18]||(t[18]=e("thead",{class:"bg-gray-50"},[e("tr",{class:"text-left"},[e("th",{scope:"col",class:"px-4 py-3 text-xs font-medium text-gray-500 uppercase"},"No."),e("th",{scope:"col",class:"px-4 py-3 text-xs font-medium text-gray-500 uppercase"},"Pop Name"),e("th",{scope:"col",class:"px-4 py-3 text-xs font-medium text-gray-500 uppercase"},"Pop Owner"),e("th",{scope:"col",class:"px-4 py-3 text-xs font-medium text-gray-500 uppercase"},"Site Description"),e("th",{scope:"col",class:"px-4 py-3 text-xs font-medium text-gray-500 uppercase"},"Created At"),e("th",{scope:"col",class:"px-4 py-3 text-xs font-medium text-gray-500 uppercase"},"Updated At"),e("th",{scope:"col",class:"px-4 py-3 text-xs font-medium text-gray-500 uppercase"},[e("span",{class:"sr-only"},"Action")])])],-1)),e("tbody",tt,[(m(!0),p(O,null,U(d.pops.data,(o,r)=>{var l;return m(),p("tr",{key:o.id},[e("td",et,a(d.pops.from+r),1),e("td",ot,a(o.site_name),1),e("td",st,a((l=o.partner)==null?void 0:l.name),1),e("td",nt,a(o.site_description),1),e("td",rt,a(o.created_at),1),e("td",lt,a(o.updated_at),1),e("td",it,[e("a",{href:"#",onClick:T=>s.edit(o),class:"text-yellow-600 ml-2"},t[16]||(t[16]=[e("i",{class:"fa fa-pen"},null,-1)]),8,at),e("a",{href:"#",onClick:T=>s.confirmDelete(o),class:"text-red-600 ml-2"},t[17]||(t[17]=[e("i",{class:"fa fa-trash"},null,-1)]),8,dt)])])}),128))])])])):g("",!0),d.pops.total?(m(),p("span",pt,[e("label",mt,a(d.pops.data.length)+" POP List in Current Page. Total Number of POP : "+a(d.pops.total),1)])):g("",!0),d.pops.links?(m(),p("span",ct,[u(P,{class:"mt-6",links:d.pops.links},null,8,["links"])])):g("",!0)])])])]),_:1}),u(S,{show:s.form.id,onClose:t[4]||(t[4]=o=>s.form.id=null)},{title:c(()=>t[19]||(t[19]=[x(" Delete Node")])),content:c(()=>t[20]||(t[20]=[x(" Are you sure you would like to delete this DN ? It might effect to Customer Data ! ")])),footer:c(()=>[u(w,{onClick:t[3]||(t[3]=o=>s.form.id=null)},{default:c(()=>t[21]||(t[21]=[x(" Cancel ")])),_:1}),u(C,{class:"ml-2",onClick:s.deleteNode},{default:c(()=>t[22]||(t[22]=[x(" Delete ")])),_:1},8,["onClick"])]),_:1},8,["show"]),u(N,{show:s.show,onClose:t[13]||(t[13]=o=>s.show=!1)},{title:c(()=>t[23]||(t[23]=[x(" Add New Port ")])),content:c(()=>[e("div",ut,[t[24]||(t[24]=e("label",{for:"partner_id",class:"block text-gray-700 text-sm font-bold mb-2"},"POP Site Owner :",-1)),u(v,{modelValue:s.form.partner,"onUpdate:modelValue":[t[5]||(t[5]=o=>s.form.partner=o),t[6]||(t[6]=o=>s.form.partner_id=o==null?void 0:o.id)],options:d.partners,multiple:!1,"track-by":"id",label:"name",placeholder:"Select Partner",class:"mt-1"},null,8,["modelValue","options"]),i.$page.props.errors.partner_id?(m(),p("div",ft,a(i.$page.props.errors.partner_id),1)):g("",!0)]),e("div",gt,[t[25]||(t[25]=e("label",{for:"township",class:"block text-gray-700 text-sm font-bold mb-2"},"POP Coverage Township :",-1)),u(v,{modelValue:s.form.townships,"onUpdate:modelValue":t[7]||(t[7]=o=>s.form.townships=o),options:d.townships,multiple:!0,"track-by":"id",label:"name",placeholder:"Select Townships",class:"mt-1"},null,8,["modelValue","options"]),i.$page.props.errors.townships?(m(),p("div",xt,a(i.$page.props.errors.townships),1)):g("",!0)]),e("div",yt,[t[26]||(t[26]=e("label",{for:"site_name",class:"block text-gray-700 text-sm font-bold mb-2"},"POP Site Name :",-1)),b(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"site_name",placeholder:"Enter POP Site Name","onUpdate:modelValue":t[8]||(t[8]=o=>s.form.site_name=o)},null,512),[[_,s.form.site_name]]),i.$page.props.errors.site_name?(m(),p("div",bt,a(i.$page.props.errors.site_name),1)):g("",!0)]),e("div",_t,[t[27]||(t[27]=e("label",{for:"description",class:"block text-gray-700 text-sm font-bold mb-2"},"POP Site Description :",-1)),b(e("textarea",{class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"description",placeholder:"Enter Description","onUpdate:modelValue":t[9]||(t[9]=o=>s.form.site_description=o)},null,512),[[_,s.form.site_description]]),i.$page.props.errors.site_description?(m(),p("div",ht,a(i.$page.props.errors.site_description),1)):g("",!0)]),e("div",wt,[t[28]||(t[28]=e("label",{for:"site_location",class:"block text-gray-700 text-sm font-bold mb-2"},"POP Site Location :",-1)),b(e("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"site_location",placeholder:"Enter Location (Lat,Long)","onUpdate:modelValue":t[10]||(t[10]=o=>s.form.site_location=o),onInput:t[11]||(t[11]=(...o)=>s.onInputLocation&&s.onInputLocation(...o))},null,544),[[_,s.form.site_location]]),i.$page.props.errors.site_location?(m(),p("div",vt,a(i.$page.props.errors.site_location),1)):g("",!0)]),e("div",kt,[e("button",{onClick:t[12]||(t[12]=o=>s.devices.push({})),type:"button",class:"btn bg-indigo-600 hover:bg-indigo-700 text-sm shadow-sm rounded-md px-4 py-2 text-white font-semibold"},t[29]||(t[29]=[x("Add "),e("i",{class:"fa fas fa-plus"},null,-1)])),e("table",Pt,[t[30]||(t[30]=e("thead",{class:"border-b bg-gray-50"},[e("tr",null,[e("th",{scope:"col",class:"text-left text-sm font-bold text-gray-900 py-4"},"#"),e("th",{scope:"col",class:"text-left text-sm font-bold text-gray-900 py-4"},"OLT Name"),e("th",{scope:"col",class:"text-left text-sm font-bold text-gray-900 py-4"},"No. of Frame "),e("th",{scope:"col",class:"text-left text-sm font-bold text-gray-900 py-4"},"Remark"),e("th",{scope:"col",class:"text-left text-sm font-bold text-gray-900 py-4"},"#")])],-1)),e("tbody",null,[(m(!0),p(O,null,U(s.devices,(o,r)=>(m(),p("tr",{class:"bg-white border-b",key:r},[e("td",Ct,a(r+1),1),e("td",St,[b(e("input",{type:"text",name:"description",id:"description","onUpdate:modelValue":l=>o.device_name=l,class:"form-control w-full text-sm text-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"},null,8,Vt),[[_,o.device_name]])]),e("td",Nt,[b(e("input",{type:"number",name:"price",id:"price","onUpdate:modelValue":l=>o.qty=l,class:"form-control w-full text-sm text-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none",min:"1"},null,8,Ot),[[_,o.qty]])]),e("td",jt,[b(e("input",{type:"text",name:"remark",id:"remark","onUpdate:modelValue":l=>o.remark=l,class:"form-control w-full text-sm text-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"},null,8,Dt),[[_,o.remark]])]),e("td",Ut,[e("button",{onClick:l=>s.devices.splice(r,1),type:"button",class:"btn bg-yellow-600 hover:bg-yellow-700 text-lg shadow-sm rounded-md px-2 py-0 text-white font-semibold"},"×",8,Tt)])]))),128))])])])]),footer:c(()=>[u(w,{onClick:s.cancel},{default:c(()=>t[31]||(t[31]=[x(" Close ")])),_:1},8,["onClick"]),u(V,{class:"ml-2",onClick:s.save},{default:c(()=>t[32]||(t[32]=[x(" Save ")])),_:1},8,["onClick"])]),_:1},8,["show"])],64)}const Ht=R(G,[["render",Et]]);export{Ht as default};
