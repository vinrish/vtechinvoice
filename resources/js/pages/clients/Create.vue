<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Create Client', href: '/clients/create' },
];

const form = useForm({
  name: '',
  email: '',
  phone: '',
  address: '',
  notes: '',
});

function submit() {
  form.post(route('clients.store'), {
    preserveScroll: true,
    onSuccess: () => router.visit(route('clients.index')),
  });
}
</script>

<template>
  <Head title="Create Client" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
      <h1 class="text-xl font-semibold">New Client</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="mb-1 block text-sm">Name</label>
          <Input v-model="form.name" placeholder="Client name" />
          <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
        </div>
        <div>
          <label class="mb-1 block text-sm">Email</label>
          <Input v-model="form.email" type="email" placeholder="name@example.com" />
          <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
        </div>
        <div>
          <label class="mb-1 block text-sm">Phone</label>
          <Input v-model="form.phone" placeholder="+1 555 555 5555" />
          <div v-if="form.errors.phone" class="text-sm text-red-600 mt-1">{{ form.errors.phone }}</div>
        </div>
        <div class="md:col-span-2">
          <label class="mb-1 block text-sm">Address</label>
          <textarea v-model="form.address" rows="3" class="border-input w-full rounded-md border bg-transparent px-3 py-2"></textarea>
          <div v-if="form.errors.address" class="text-sm text-red-600 mt-1">{{ form.errors.address }}</div>
        </div>
        <div class="md:col-span-2">
          <label class="mb-1 block text-sm">Notes</label>
          <textarea v-model="form.notes" rows="4" class="border-input w-full rounded-md border bg-transparent px-3 py-2"></textarea>
          <div v-if="form.errors.notes" class="text-sm text-red-600 mt-1">{{ form.errors.notes }}</div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <Button :disabled="form.processing" @click="submit">Create Client</Button>
        <div v-if="Object.keys(form.errors).length" class="text-sm text-red-600">Please fix the errors above.</div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped></style>
