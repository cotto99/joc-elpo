<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Head } from '@inertiajs/vue3'

const props = defineProps({ repartos: Array })

const form = useForm({
    mes_anio: new Date().toISOString().slice(0, 7),
    socio_1: '',
    socio_2: '',
})

function guardar() {
    form.post(route('repartos.store'), { onSuccess: () => form.reset() })
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}
</script>

<template>
    <Head title="Repartos" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-blue-900">🤝 Repartos Mensuales</h1>
        </template>

        <!-- Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex gap-3 items-start">
            <span class="text-2xl">ℹ️</span>
            <div>
                <p class="font-semibold text-blue-800">Reparto automático del 50% para cada socio</p>
                <p class="text-sm text-blue-700 mt-1">
                    El sistema calcula automáticamente la utilidad neta del mes seleccionado
                    (Ingresos − Gastos Fijos − Gastos Variables − Caja Chica) y la divide en partes iguales.
                </p>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Realizar Reparto del Mes</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Mes a repartir *</label>
                    <input v-model="form.mes_anio" type="month"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    <p v-if="form.errors.mes_anio" class="text-red-500 text-xs mt-1">{{ form.errors.mes_anio }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre Socio 1 *</label>
                    <input v-model="form.socio_1" type="text" placeholder="Ej: Juan"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    <p v-if="form.errors.socio_1" class="text-red-500 text-xs mt-1">{{ form.errors.socio_1 }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre Socio 2 *</label>
                    <input v-model="form.socio_2" type="text" placeholder="Ej: Pedro"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    <p v-if="form.errors.socio_2" class="text-red-500 text-xs mt-1">{{ form.errors.socio_2 }}</p>
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                🤝 Calcular y Registrar Reparto
            </button>
        </div>

        <!-- Historial -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-bold text-gray-700">📋 Historial de Repartos</h2>
            </div>
                <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Mes</th>
                        <th class="text-right px-4 py-3">Utilidad Base</th>
                        <th class="text-right px-4 py-3">Total Repartido</th>
                        <th class="text-left px-4 py-3">Socio 1</th>
                        <th class="text-right px-4 py-3">Monto S1</th>
                        <th class="text-left px-4 py-3">Socio 2</th>
                        <th class="text-right px-4 py-3">Monto S2</th>
                        <th class="text-center px-4 py-3">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in repartos" :key="r.id" class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-bold text-blue-700">{{ r.mes_anio }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ fmt(r.utilidad_base) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold">{{ fmt(r.total_repartido) }}</td>
                        <td class="px-4 py-3">{{ r.socio_1 }}</td>
                        <td class="px-4 py-3 text-right font-mono text-green-600 font-bold">{{ fmt(r.monto_por_socio) }}</td>
                        <td class="px-4 py-3">{{ r.socio_2 }}</td>
                        <td class="px-4 py-3 text-right font-mono text-green-600 font-bold">{{ fmt(r.monto_por_socio) }}</td>
                        <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ r.created_at }}</td>
                    </tr>
                    <tr v-if="!repartos?.length">
                        <td colspan="8" class="text-center py-8 text-gray-400">No hay repartos registrados aún</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>