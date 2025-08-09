<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface UserVm {
  id: number;
  name: string;
  email: string;
  created_at?: string;
}

const props = defineProps<{ user: UserVm }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: '/users' },
  { title: props.user.name, href: `/users/${props.user.id}` },
];
</script>

<template>
  <Head :title="`User | ${user.name}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-4 max-w-2xl">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">User Details</h1>
        <Link :href="route('users.edit', user.id)" class="text-blue-600 hover:underline">Edit</Link>
      </div>
      <div class="rounded border divide-y">
        <div class="p-3"><span class="font-medium">Name:</span> {{ user.name }}</div>
        <div class="p-3"><span class="font-medium">Email:</span> {{ user.email }}</div>
        <div v-if="user.created_at" class="p-3"><span class="font-medium">Joined:</span> {{ new Date(user.created_at).toLocaleString() }}</div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped></style>
