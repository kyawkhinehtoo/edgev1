<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center  px-6 py-6 sm:pt-0 bg-gray-400 dark:bg-gray-900 "
        :style="{ backgroundImage: 'url(/storage/images/background.jpg)', backgroundSize: 'cover', backgroundPosition: 'center' }"
    >
  
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full px-6 py-6 border-s-orange-100 border-4 rounded-xl grow items-center mt-6 bg-gray-300 bg-opacity-60 ">
            <div class="col-span-2  px-12"> <slot name="logo"  /></div>
             <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                <slot />
             </div>
             
        </div>
  
        

       
    </div>
</template>
