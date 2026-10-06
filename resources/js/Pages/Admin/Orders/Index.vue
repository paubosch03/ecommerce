<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ArrowRight } from '@lucide/vue';

defineProps({
    orders: Object,
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(date));
}

const statusConfig = {
    pending:   { label: 'Pendiente',  class: 'bg-amber-50 text-amber-600 border-amber-100' },
    paid:      { label: 'Pagado',     class: 'bg-emerald-50 text-emerald-600 border-emerald-100' },
    shipped:   { label: 'Enviado',    class: 'bg-blue-50 text-blue-600 border-blue-100' },
    delivered: { label: 'Entregado',  class: 'bg-gray-50 text-gray-600 border-gray-100' },
    cancelled: { label: 'Cancelado',  class: 'bg-rose-50 text-rose-500 border-rose-100' },
};
</script>

<template>
    <Head title="Admin — Pedidos" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <div class="mb-12">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Panel admin</p>
                <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Pedidos</h1>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">#</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Cliente</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Fecha</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Total</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Estado</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">#{{ order.id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ order.user?.name }}</p>
                                <p class="text-xs text-gray-400">{{ order.shipping_email }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(order.total) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="statusConfig[order.status]?.class"
                                    class="text-xs font-medium px-2.5 py-1 rounded-full border">
                                    {{ statusConfig[order.status]?.label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('admin.orders.show', order.id)"
                                    class="p-2 text-gray-400 hover:text-gray-900 transition-colors rounded-lg hover:bg-gray-100 inline-flex">
                                    <ArrowRight :size="14" />
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!orders.data.length">
                            <td colspan="6" class="px-6 py-16 text-center">
                                <p class="text-sm text-gray-400">No hay pedidos todavía</p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="orders.last_page > 1" class="flex items-center justify-center gap-2 px-6 py-4 border-t border-gray-100">
                    <Link v-for="link in orders.links" :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'text-xs font-medium px-4 py-2 rounded-full transition-colors',
                            link.active ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900 border border-gray-200',
                            !link.url ? 'opacity-30 pointer-events-none' : ''
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </ShopLayout>
</template>