<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';

interface ClientVm { id: number; name: string; email?: string | null; phone?: string | null }
const props = defineProps<{ client: ClientVm }>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Clients', href: '/clients' },
  { title: 'Edit', href: `/clients/${props.client.id}/edit` },
];

const form = useForm({
  name: props.client.name || '',
  email: props.client.email || '',
  phone: props.client.phone || '',
});

const submit = () => {
  form.put(route('clients.update', props.client.id));
};
</script>

<template>
  <Head title="Edit Client" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4 max-w-xl">
      <h1 class="text-xl font-semibold">Edit Client</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-2">
          <Label for="name">Name</Label>
          <Input id="name" v-model="form.name" required />
          <InputError :message="form.errors.name" />
        </div>
        <div class="grid gap-2">
          <Label for="email">Email</Label>
          <Input id="email" type="email" v-model="form.email" />
          <InputError :message="form.errors.email" />
        </div>
        <div class="grid gap-2">
          <Label for="phone">Phone</Label>
          <Input id="phone" v-model="form.phone" />
          <InputError :message="form.errors.phone" />
        </div>
        <div class="flex items-center gap-2">
          <Button type="submit" :disabled="form.processing">Save</Button>
          <Link :href="route('clients.index')" class="text-sm text-muted-foreground hover:underline">Cancel</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped></style>
