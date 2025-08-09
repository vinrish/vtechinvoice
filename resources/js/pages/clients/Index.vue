<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Client {
    id: number;
    name: string;
    email?: string | null;
    phone?: string | null;
}

interface Props {
    clients: {
        data: Client[];
        links: any[];
        meta?: any;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [{ title: 'Clients', href: '/clients' }];
</script>

<template>
    <Head title="Clients | List" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <h1 class="text-xl font-semibold">Clients</h1>
            <div class="overflow-x-auto rounded border">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left dark:bg-gray-800 dark:text-gray-100">
                        <tr>
                            <th class="p-2">Name</th>
                            <th class="p-2">Email</th>
                            <th class="p-2">Phone</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in clients.data" :key="c.id" class="border-t">
                            <td class="p-2">{{ c.name }}</td>
                            <td class="p-2">{{ c.email }}</td>
                            <td class="p-2">{{ c.phone }}</td>
                            <td class="p-2 space-x-2">
                                <Link :href="route('clients.show', c.id)" class="text-blue-600 hover:underline">View</Link>
                                <Link :href="route('clients.edit', c.id)" class="text-blue-600 hover:underline">Edit</Link>
                                <Link as="button" method="delete" :href="route('clients.destroy', c.id)" class="text-red-600 hover:underline" onclick="return confirm('Delete this client?')">Delete</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
