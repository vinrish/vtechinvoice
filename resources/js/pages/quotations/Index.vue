<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Quotation {
  id: number;
  number: string;
  status: string;
  total: number | string;
  currency: string;
  client?: { id: number; name: string };
}

interface Props {
  quotations: {
    data: Quotation[];
    links: any[];
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [{ title: 'Quotations', href: '/quotations' }];

const viewKey = 'quotationsViewMode';
const viewMode = ref<string>(localStorage.getItem(viewKey) || 'list');
watch(viewMode, v => localStorage.setItem(viewKey, v));

const statuses = computed(() => {
  const set = new Set<string>();
  for (const q of props.quotations.data) set.add(q.status || 'unknown');
  return Array.from(set);
});

const grouped = computed(() => {
  const map: Record<string, Quotation[]> = {};
  for (const s of statuses.value) map[s] = [];
  for (const q of props.quotations.data) {
    const key = q.status || 'unknown';
    (map[key] ||= []).push(q);
  }
  return map;
});
</script>

<template>
  <Head title="Quotations | List" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-4">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Quotations</h1>
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
            <tr v-for="q in quotations.data" :key="q.id" class="border-t">
              <td class="p-2">{{ q.number }}</td>
              <td class="p-2">{{ q.client?.name }}</td>
              <td class="p-2">{{ q.status }}</td>
              <td class="p-2">{{ q.currency }} {{ Number(q.total).toFixed(2) }}</td>
              <td class="p-2">
                <a :href="route('quotations.show', q.id)" class="text-blue-600 hover:underline">View</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="status in statuses" :key="status" class="rounded border">
          <div class="border-b bg-gray-50 px-3 py-2 font-medium">{{ status }}</div>
          <div class="p-3 space-y-3">
            <div v-for="q in grouped[status]" :key="q.id" class="rounded border p-3 bg-white shadow-sm">
              <div class="text-sm font-medium">{{ q.number }}</div>
              <div class="text-xs text-muted-foreground">{{ q.client?.name }}</div>
              <div class="text-sm">{{ q.currency }} {{ Number(q.total).toFixed(2) }}</div>
            </div>
            <div v-if="grouped[status]?.length === 0" class="text-xs text-muted-foreground">No quotations</div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped></style>
