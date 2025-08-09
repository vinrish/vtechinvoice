<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
  users: {
    data: Pick<User, 'id' | 'name' | 'email' | 'created_at'>[];
    links: any[];
    meta?: any;
  };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: '/users' },
];
</script>

<template>
  <Head title="Users | List" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-4">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Users</h1>
        <Link :href="route('users.create')"><Button size="sm">Create User</Button></Link>
      </div>

      <div class="overflow-x-auto rounded border">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-left dark:bg-gray-800 dark:text-gray-100">
            <tr>
              <th class="p-2">Name</th>
              <th class="p-2">Email</th>
              <th class="p-2">Joined</th>
              <th class="p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users.data" :key="u.id" class="border-t">
              <td class="p-2">{{ u.name }}</td>
              <td class="p-2">{{ u.email }}</td>
              <td class="p-2">{{ new Date(u.created_at).toLocaleDateString() }}</td>
              <td class="p-2 space-x-2">
                <Link :href="route('users.show', u.id)" class="text-blue-600 hover:underline">View</Link>
                <Link :href="route('users.edit', u.id)" class="text-blue-600 hover:underline">Edit</Link>
                <Link as="button" method="delete" :href="route('users.destroy', u.id)" class="text-red-600 hover:underline" onclick="return confirm('Delete this user?')">Delete</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped></style>
