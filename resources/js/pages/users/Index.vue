<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users List',
        href: '/users/index',
    },
];
</script>

<template>
    <Head title="Users | List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="p-4">
                <Link :href="route('users.create')"><Button>Create User</Button></Link>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
