import{E as m,d as f,a as t,h as p,s as d,v as n,e as g,T as b,m as y,o as c}from"./app-DzBQ2I0X.js";import{_ as v}from"./_plugin-vue_export-helper-DlAUqK2U.js";const x=m({props:{show:Boolean,invoiceItem:{type:Object,default:()=>({})}},emits:["close"],setup(e,{emit:o}){const i=b({id:"",ftth_id:"",customer_name:"",description:"",type:"",start_date:"",end_date:"",unit_price:"",total_amount:""});y(()=>e.invoiceItem,r=>{var s,u;r&&(i.id=r.id,i.ftth_id=(s=r.customer)==null?void 0:s.ftth_id,i.customer_name=(u=r.customer)==null?void 0:u.name,i.description=r.description,i.type=r.type,i.start_date=r.start_date,i.end_date=r.end_date,i.unit_price=r.unit_price,i.total_amount=r.total_amount)},{immediate:!0,deep:!0});const a=()=>{i.put(route("invoiceItems.update",i.id),{onSuccess:()=>l()})},l=()=>{i.reset(),o("close")};return{form:i,submit:a,closeModal:l}}}),w={key:0,class:"fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"},k={class:"mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"},U={class:"p-6"},I={class:"grid grid-cols-1 gap-4"},C={class:"mt-6 flex justify-end space-x-3"},D=["disabled"];function E(e,o,i,a,l,r){return e.show?(c(),f("div",w,[o[19]||(o[19]=t("div",{class:"fixed inset-0 transform transition-all"},[t("div",{class:"absolute inset-0 bg-gray-500 opacity-75"})],-1)),t("div",k,[t("div",U,[o[18]||(o[18]=t("h2",{class:"text-lg font-medium text-gray-900 mb-4"}," Edit Invoice Item ",-1)),t("form",{onSubmit:o[9]||(o[9]=p((...s)=>e.submit&&e.submit(...s),["prevent"]))},[t("div",I,[t("div",null,[o[10]||(o[10]=t("label",{class:"block font-medium text-sm text-gray-700"}," Customer ID ",-1)),d(t("input",{"onUpdate:modelValue":o[0]||(o[0]=s=>e.form.ftth_id=s),type:"text",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm",disabled:""},null,512),[[n,e.form.ftth_id]])]),t("div",null,[o[11]||(o[11]=t("label",{class:"block font-medium text-sm text-gray-700"}," Customer Name ",-1)),d(t("input",{"onUpdate:modelValue":o[1]||(o[1]=s=>e.form.customer_name=s),type:"text",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm",disabled:""},null,512),[[n,e.form.customer_name]])]),t("div",null,[o[12]||(o[12]=t("label",{class:"block font-medium text-sm text-gray-700"}," Description ",-1)),d(t("input",{"onUpdate:modelValue":o[2]||(o[2]=s=>e.form.description=s),type:"text",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm",disabled:""},null,512),[[n,e.form.description]])]),t("div",null,[o[13]||(o[13]=t("label",{class:"block font-medium text-sm text-gray-700"}," Type ",-1)),d(t("input",{"onUpdate:modelValue":o[3]||(o[3]=s=>e.form.type=s),type:"text",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm",disabled:""},null,512),[[n,e.form.type]])]),t("div",null,[o[14]||(o[14]=t("label",{class:"block font-medium text-sm text-gray-700"}," Start Date ",-1)),d(t("input",{"onUpdate:modelValue":o[4]||(o[4]=s=>e.form.start_date=s),type:"date",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"},null,512),[[n,e.form.start_date]])]),t("div",null,[o[15]||(o[15]=t("label",{class:"block font-medium text-sm text-gray-700"}," End Date ",-1)),d(t("input",{"onUpdate:modelValue":o[5]||(o[5]=s=>e.form.end_date=s),type:"date",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"},null,512),[[n,e.form.end_date]])]),t("div",null,[o[16]||(o[16]=t("label",{class:"block font-medium text-sm text-gray-700"}," Unit Price ",-1)),d(t("input",{"onUpdate:modelValue":o[6]||(o[6]=s=>e.form.unit_price=s),type:"number",step:"0.01",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"},null,512),[[n,e.form.unit_price]])]),t("div",null,[o[17]||(o[17]=t("label",{class:"block font-medium text-sm text-gray-700"}," Total Amount ",-1)),d(t("input",{"onUpdate:modelValue":o[7]||(o[7]=s=>e.form.total_amount=s),type:"number",step:"0.01",class:"mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"},null,512),[[n,e.form.total_amount]])])]),t("div",C,[t("button",{type:"button",class:"inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition",onClick:o[8]||(o[8]=(...s)=>e.closeModal&&e.closeModal(...s))}," Cancel "),t("button",{type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition",disabled:e.form.processing}," Save Changes ",8,D)])],32)])])])):g("",!0)}const S=v(x,[["render",E]]);export{S as default};
