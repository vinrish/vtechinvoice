<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: '/users' },
  { title: 'Create', href: '/users/create' },
];

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('users.store'));
};
</script>

<template>
  <Head title="Create User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4 max-w-xl">
      <h1 class="text-xl font-semibold">Create User</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-2">
          <Label for="name">Name</Label>
          <Input id="name" v-model="form.name" placeholder="Full name" required autocomplete="name" />
          <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
          <Label for="email">Email</Label>
          <Input id="email" type="email" v-model="form.email" placeholder="name@example.com" required autocomplete="username" />
          <InputError :message="form.errors.email" />
        </div>

        <div class="grid gap-2">
          <Label for="password">Password</Label>
          <Input id="password" type="password" v-model="form.password" placeholder="••••••••" required autocomplete="new-password" />
          <InputError :message="form.errors.password" />
        </div>

        <div class="grid gap-2">
          <Label for="password_confirmation">Confirm password</Label>
          <Input id="password_confirmation" type="password" v-model="form.password_confirmation" placeholder="••••••••" required autocomplete="new-password" />
        </div>

        <div class="flex items-center gap-2">
          <Button type="submit" :disabled="form.processing">Create</Button>
          <Link :href="route('users.index')" class="text-sm text-muted-foreground hover:underline">Cancel</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped></style>
