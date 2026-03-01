<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    mes: String,
    ingresos: Number,
    gastosFijos: Number,
    gastosVariables: Number,
    cajaChica: Number,
    utilidad: Number,
    creditosPendientes: Number,
    totalViajes: Number,
    repartoMes: Object,
    historico: Array,
})

const mesSeleccionado = ref(props.mes)

function cambiarMes() {
    router.get(route('dashboard'), { mes: mesSeleccionado.value }, { preserveState: true })
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-blue-900">📊 Dashboard</h1>
        </template>

        <!-- Selector de mes -->
        <div class="mb-6 flex items-center gap-3">
            <label class="text-sm font-medium text-gray-600">Mes:</label>
            <input type="month" v-model="mesSeleccionado" @change="cambiarMes"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Tarjetas principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Ingresos del mes</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">{{ fmt(ingresos) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ totalViajes }} viajes</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-400">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Gastos Fijos</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ fmt(gastosFijos) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-orange-400">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Gastos Variables</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">{{ fmt(gastosVariables) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-400">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Caja Chica</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ fmt(cajaChica) }}</p>
            </div>
        </div>

        <!-- Utilidad neta + créditos pendientes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div :class="utilidad >= 0 ? 'border-green-500' : 'border-red-500'"
                 class="bg-white rounded-xl shadow p-6 border-l-4">
                <p class="text-sm text-gray-500 uppercase tracking-wide font-medium">💰 Utilidad Neta</p>
                <p :class="utilidad >= 0 ? 'text-green-600' : 'text-red-600'"
                   class="text-4xl font-bold mt-2">{{ fmt(utilidad) }}</p>
                <p class="text-xs text-gray-400 mt-2">Ingresos − Gastos Fijos − Gastos Variables − Caja Chica</p>

                <!-- Si hay reparto -->
                <div v-if="repartoMes" class="mt-4 p-3 bg-green-50 rounded-lg text-sm">
                    <p class="font-semibold text-green-700">✅ Reparto realizado</p>
                    <p class="text-green-600">{{ fmt(repartoMes.monto_por_socio) }} por socio</p>
                </div>
                <div v-else class="mt-4 p-3 bg-yellow-50 rounded-lg text-sm">
                    <p class="text-yellow-700">⏳ Reparto pendiente este mes</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-400">
                <p class="text-sm text-gray-500 uppercase tracking-wide font-medium">💳 Créditos Pendientes</p>
                <p class="text-4xl font-bold text-yellow-600 mt-2">{{ fmt(creditosPendientes) }}</p>
                <p class="text-xs text-gray-400 mt-2">Monto total por cobrar</p>
            </div>
        </div>

        <!-- Histórico últimos 6 meses -->
<div class="bg-white rounded-xl shadow p-4 lg:p-6">
    <h2 class="text-base lg:text-lg font-bold text-gray-700 mb-4">📈 Histórico últimos 6 meses</h2>
    <!-- Tarjetas en móvil -->
    <div class="grid grid-cols-1 gap-3 lg:hidden">
        <div v-for="h in historico" :key="h.mes"
             class="border rounded-lg p-3 flex justify-between items-center">
            <span class="font-bold text-gray-700">{{ h.mes }}</span>
            <div class="text-right">
                <p class="text-xs text-blue-600">Ing: {{ fmt(h.ingresos) }}</p>
                <p class="text-xs text-red-500">Gas: {{ fmt(h.gastos) }}</p>
                <p class="text-sm font-bold" :class="h.utilidad >= 0 ? 'text-green-600' : 'text-red-600'">
                    {{ fmt(h.utilidad) }}
                </p>
            </div>
        </div>
    </div>
    <!-- Tabla en desktop -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Mes</th>
                    <th class="pb-2 text-right">Ingresos</th>
                    <th class="pb-2 text-right">Gastos</th>
                    <th class="pb-2 text-right">Utilidad</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="h in historico" :key="h.mes" class="border-b last:border-0">
                    <td class="py-2 font-medium">{{ h.mes }}</td>
                    <td class="py-2 text-right text-blue-600">{{ fmt(h.ingresos) }}</td>
                    <td class="py-2 text-right text-red-500">{{ fmt(h.gastos) }}</td>
                    <td class="py-2 text-right font-bold"
                        :class="h.utilidad >= 0 ? 'text-green-600' : 'text-red-600'">
                        {{ fmt(h.utilidad) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    </AuthenticatedLayout>
</template>