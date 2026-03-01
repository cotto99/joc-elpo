<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Head } from '@inertiajs/vue3'

const props = defineProps({ gastos: Array })

const form = useForm({
    descripcion: '',
    monto: '',
    fecha: new Date().toISOString().split('T')[0],
})

function guardar() {
    form.post(route('gastos-fijos.store'), { onSuccess: () => form.reset() })
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}

const total = props.gastos?.reduce((s, g) => s + Number(g.monto), 0) || 0
</script>

<template>
    <Head title="Gastos Fijos" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-blue-900">🔒 Gastos Fijos</h1>
        </template>

        <!-- Aviso inmutabilidad -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex gap-3 items-start">
            <span class="text-2xl">⚠️</span>
            <div>
                <p class="font-semibold text-amber-800">Atención: Los gastos fijos no se pueden modificar ni eliminar</p>
                <p class="text-sm text-amber-700 mt-1">Una vez registrado, el gasto queda guardado permanentemente como respaldo contable.</p>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Registrar Gasto Fijo</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Descripción *</label>
                    <input v-model="form.descripcion" type="text"
                           placeholder="Ej: Renta del local, Salario, etc."
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    <p v-if="form.errors.descripcion" class="text-red-500 text-xs mt-1">{{ form.errors.descripcion }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Monto (GTQ) *</label>
                    <input v-model="form.monto" type="number" step="0.01" placeholder="0.00"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    <p v-if="form.errors.monto" class="text-red-500 text-xs mt-1">{{ form.errors.monto }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Fecha *</label>
                    <input v-model="form.fecha" type="date"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 text-sm font-medium disabled:opacity-50">
                🔒 Registrar Gasto Fijo
            </button>
        </div>

        <!-- Total -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex justify-between items-center">
            <span class="font-semibold text-red-700">Total registrado:</span>
            <span class="text-2xl font-bold text-red-700">{{ fmt(total) }}</span>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Descripción</th>
                        <th class="text-center px-4 py-3">Fecha</th>
                        <th class="text-center px-4 py-3">Mes</th>
                        <th class="text-right px-4 py-3">Monto</th>
                        <th class="text-center px-4 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="g in gastos" :key="g.id" class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ g.descripcion }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ g.fecha }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ g.mes_anio }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-red-600">{{ fmt(g.monto) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs">🔒 Fijo</span>
                        </td>
                    </tr>
                    <tr v-if="!gastos?.length">
                        <td colspan="5" class="text-center py-8 text-gray-400">No hay gastos fijos registrados</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>