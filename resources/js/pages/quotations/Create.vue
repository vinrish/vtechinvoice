<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Client { id: number; name: string; email?: string }

interface Props {
  clients: Client[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Create Quotation', href: '/quotations/create' },
];

const form = useForm({
  client_id: null as number | null,
  valid_until: '' as string | null,
  currency: 'USD',
  notes: '',
  tax_rate: 0,
  items: [
    { description: '', quantity: 1, unit_price: 0 },
  ] as Array<{ description: string; quantity: number; unit_price: number }>,
});

function addItem() {
  form.items.push({ description: '', quantity: 1, unit_price: 0 });
}
function removeItem(idx: number) {
  form.items.splice(idx, 1);
}

const totals = computed(() => {
  const subtotal = form.items.reduce((sum, it) => sum + (Number(it.quantity) || 0) * (Number(it.unit_price) || 0), 0);
  const tax = Math.round((subtotal * (Number(form.tax_rate) || 0)) ) / 100; // percent
  const total = Math.round((subtotal + tax) * 100) / 100;
  return { subtotal, tax, total };
});

function submit() {
  form.post(route('quotations.store'), {
    preserveScroll: true,
    onSuccess: () => router.visit(route('quotations.index')),
  });
}
</script>

<template>
  <Head title="Create Quotation" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
      <h1 class="text-xl font-semibold">New Quotation</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="mb-1 block text-sm">Client</label>
          <select v-model.number="form.client_id" class="border-input w-full rounded-md border bg-transparent px-3 py-2">
            <option :value="null" disabled>Select a client</option>
            <option v-for="c in props.clients" :key="c.id" :value="c.id">{{ c.name }} <span v-if="c.email"> ({{ c.email }})</span></option>
          </select>
          <div v-if="form.errors.client_id" class="text-sm text-red-600 mt-1">{{ form.errors.client_id }}</div>
        </div>
        <div>
          <label class="mb-1 block text-sm">Currency</label>
          <Input v-model="form.currency" />
          <div v-if="form.errors.currency" class="text-sm text-red-600 mt-1">{{ form.errors.currency }}</div>
        </div>
        <div>
          <label class="mb-1 block text-sm">Valid Until</label>
          <Input v-model="form.valid_until" type="date" />
          <div v-if="form.errors.valid_until" class="text-sm text-red-600 mt-1">{{ form.errors.valid_until }}</div>
        </div>
      </div>

      <div>
        <label class="mb-2 block text-sm">Items</label>
        <div class="space-y-3">
          <div v-for="(it, idx) in form.items" :key="idx" class="grid grid-cols-12 gap-2 items-end">
            <div class="col-span-6">
              <label class="mb-1 block text-xs text-muted-foreground">Description</label>
              <Input v-model="it.description" placeholder="Item description" />
            </div>
            <div class="col-span-2">
              <label class="mb-1 block text-xs text-muted-foreground">Qty</label>
              <Input v-model.number="it.quantity" type="number" min="0" step="0.01" />
            </div>
            <div class="col-span-2">
              <label class="mb-1 block text-xs text-muted-foreground">Unit Price</label>
              <Input v-model.number="it.unit_price" type="number" min="0" step="0.01" />
            </div>
            <div class="col-span-2 flex items-center gap-2">
              <Button variant="destructive" size="sm" class="mt-6" @click="removeItem(idx)" :disabled="form.items.length === 1">Remove</Button>
            </div>
          </div>
          <div class="text-sm text-red-600" v-if="form.errors['items']">{{ form.errors['items'] }}</div>
          <div class="text-sm text-red-600" v-if="form.errors['items.0.description'] || form.errors['items.*.description']">{{ form.errors['items.0.description'] || form.errors['items.*.description'] }}</div>
        </div>
        <div class="mt-3">
          <Button size="sm" variant="secondary" @click="addItem">Add Item</Button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
          <label class="mb-1 block text-sm">Notes</label>
          <textarea v-model="form.notes" rows="4" class="border-input w-full rounded-md border bg-transparent px-3 py-2"></textarea>
          <div v-if="form.errors.notes" class="text-sm text-red-600 mt-1">{{ form.errors.notes }}</div>
        </div>
        <div class="md:col-span-1">
          <div class="rounded-md border p-3 space-y-2">
            <div class="flex justify-between text-sm"><span>Subtotal</span><span>{{ totals.subtotal.toFixed(2) }}</span></div>
            <div>
              <label class="mb-1 block text-sm">Tax Rate (%)</label>
              <Input v-model.number="form.tax_rate" type="number" min="0" step="0.01" />
            </div>
            <div class="flex justify-between text-sm"><span>Tax</span><span>{{ totals.tax.toFixed(2) }}</span></div>
            <div class="flex justify-between font-semibold"><span>Total</span><span>{{ totals.total.toFixed(2) }}</span></div>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <Button :disabled="form.processing" @click="submit">Create Quotation</Button>
        <div v-if="Object.keys(form.errors).length" class="text-sm text-red-600">Please fix the errors above.</div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped></style>
