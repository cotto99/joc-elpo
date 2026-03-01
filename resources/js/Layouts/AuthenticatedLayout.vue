<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = ref(false)
const page = usePage()

const nav = [
    { name: 'Dashboard',        href: route('dashboard'),              icon: '📊' },
    { name: 'Ventas',           href: route('ventas.index'),           icon: '💧' },
    { name: 'Clientes',         href: route('clientes.index'),         icon: '👥' },
    { name: 'Productos',        href: route('productos.index'),        icon: '📦' },
    { name: 'Créditos',         href: route('creditos.index'),         icon: '💳' },
    { name: 'Gastos Fijos',     href: route('gastos-fijos.index'),     icon: '🔒' },
    { name: 'Gastos Variables', href: route('gastos-variables.index'), icon: '📝' },
    { name: 'Repartos',         href: route('repartos.index'),         icon: '🤝' },
]

function isActive(href) {
    try {
        return page.url.startsWith(new URL(href).pathname)
    } catch {
        return false
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">

        <!-- ===== TOPBAR MÓVIL ===== -->
        <header class="lg:hidden bg-blue-900 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-lg">
            <span class="text-lg font-bold">💧 JOC-ELPO</span>
            <div class="flex items-center gap-3">
                <Link :href="route('logout')" method="post" as="button"
                      class="text-xs text-blue-300 hover:text-white">
                    Salir
                </Link>
                <button @click="sidebarOpen = !sidebarOpen"
                        class="text-white text-2xl focus:outline-none">
                    {{ sidebarOpen ? '✕' : '☰' }}
                </button>
            </div>
        </header>

        <!-- ===== DRAWER MÓVIL ===== -->
        <transition name="slide">
            <div v-if="sidebarOpen"
                 class="lg:hidden fixed inset-0 z-30 flex">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/50" @click="sidebarOpen = false"></div>
                <!-- Menu -->
                <nav class="relative bg-blue-900 text-white w-72 h-full overflow-y-auto z-40 pt-4">
                    <div class="px-4 pb-4 border-b border-blue-700 mb-2">
                        <p class="text-xs text-blue-300">Bienvenido,</p>
                        <p class="font-semibold">{{ page.props.auth.user.name }}</p>
                    </div>
                    <Link v-for="item in nav" :key="item.name"
                          :href="item.href"
                          @click="sidebarOpen = false"
                          class="flex items-center gap-4 px-5 py-4 hover:bg-blue-700 transition-colors text-base"
                          :class="{ 'bg-blue-700 border-l-4 border-blue-300': isActive(item.href) }">
                        <span class="text-2xl">{{ item.icon }}</span>
                        <span class="font-medium">{{ item.name }}</span>
                    </Link>
                </nav>
            </div>
        </transition>

        <!-- ===== LAYOUT DESKTOP ===== -->
        <div class="hidden lg:flex min-h-screen">
            <!-- Sidebar desktop -->
            <aside class="w-64 bg-blue-900 text-white flex flex-col min-h-screen sticky top-0">
                <div class="flex items-center justify-between p-4 border-b border-blue-700">
                    <span class="text-xl font-bold tracking-wide">💧 JOC-ELPO</span>
                </div>
                <nav class="flex-1 py-4">
                    <Link v-for="item in nav" :key="item.name"
                          :href="item.href"
                          class="flex items-center gap-3 px-4 py-3 hover:bg-blue-700 transition-colors"
                          :class="{ 'bg-blue-700 border-l-4 border-blue-300': isActive(item.href) }">
                        <span class="text-xl">{{ item.icon }}</span>
                        <span class="text-sm font-medium">{{ item.name }}</span>
                    </Link>
                </nav>
                <div class="p-4 border-t border-blue-700 text-xs text-blue-300">
                    {{ page.props.auth.user.name }}
                </div>
            </aside>

            <!-- Contenido desktop -->
            <div class="flex-1 flex flex-col">
                <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                    <slot name="header" />
                    <Link :href="route('logout')" method="post" as="button"
                          class="text-sm text-gray-500 hover:text-red-500">
                        Cerrar sesión
                    </Link>
                </header>
                <main class="flex-1 p-6">
                    <slot />
                </main>
            </div>
        </div>

        <!-- ===== CONTENIDO MÓVIL ===== -->
        <main class="lg:hidden p-4 pb-6">
            <slot />
        </main>

    </div>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active {
    transition: opacity 0.2s ease;
}
.slide-enter-from, .slide-leave-to {
    opacity: 0;
}
</style>