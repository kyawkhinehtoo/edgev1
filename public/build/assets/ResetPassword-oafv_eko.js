import{T as f,d as c,b as o,u as e,w as l,F as w,o as _,Z as g,a as t,n as V,f as b,h as k}from"./app-DdoqqyeN.js";import{A as v}from"./AuthenticationCard-BRY1FFxv.js";import{_ as x}from"./AuthenticationCardLogo-BuVGeeSu.js";import{_ as m,a as i}from"./TextInput-B7xG93F8.js";import{_ as n}from"./InputLabel-Cj7F8OT7.js";import{_ as y}from"./PrimaryButton-CMsSAGp3.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const P={class:"mt-4"},$={class:"mt-4"},C={class:"flex items-center justify-end mt-4"},E={__name:"ResetPassword",props:{email:String,token:String},setup(p){const d=p,s=f({token:d.token,email:d.email,password:"",password_confirmation:""}),u=()=>{s.post(route("password.update"),{onFinish:()=>s.reset("password","password_confirmation")})};return(q,a)=>(_(),c(w,null,[o(e(g),{title:"Reset Password"}),o(v,null,{logo:l(()=>[o(x)]),default:l(()=>[t("form",{onSubmit:k(u,["prevent"])},[t("div",null,[o(n,{for:"email",value:"Email"}),o(m,{id:"email",modelValue:e(s).email,"onUpdate:modelValue":a[0]||(a[0]=r=>e(s).email=r),type:"email",class:"mt-1 block w-full",required:"",autofocus:"",autocomplete:"username"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.email},null,8,["message"])]),t("div",P,[o(n,{for:"password",value:"Password"}),o(m,{id:"password",modelValue:e(s).password,"onUpdate:modelValue":a[1]||(a[1]=r=>e(s).password=r),type:"password",class:"mt-1 block w-full",required:"",autocomplete:"new-password"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.password},null,8,["message"])]),t("div",$,[o(n,{for:"password_confirmation",value:"Confirm Password"}),o(m,{id:"password_confirmation",modelValue:e(s).password_confirmation,"onUpdate:modelValue":a[2]||(a[2]=r=>e(s).password_confirmation=r),type:"password",class:"mt-1 block w-full",required:"",autocomplete:"new-password"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.password_confirmation},null,8,["message"])]),t("div",C,[o(y,{class:V({"opacity-25":e(s).processing}),disabled:e(s).processing},{default:l(()=>a[3]||(a[3]=[b(" Reset Password ")])),_:1},8,["class","disabled"])])],32)]),_:1})],64))}};export{E as default};
