<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Invoice {
  id: number;
  number: string;
  status: string;
  total: number | string;
  currency: string;
  client?: { id: number; name: string };
}

interface Props {
  invoices: {
    data: Invoice[];
    links: any[];
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [{ title: 'Invoices', href: '/invoices' }];

const viewKey = 'invoicesViewMode';
const viewMode = ref<string>(localStorage.getItem(viewKey) || 'list');
watch(viewMode, v => localStorage.setItem(viewKey, v));

const statuses = computed(() => {
  const set = new Set<string>();
  for (const i of props.invoices.data) set.add(i.status || 'unknown');
  return Array.from(set);
});

const grouped = computed(() => {
  const map: Record<string, Invoice[]> = {};
  for (const s of statuses.value) map[s] = [];
  for (const i of props.invoices.data) {
    const key = i.status || 'unknown';
    (map[key] ||= []).push(i);
  }
  return map;
});
</script>

<template>
  <Head title="Invoices | List" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-4">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Invoices</h1>
        <div class="flex gap-2">
          <Button :variant="viewMode === 'list' ? 'default' : 'secondary'" size="sm" @click="viewMode = 'list'">List</Button>
          <Button :variant="viewMode === 'kanban' ? 'default' : 'secondary'" size="sm" @click="viewMode = 'kanban'">Kanban</Button>
        </div>
      </div>

      <div v-if="viewMode === 'list'" class="overflow-x-auto rounded border">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-left dark:bg-gray-800 dark:text-gray-100">
            <tr>
              <th class="p-2">Number</th>
              <th class="p-2">Client</th>
              <th class="p-2">Status</th>
              <th class="p-2">Total</th>
              <th class="p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="inv in invoices.data" :key="inv.id" class="border-t">
              <td class="p-2">{{ inv.number }}</td>
              <td class="p-2">{{ inv.client?.name }}</td>
              <td class="p-2">{{ inv.status }}</td>
              <td class="p-2">{{ inv.currency }} {{ Number(inv.total).toFixed(2) }}</td>
              <td class="p-2 space-x-2">
                <a :href="route('invoices.show', inv.id)" class="text-blue-600 hover:underline">View</a>
                <a :href="route('invoices.download', inv.id)" class="text-blue-600 hover:underline">Download PDF</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="status in statuses" :key="status" class="rounded border">
          <div class="border-b bg-gray-50 px-3 py-2 font-medium">{{ status }}</div>
          <div class="p-3 space-y-3">
            <div v-for="inv in grouped[status]" :key="inv.id" class="rounded border p-3 bg-white shadow-sm">
              <div class="text-sm font-medium">{{ inv.number }}</div>
              <div class="text-xs text-muted-foreground">{{ inv.client?.name }}</div>
              <div class="text-sm">{{ inv.currency }} {{ Number(inv.total).toFixed(2) }}</div>
              <div class="mt-2 text-xs"><a :href="route('invoices.download', inv.id)" class="text-blue-600 hover:underline">Download</a></div>
            </div>
            <div v-if="grouped[status]?.length === 0" class="text-xs text-muted-foreground">No invoices</div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped></style>
