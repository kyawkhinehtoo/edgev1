<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Jetstream/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue';
import Checkbox from '@/Jetstream/Checkbox.vue';
import InputError from '@/Jetstream/InputError.vue';
import InputLabel from '@/Jetstream/InputLabel.vue';
import PrimaryButton from '@/Jetstream/PrimaryButton.vue';
import TextInput from '@/Jetstream/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
    error: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head>
        <title>Login -{{ ($page.props.isp)?$page.props.isp.name + ' |' :"" }} {{ $page.props.application_name }}</title>
        <meta name="description" content="ISP Manager OSS BSS SYSTEM">
        <link rel="icon" type="image/png" href="/storage/logos/favicon.png" />
      </Head>
  

    <AuthenticationCard>
        <template #logo>
            <span class="flex flex-col items-center align-middle uppercase text-gray-400">
                <span v-if="$page.props.login_type === 'internal'">
                    <img v-if="$page.props.logo_small" :src="`/storage/${$page.props.logo_small}`" alt="Logo" class="w-16" />
                  </span>
                  <span v-else-if="$page.props.login_type === 'isp'">
                    <img v-if="$page.props.isp?.logo" :src="`/storage/${$page.props.isp.logo}`" alt="Logo" class="w-16" />
                  </span>
                  <span v-else-if="$page.props.login_type === 'partner'">
                    <img v-if="$page.props.partner?.logo" :src="`/storage/${$page.props.partner.logo}`" alt="Logo" class="w-16" />
                  </span>
               
                <h2 class="mt-2 font-bold antialiased flex">Operation & Billing Support System</h2>
            </span>
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>
        <div v-if="$page.props.flash.error" class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
            {{ $page.props.flash.error }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus
                    autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
                    autocomplete="current-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Forgot your password?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
