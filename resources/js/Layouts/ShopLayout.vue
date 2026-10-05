<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { ShoppingCart, Menu, X, User, Package } from '@lucide/vue';

const page  = usePage();
const user  = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);

const mobileOpen = ref(false);

const navLinks = [
    { name: 'Inicio',    href: route('home') },
    { name: 'Productos', href: route('products.index') },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Navbar -->
        <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    <!-- Logo -->
                    <Link :href="route('home')" class="text-xl font-bold text-gray-900 tracking-tight">
                        Shop<span class="text-indigo-600">.</span>
                    </Link>

                    <!-- Links escritorio -->
                    <div class="hidden md:flex items-center gap-8">
                        <Link
                            v-for="link in navLinks"
                            :key="link.name"
                            :href="link.href"
                            class="text-sm text-gray-500 hover:text-gray-900 transition-colors"
                        >
                            {{ link.name }}
                        </Link>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center gap-4">
                        <!-- Carrito -->
                        <Link :href="route('cart.index')" class="relative p-2 text-gray-500 hover:text-gray-900 transition-colors">
                            <ShoppingCart :size="20" />
                        </Link>

                        <!-- Usuario -->
                        <template v-if="user">
                            <Link :href="route('orders.index')" class="p-2 text-gray-500 hover:text-gray-900 transition-colors">
                                <Package :size="20" />
                            </Link>
                            <Link :href="route('profile.edit')" class="p-2 text-gray-500 hover:text-gray-900 transition-colors">
                                <User :size="20" />
                            </Link>
                            <Link
                                v-if="user.is_admin"
                                :href="route('admin.products.index')"
                                class="text-xs font-medium bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition-colors"
                            >
                                Admin
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">
                                Entrar
                            </Link>
                            <Link :href="route('register')" class="text-sm font-medium bg-gray-900 text-white px-4 py-2 rounded-xl hover:bg-gray-700 transition-colors">
                                Registrarse
                            </Link>
                        </template>

                        <!-- Menú móvil -->
                        <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-500">
                            <X v-if="mobileOpen" :size="20" />
                            <Menu v-else :size="20" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menú móvil desplegable -->
            <div v-if="mobileOpen" class="md:hidden border-t border-gray-100 bg-white px-4 py-4 space-y-2">
                <Link
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    class="block text-sm text-gray-600 hover:text-gray-900 py-2"
                    @click="mobileOpen = false"
                >
                    {{ link.name }}
                </Link>
            </div>
        </nav>

        <!-- Flash messages -->
        <div v-if="flash?.success" class="max-w-6xl mx-auto px-4 pt-4">
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-xl px-5 py-3">
                <span class="text-emerald-500">✓</span>
                <p class="text-sm font-medium text-emerald-700">{{ flash.success }}</p>
            </div>
        </div>
        <div v-if="flash?.error" class="max-w-6xl mx-auto px-4 pt-4">
            <div class="flex items-center gap-3 bg-rose-50 border border-rose-100 rounded-xl px-5 py-3">
                <span class="text-rose-500">✕</span>
                <p class="text-sm font-medium text-rose-700">{{ flash.error }}</p>
            </div>
        </div>

        <!-- Contenido -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-24 border-t border-gray-100 bg-white py-12">
            <div class="max-w-6xl mx-auto px-4 text-center">
                <p class="text-sm text-gray-400">© 2026 Shop. Todos los derechos reservados.</p>
            </div>
        </footer>

    </div>
</template>