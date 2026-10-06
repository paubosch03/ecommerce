<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ref, watch } from 'vue';
import { Search, SlidersHorizontal, X, ArrowRight } from '@lucide/vue';

const props = defineProps({
    products:   Object,
    categories: Array,
    filters:    Object,
});

const search    = ref(props.filters?.search ?? '');
const category  = ref(props.filters?.category ?? '');
const minPrice  = ref(props.filters?.min_price ?? '');
const maxPrice  = ref(props.filters?.max_price ?? '');
const sortBy    = ref(props.filters?.sort_by ?? 'created_at');
const mobileFilters = ref(false);

function applyFilters() {
    router.get(route('products.index'), {
        search:    search.value,
        category:  category.value,
        min_price: minPrice.value,
        max_price: maxPrice.value,
        sort_by:   sortBy.value,
    }, { preserveState: true, replace: true });
}

function clearFilters() {
    search.value   = '';
    category.value = '';
    minPrice.value = '';
    maxPrice.value = '';
    sortBy.value   = 'created_at';
    applyFilters();
}

watch(sortBy, applyFilters);

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

const hasFilters = () => search.value || category.value || minPrice.value || maxPrice.value;
</script>

<template>
    <Head title="Productos" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <!-- Header -->
            <div class="flex items-end justify-between mb-12">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Catálogo</p>
                    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
                        Todos los productos
                        <span class="text-gray-400 font-normal text-lg ml-2">({{ products.total }})</span>
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Ordenar -->
                    <select
                        v-model="sortBy"
                        class="text-xs font-medium text-gray-600 border border-gray-200 rounded-full px-4 py-2 bg-white focus:outline-none focus:border-gray-400 appearance-none cursor-pointer"
                    >
                        <option value="created_at">Más recientes</option>
                        <option value="price">Precio: menor a mayor</option>
                        <option value="price_desc">Precio: mayor a menor</option>
                        <option value="name">Nombre A-Z</option>
                    </select>

                    <!-- Filtros móvil -->
                    <button
                        @click="mobileFilters = !mobileFilters"
                        class="lg:hidden flex items-center gap-2 text-xs font-medium border border-gray-200 rounded-full px-4 py-2 hover:border-gray-400 transition-colors"
                    >
                        <SlidersHorizontal :size="14" />
                        Filtros
                    </button>
                </div>
            </div>

            <div class="flex gap-10">

                <!-- ─── SIDEBAR ─── -->
                <aside class="hidden lg:block w-56 shrink-0">
                    <div class="sticky top-24 space-y-8">

                        <!-- Búsqueda -->
                        <div>
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Buscar</p>
                            <div class="relative">
                                <Search :size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar productos..."
                                    @keyup.enter="applyFilters"
                                    class="w-full pl-9 pr-3 py-2.5 text-xs border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                            </div>
                        </div>

                        <!-- Categorías -->
                        <div>
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Categoría</p>
                            <div class="space-y-1">
                                <button
                                    @click="category = ''; applyFilters()"
                                    :class="!category ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                                    class="w-full text-left text-xs font-medium px-3 py-2 rounded-xl transition-colors"
                                >
                                    Todas
                                </button>
                                <button
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    @click="category = cat.slug; applyFilters()"
                                    :class="category === cat.slug ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                                    class="w-full text-left text-xs font-medium px-3 py-2 rounded-xl transition-colors"
                                >
                                    {{ cat.name }}
                                </button>
                            </div>
                        </div>

                        <!-- Precio -->
                        <div>
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Precio</p>
                            <div class="space-y-2">
                                <input
                                    v-model="minPrice"
                                    type="number"
                                    placeholder="Mínimo €"
                                    class="w-full px-3 py-2.5 text-xs border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                                <input
                                    v-model="maxPrice"
                                    type="number"
                                    placeholder="Máximo €"
                                    class="w-full px-3 py-2.5 text-xs border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors"
                                />
                                <button
                                    @click="applyFilters"
                                    class="w-full text-xs font-medium bg-gray-900 text-white py-2.5 rounded-xl hover:bg-gray-700 transition-colors"
                                >
                                    Aplicar
                                </button>
                            </div>
                        </div>

                        <!-- Limpiar filtros -->
                        <button
                            v-if="hasFilters()"
                            @click="clearFilters"
                            class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 transition-colors"
                        >
                            <X :size="12" />
                            Limpiar filtros
                        </button>
                    </div>
                </aside>

                <!-- ─── PRODUCTOS ─── -->
                <div class="flex-1">

                    <!-- Filtros activos -->
                    <div v-if="hasFilters()" class="flex items-center gap-2 mb-6 flex-wrap">
                        <span class="text-xs text-gray-400">Filtros activos:</span>
                        <span v-if="search" class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
                            "{{ search }}" <button @click="search = ''; applyFilters()"><X :size="10" /></button>
                        </span>
                        <span v-if="category" class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
                            {{ categories.find(c => c.slug === category)?.name }}
                            <button @click="category = ''; applyFilters()"><X :size="10" /></button>
                        </span>
                        <span v-if="minPrice || maxPrice" class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
                            {{ minPrice ? `${minPrice}€` : '0€' }} — {{ maxPrice ? `${maxPrice}€` : '∞' }}
                            <button @click="minPrice = ''; maxPrice = ''; applyFilters()"><X :size="10" /></button>
                        </span>
                    </div>

                    <!-- Grid productos -->
                    <div v-if="products.data.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        <Link
                            v-for="product in products.data"
                            :key="product.id"
                            :href="route('products.show', product.slug)"
                            class="group cursor-pointer"
                        >
                            <div class="aspect-square rounded-2xl bg-gray-50 overflow-hidden mb-4 border border-gray-100 group-hover:border-gray-200 transition-all duration-300">
                                <img
                                    v-if="product.image"
                                    :src="`/storage/${product.image}`"
                                    :alt="product.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-4xl text-gray-200">◆</span>
                                </div>
                            </div>
                            <div class="px-1">
                                <p class="text-xs text-gray-400 mb-1">{{ product.category?.name }}</p>
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                                    <p class="text-sm font-semibold text-gray-900 shrink-0">{{ formatCurrency(product.price) }}</p>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span v-if="product.stock > 0" class="text-xs text-emerald-500 font-medium">En stock</span>
                                    <span v-else class="text-xs text-rose-400 font-medium">Agotado</span>
                                    <ArrowRight :size="12" class="text-gray-300 group-hover:text-gray-600 transition-colors" />
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Sin resultados -->
                    <div v-else class="py-32 text-center">
                        <p class="text-4xl mb-4">◆</p>
                        <p class="text-sm font-medium text-gray-900 mb-1">Sin resultados</p>
                        <p class="text-xs text-gray-400 mb-6">Prueba con otros filtros o términos de búsqueda</p>
                        <button @click="clearFilters" class="text-xs font-medium text-gray-900 border border-gray-200 px-6 py-2.5 rounded-full hover:border-gray-400 transition-colors">
                            Limpiar filtros
                        </button>
                    </div>

                    <!-- Paginación -->
                    <div v-if="products.last_page > 1" class="flex items-center justify-center gap-2 mt-16">
                        <Link
                            v-for="link in products.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            :class="[
                                'text-xs font-medium px-4 py-2 rounded-full transition-colors',
                                link.active ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900 border border-gray-200 hover:border-gray-400',
                                !link.url ? 'opacity-30 pointer-events-none' : ''
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- Filtros móvil overlay -->
            <div v-if="mobileFilters" class="lg:hidden fixed inset-0 z-50 bg-black/50" @click="mobileFilters = false">
                <div class="absolute right-0 top-0 bottom-0 w-72 bg-white p-6 overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between mb-8">
                        <p class="text-sm font-semibold text-gray-900">Filtros</p>
                        <button @click="mobileFilters = false"><X :size="18" class="text-gray-400" /></button>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Buscar</p>
                            <input v-model="search" type="text" placeholder="Buscar..."
                                class="w-full px-3 py-2.5 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-gray-400" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Categoría</p>
                            <div class="space-y-1">
                                <button @click="category = ''" :class="!category ? 'bg-gray-900 text-white' : 'text-gray-500'"
                                    class="w-full text-left text-xs font-medium px-3 py-2 rounded-xl">Todas</button>
                                <button v-for="cat in categories" :key="cat.id" @click="category = cat.slug"
                                    :class="category === cat.slug ? 'bg-gray-900 text-white' : 'text-gray-500'"
                                    class="w-full text-left text-xs font-medium px-3 py-2 rounded-xl">{{ cat.name }}</button>
                            </div>
                        </div>
                        <button @click="applyFilters(); mobileFilters = false"
                            class="w-full text-xs font-medium bg-gray-900 text-white py-3 rounded-full">
                            Aplicar filtros
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </ShopLayout>
</template>