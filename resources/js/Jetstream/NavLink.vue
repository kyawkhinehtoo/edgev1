<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: '#'
    },
    active: {
        type: Boolean,
        default: false
    },
    show: {
        type: Boolean,
        default: true
    },
    isCollapsed: {
        type: Boolean,
        default: false
    }
});


const classes = computed(() => {
    return props.active
        ? 'text-blue-600 hover:text-blue-500   bg-blueGray-100 shadow-blueGray-200 shadow-inner  dark:bg-gray-700 dark:text-white '
        : 'text-gray-700 hover:bg-gray-100     bg-white        shadow-gray-900                   dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200';
});
</script>

<template>
    <div v-if="isCollapsed" class="bg-white m-0">
        <Link :href="href" class="py-2 px-4 text-xs w-full rounded-md" :class="classes" :style="props.active? { color: $page.props.accent_color }:''"  v-if="show">
            <slot />  
        </Link>
    </div>
   
    <template v-else>
        <Link :href="href" class="text-xs uppercase py-3 px-4 font-bold block rounded-md mt-1"  :class="classes" :style="props.active? { color: $page.props.accent_color }:''" v-if="show">
            <slot />
          </Link>
    </template>
    
</template>
