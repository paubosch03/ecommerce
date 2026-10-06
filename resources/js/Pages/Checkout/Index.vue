<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ref, onMounted } from 'vue';
import { ArrowLeft, ShieldCheck, Lock } from '@lucide/vue';
import { loadStripe } from '@stripe/stripe-js';

const props = defineProps({
    items:           Array,
    total:           Number,
    stripePublicKey: String,
});

const stripe        = ref(null);
const elements      = ref(null);
const cardElement   = ref(null);
const cardContainer = ref(null);
const processing    = ref(false);
const cardError     = ref('');

const form = useForm({
    shipping_name:     '',
    shipping_email:    '',
    shipping_address:  '',
    shipping_city:     '',
    shipping_zip:      '',
    stripe_payment_id: '',
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

onMounted(async () => {
    stripe.value   = await loadStripe(props.stripePublicKey);
    elements.value = stripe.value.elements();

    cardElement.value = elements.value.create('card', {
        style: {
            base: {
                fontSize:        '14px',
                color:           '#111827',
                fontFamily:      'Inter, sans-serif',
                '::placeholder': { color: '#9ca3af' },
            },
            invalid: { color: '#f43f5e' },
        },
    });

    cardElement.value.mount(cardContainer.value);

    cardElement.value.on('change', (event) => {
        cardError.value = event.error ? event.error.message : '';
    });
});

async function submitOrder() {
    processing.value = true;
    cardError.value  = '';

    try {
        // Crear PaymentMethod con Stripe
        const { paymentMethod, error } = await stripe.value.createPaymentMethod({
            type: 'card',
            card: cardElement.value,
            billing_details: {
                name:  form.shipping_name,
                email: form.shipping_email,
            },
        });

        if (error) {
            cardError.value  = error.message;
            processing.value = false;
            return;
        }

        form.stripe_payment_id = paymentMethod.id;
        form.post(route('checkout.store'), {
            onFinish: () => { processing.value = false; },
        });

    } catch (e) {
        cardError.value  = 'Error al procesar el pago. Inténtalo de nuevo.';
        processing.value = false;
    }
}
</script>

<template>
    <Head title="Checkout" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <!-- Header -->
            <div class="mb-12">
                <Link :href="route('cart.index')"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 transition-colors mb-6">
                    <ArrowLeft :size="12" />
                    Volver al carrito
                </Link>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Último paso</p>
                <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Finalizar compra</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                <!-- Formulario -->
                <div class="lg:col-span-2 space-y-10">

                    <!-- Datos de envío -->
                    <div>
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-6">
                            Datos de envío
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-2">Nombre completo</label>
                                <input
                                    v-model="form.shipping_name"
                                    type="text"
                                    placeholder="Pau Bosch"
                                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                                <p v-if="form.errors.shipping_name" class="text-xs text-rose-500 mt-1">{{ form.errors.shipping_name }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-2">Email</label>
                                <input
                                    v-model="form.shipping_email"
                                    type="email"
                                    placeholder="pau@ejemplo.com"
                                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                                <p v-if="form.errors.shipping_email" class="text-xs text-rose-500 mt-1">{{ form.errors.shipping_email }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-2">Dirección</label>
                                <input
                                    v-model="form.shipping_address"
                                    type="text"
                                    placeholder="Calle Mayor 123"
                                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                                <p v-if="form.errors.shipping_address" class="text-xs text-rose-500 mt-1">{{ form.errors.shipping_address }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Ciudad</label>
                                <input
                                    v-model="form.shipping_city"
                                    type="text"
                                    placeholder="Barcelona"
                                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                                <p v-if="form.errors.shipping_city" class="text-xs text-rose-500 mt-1">{{ form.errors.shipping_city }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Código postal</label>
                                <input
                                    v-model="form.shipping_zip"
                                    type="text"
                                    placeholder="08001"
                                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                                <p v-if="form.errors.shipping_zip" class="text-xs text-rose-500 mt-1">{{ form.errors.shipping_zip }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Datos de pago -->
                    <div>
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-6">
                            Datos de pago
                        </p>
                        <div class="border border-gray-200 rounded-xl px-4 py-3.5 bg-gray-50 focus-within:border-gray-400 focus-within:bg-white transition-colors">
                            <div ref="cardContainer"></div>
                        </div>
                        <p v-if="cardError" class="text-xs text-rose-500 mt-2">{{ cardError }}</p>

                        <!-- Tarjetas de prueba -->
                        <div class="mt-3 flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                            <span class="text-blue-500 text-xs">ℹ</span>
                            <p class="text-xs text-blue-600">
                                Modo prueba: usa la tarjeta <strong>4242 4242 4242 4242</strong>, cualquier fecha futura y cualquier CVC.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 rounded-3xl border border-gray-100 bg-white p-8">
                        <p class="text-sm font-semibold text-gray-900 mb-6">Resumen</p>

                        <!-- Productos -->
                        <div class="space-y-4 mb-6 pb-6 border-b border-gray-100">
                            <div v-for="item in items" :key="item.name" class="flex items-center justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-900 truncate">{{ item.name }}</p>
                                    <p class="text-xs text-gray-400">× {{ item.quantity }}</p>
                                </div>
                                <p class="text-xs font-semibold text-gray-900 shrink-0">
                                    {{ formatCurrency(item.subtotal) }}
                                </p>
                            </div>
                        </div>

                        <!-- Totales -->
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Subtotal</span>
                                <span class="text-xs font-medium text-gray-900">{{ formatCurrency(total) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Envío</span>
                                <span class="text-xs font-medium text-emerald-500">
                                    {{ total >= 50 ? 'Gratis' : formatCurrency(4.99) }}
                                </span>
                            </div>
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900">Total</span>
                                <span class="text-lg font-bold text-gray-900">
                                    {{ formatCurrency(total < 50 ? total + 4.99 : total) }}
                                </span>
                            </div>
                        </div>

                        <!-- Botón pagar -->
                        <button
                            @click="submitOrder"
                            :disabled="processing || form.processing"
                            class="w-full flex items-center justify-center gap-2 bg-gray-900 text-white text-sm font-medium py-4 rounded-full hover:bg-gray-700 transition-colors disabled:opacity-50"
                        >
                            <Lock :size="14" />
                            {{ processing ? 'Procesando...' : `Pagar ${formatCurrency(total < 50 ? total + 4.99 : total)}` }}
                        </button>

                        <!-- Seguridad -->
                        <div class="flex items-center justify-center gap-2 mt-4">
                            <ShieldCheck :size="12" class="text-gray-300" />
                            <p class="text-xs text-gray-400">Pago seguro con Stripe</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>