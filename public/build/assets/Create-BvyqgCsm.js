import{A as h}from"./AppLayout-DQMHUDA-.js";import{i as U,T,k as P,r as S,q as m,c as L,w as x,o as s,a as t,n as p,h as j,d,s as i,v as a,t as n,e as l,b as f,F as D,g as N,f as A}from"./app-DdoqqyeN.js";import{_ as B}from"./Checkbox-Drxt8cAp.js";import{_ as M}from"./InputLabel-Cj7F8OT7.js";import{M as F}from"./vue3-multiselect.umd.min-BtFOAPRA.js";import{_ as q}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-DupIRA2Y.js";const E={components:{AppLayout:h,JetCheckbox:B,JetLabel:M,Multiselect:F,Link:U},props:{customerColumns:{type:Array,required:!0},customerStatus:Object},setup(b){const o=T({name:"",contact_person:"",phone:"",email:"",website:"",address:"",description:"",domain:"",logo:"",permissions:[],customer_status:null}),u=P(()=>b.customerColumns.map(g=>({value:g.name,label:g.name.replace("_"," ").toUpperCase()}))),e=()=>{o.post(route("partner.store"),{forceFormData:!0,onSuccess:()=>o.reset()})},y=S("data");return{form:o,submit:e,availablePermissions:u,activeTab:y}}},I={class:"py-4"},J={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},z={class:"flex max-w-72 gap-1"},O={class:"bg-white overflow-hidden shadow-xl sm:rounded-b-lg p-6 rounded-r-lg"},W={key:0},G={class:"grid grid-cols-2 gap-6"},H={key:0,class:"text-red-500 text-sm mt-1"},K={key:0,class:"text-red-500 text-sm mt-1"},Q={key:0,class:"text-red-500 text-xs mt-1"},R={key:0,class:"text-red-500 text-xs mt-1"},X={key:0,class:"text-red-500 text-xs mt-1"},Y={key:0,class:"text-red-500 text-xs mt-1"},Z={key:0,class:"text-red-500 text-xs mt-1"},$={key:0,class:"text-red-500 text-xs mt-1"},oo={key:0,class:"text-red-500 text-xs mt-1"},eo={key:1},to={class:"mb-4"},ro={key:0,class:"mt-1 flex rounded-md shadow-sm"},so={class:"mb-4"},lo={class:"grid grid-cols-1 md:grid-cols-3 gap-4"},no={class:"mt-6 flex items-center justify-end"};function io(b,o,u,e,y,g){const v=m("multiselect"),k=m("jet-checkbox"),w=m("jet-label"),_=m("Link"),C=m("app-layout");return s(),L(C,null,{header:x(()=>o[14]||(o[14]=[t("h2",{class:"font-semibold text-xl text-white leading-tight"},"Create Partner",-1)])),default:x(()=>[t("div",I,[t("div",J,[t("nav",z,[t("button",{type:"button",onClick:o[0]||(o[0]=r=>e.activeTab="data"),class:p([e.activeTab==="data"?"border-t-2 border-indigo-500 text-indigo-600":"border-b border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700","bg-white sm:rounded-t-lg w-1/2 py-4 px-1 text-center border-0 border-t-2 font-medium text-sm focus:ring-0 focus:outline-none"])}," Partner Information ",2),t("button",{type:"button",onClick:o[1]||(o[1]=r=>e.activeTab="permissions"),class:p([e.activeTab==="permissions"?"border-t-2 border-indigo-500 text-indigo-600":"border-b border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700","bg-white sm:rounded-t-lg w-1/2 py-4 px-1 text-center border-0 border-t-2 font-medium text-sm focus:ring-0 focus:outline-none"])}," Permissions ",2)]),t("div",O,[t("form",{onSubmit:o[13]||(o[13]=j((...r)=>e.submit&&e.submit(...r),["prevent"]))},[e.activeTab==="data"?(s(),d("div",W,[t("div",G,[t("div",null,[o[15]||(o[15]=t("label",{for:"domain",class:"block text-sm font-medium text-gray-700"},"Domain",-1)),i(t("input",{type:"text",id:"domain","onUpdate:modelValue":o[2]||(o[2]=r=>e.form.domain=r),class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.domain]]),e.form.errors.domain?(s(),d("div",H,n(e.form.errors.domain),1)):l("",!0)]),t("div",null,[o[16]||(o[16]=t("label",{for:"logo",class:"block text-sm font-medium text-gray-700"},"Logo",-1)),t("input",{type:"file",id:"logo",onInput:o[3]||(o[3]=r=>e.form.logo=r.target.files[0]),class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,32),e.form.errors.logo?(s(),d("div",K,n(e.form.errors.logo),1)):l("",!0)]),t("div",null,[o[17]||(o[17]=t("label",{class:"block text-sm font-medium text-gray-700"},"Name",-1)),i(t("input",{type:"text","onUpdate:modelValue":o[4]||(o[4]=r=>e.form.name=r),class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.name]]),e.form.errors.name?(s(),d("div",Q,n(e.form.errors.name),1)):l("",!0)]),t("div",null,[o[18]||(o[18]=t("label",{class:"block text-sm font-medium text-gray-700"},"Contact Person",-1)),i(t("input",{type:"text","onUpdate:modelValue":o[5]||(o[5]=r=>e.form.contact_person=r),class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.contact_person]]),e.form.errors.contact_person?(s(),d("div",R,n(e.form.errors.contact_person),1)):l("",!0)]),t("div",null,[o[19]||(o[19]=t("label",{class:"block text-sm font-medium text-gray-700"},"Phone",-1)),i(t("input",{type:"text","onUpdate:modelValue":o[6]||(o[6]=r=>e.form.phone=r),class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.phone]]),e.form.errors.phone?(s(),d("div",X,n(e.form.errors.phone),1)):l("",!0)]),t("div",null,[o[20]||(o[20]=t("label",{class:"block text-sm font-medium text-gray-700"},"Email",-1)),i(t("input",{type:"email","onUpdate:modelValue":o[7]||(o[7]=r=>e.form.email=r),class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.email]]),e.form.errors.email?(s(),d("div",Y,n(e.form.errors.email),1)):l("",!0)]),t("div",null,[o[21]||(o[21]=t("label",{class:"block text-sm font-medium text-gray-700"},"Website",-1)),i(t("input",{type:"url","onUpdate:modelValue":o[8]||(o[8]=r=>e.form.website=r),class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.website]]),e.form.errors.website?(s(),d("div",Z,n(e.form.errors.website),1)):l("",!0)]),t("div",null,[o[22]||(o[22]=t("label",{class:"block text-sm font-medium text-gray-700"},"Address",-1)),i(t("textarea",{"onUpdate:modelValue":o[9]||(o[9]=r=>e.form.address=r),rows:"3",class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.address]]),e.form.errors.address?(s(),d("div",$,n(e.form.errors.address),1)):l("",!0)]),t("div",null,[o[23]||(o[23]=t("label",{class:"block text-sm font-medium text-gray-700"},"Description",-1)),i(t("textarea",{"onUpdate:modelValue":o[10]||(o[10]=r=>e.form.description=r),rows:"3",class:"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"},null,512),[[a,e.form.description]]),e.form.errors.description?(s(),d("div",oo,n(e.form.errors.description),1)):l("",!0)])])])):l("",!0),e.activeTab==="permissions"?(s(),d("span",eo,[t("div",to,[o[24]||(o[24]=t("h4",{class:"text-sm font-medium text-gray-500 dark:text-gray-400 mb-4"},"Customer Status",-1)),u.customerStatus.length!==0?(s(),d("div",ro,[f(v,{"deselect-label":"Selected already",options:u.customerStatus,"track-by":"id",label:"name",modelValue:e.form.customer_status,"onUpdate:modelValue":o[11]||(o[11]=r=>e.form.customer_status=r),"allow-empty":!0,multiple:!0,taggable:!1},null,8,["options","modelValue"])])):l("",!0)]),t("div",so,[o[25]||(o[25]=t("h4",{class:"text-sm font-medium text-gray-500 dark:text-gray-400 mb-4"},"Customer Permission",-1)),t("div",lo,[(s(!0),d(D,null,N(e.availablePermissions,(r,c)=>(s(),d("div",{key:c,class:"flex items-center"},[f(k,{id:`permission_${c}`,value:r.value,checked:e.form.permissions,"onUpdate:checked":o[12]||(o[12]=V=>e.form.permissions=V)},null,8,["id","value","checked"]),f(w,{for:`permission_${c}`,value:r.label,class:"ml-2"},null,8,["for","value"])]))),128))])])])):l("",!0),t("div",no,[f(_,{href:b.route("partner.index"),class:"bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded mr-2"},{default:x(()=>o[26]||(o[26]=[A(" Cancel ")])),_:1},8,["href"]),o[27]||(o[27]=t("button",{type:"submit",class:"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"}," Create Partner ",-1))])],32)])])])]),_:1})}const xo=q(E,[["render",io]]);export{xo as default};
