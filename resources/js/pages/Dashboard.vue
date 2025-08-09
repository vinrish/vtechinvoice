<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Totals { clients: number; invoices: number; revenue: number; outstanding: number }
interface StatusItem { status: string; count: number; total: number }
interface RevenuePoint { month: string; total: number }

interface Props {
  totals: Totals;
  statusSummary: StatusItem[];
  revenueByMonth: RevenuePoint[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
];

const maxRevenue = computed(() => Math.max(1, ...props.revenueByMonth.map(r => r.total)));
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
      <!-- Top stats cards -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border p-4 bg-white dark:bg-neutral-900">
          <div class="text-sm text-muted-foreground">Clients</div>
          <div class="mt-2 text-2xl font-semibold">{{ props.totals.clients }}</div>
        </div>
        <div class="rounded-xl border p-4 bg-white dark:bg-neutral-900">
          <div class="text-sm text-muted-foreground">Invoices</div>
          <div class="mt-2 text-2xl font-semibold">{{ props.totals.invoices }}</div>
        </div>
        <div class="rounded-xl border p-4 bg-white dark:bg-neutral-900">
          <div class="text-sm text-muted-foreground">Total Revenue</div>
          <div class="mt-2 text-2xl font-semibold">{{ new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(props.totals.revenue) }}</div>
        </div>
        <div class="rounded-xl border p-4 bg-white dark:bg-neutral-900">
          <div class="text-sm text-muted-foreground">Outstanding</div>
          <div class="mt-2 text-2xl font-semibold">{{ new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(props.totals.outstanding) }}</div>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Revenue chart -->
        <div class="rounded-xl border p-4 bg-white dark:bg-neutral-900 lg:col-span-2">
          <div class="mb-4 flex items-center justify-between">
            <div class="text-base font-medium">Revenue (last 6 months)</div>
          </div>
          <div class="h-48 flex items-end gap-2">
            <div v-for="pt in props.revenueByMonth" :key="pt.month" class="flex-1 flex flex-col items-center justify-end">
              <div class="w-full bg-blue-500/20 dark:bg-blue-400/20 rounded-t" :style="{ height: `${(pt.total / maxRevenue) * 100}%` }">
                <div class="h-full w-full bg-blue-500 dark:bg-blue-400 rounded-t"></div>
              </div>
              <div class="mt-1 text-[10px] text-muted-foreground">{{ pt.month }}</div>
              <div class="text-[10px]">{{ pt.total.toFixed(2) }}</div>
            </div>
          </div>
        </div>

        <!-- Status summary -->
        <div class="rounded-xl border p-4 bg-white dark:bg-neutral-900">
          <div class="mb-4 text-base font-medium">Invoices by status</div>
          <div class="space-y-3">
            <div v-for="s in props.statusSummary" :key="s.status" class="flex items-center justify-between rounded border p-2">
              <div>
                <div class="text-sm font-medium capitalize">{{ s.status }}</div>
                <div class="text-xs text-muted-foreground">{{ s.count }} invoices</div>
              </div>
              <div class="text-sm font-semibold">{{ new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(s.total) }}</div>
            </div>
            <div v-if="props.statusSummary.length === 0" class="text-sm text-muted-foreground">No data yet.</div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
