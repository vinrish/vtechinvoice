<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface ClientVm {
  id: number;
  name: string;
  email?: string | null;
  phone?: string | null;
}

const props = defineProps<{ client: ClientVm }>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Clients', href: '/clients' },
  { title: props.client.name, href: `/clients/${props.client.id}` },
];
</script>

<template>
  <Head :title="`Client | ${client.name}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-4 max-w-2xl">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Client Details</h1>
        <Link :href="route('clients.edit', client.id)" class="text-blue-600 hover:underline">Edit</Link>
      </div>
      <div class="rounded border divide-y">
        <div class="p-3"><span class="font-medium">Name:</span> {{ client.name }}</div>
        <div class="p-3"><span class="font-medium">Email:</span> {{ client.email || '-' }}</div>
        <div class="p-3"><span class="font-medium">Phone:</span> {{ client.phone || '-' }}</div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped></style>
