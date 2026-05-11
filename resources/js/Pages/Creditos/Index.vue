<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({ creditos: Array })
const clienteFiltro = ref('')
const pagarForm = useForm({
    fecha_pago: new Date().toISOString().split('T')[0],
    notas: '',
    comprobante: null,
})

const pagarModal = ref(null)
const creditoSeleccionado = ref(null)

function abrirPagar(c) {
    pagarModal.value = c.id
    creditoSeleccionado.value = c
    pagarForm.fecha_pago = new Date().toISOString().split('T')[0]
    pagarForm.notas = ''
    pagarForm.comprobante = null
}

function confirmarPago() {
    pagarForm.post(route('creditos.pagar', creditoSeleccionado.value.id), {
        forceFormData: true,
        onSuccess: () => {
            pagarModal.value = null
            creditoSeleccionado.value = null
            pagarForm.reset()
        }
    })
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}

const pendientes = computed(() => {
    return props.creditos?.filter(c => {

        const esPendiente = c.estado === 'pendiente'

        const coincideCliente =
            !clienteFiltro.value ||
            c.cliente?.nombre?.toLowerCase()
                .includes(clienteFiltro.value.toLowerCase())

        return esPendiente && coincideCliente

    }) || []
})
const pagados    = computed(() => props.creditos?.filter(c => c.estado === 'pagado') || [])
</script>

<template>
    <Head title="Créditos" />
    <AuthenticatedLayout>
        <template #header><h1 class="text-xl font-bold text-blue-900">💳 Créditos</h1></template>

        <!-- Pendientes -->
        <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b bg-yellow-50 flex items-center gap-2">
                <div class="p-4 border-b bg-white flex flex-col md:flex-row gap-3 md:items-center">

<input
    v-model="clienteFiltro"
    type="text"
    placeholder="Buscar cliente..."
    class="border rounded-lg px-4 py-2 text-sm w-full md:w-80"
/>

<a
    :href="route('creditos.pdf', { cliente: clienteFiltro })"
    target="_blank"
    class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 text-center"
>
    📄 Descargar PDF
</a>

</div>
                <span class="text-lg">⏳</span>
                <h2 class="font-bold text-yellow-800">Pendientes de cobro ({{ pendientes.length }})</h2>
                <span class="ml-auto font-bold text-yellow-700">
                    Total: {{ fmt(pendientes.reduce((s, c) => s + Number(c.monto), 0)) }}
                </span>
            </div>
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Cliente</th>
                        <th class="text-left px-4 py-3">Fecha Venta</th>
                        <th class="text-right px-4 py-3">Monto</th>
                        <th class="text-center px-4 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in pendientes" :key="c.id" class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ c.cliente?.nombre }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ c.venta?.fecha }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-yellow-700">{{ fmt(c.monto) }}</td>
                        <td class="px-4 py-3 text-center">
                            <button @click="abrirPagar(c)"
                                    class="bg-green-500 text-white px-4 py-1 rounded-lg text-xs hover:bg-green-600">
                                ✓ Marcar Pagado
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!pendientes.length">
                        <td colspan="4" class="text-center py-6 text-gray-400">¡Sin créditos pendientes! 🎉</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal pago -->
        <div v-if="pagarModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                <h3 class="font-bold text-gray-800 mb-4">Registrar Pago</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Fecha de pago *</label>
                        <input v-model="pagarForm.fecha_pago" type="date"
                               class="w-full border rounded-lg px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Comprobante (imagen/PDF)</label>
                        <input type="file" accept=".jpg,.jpeg,.png,.pdf"
                               @change="e => pagarForm.comprobante = e.target.files[0]"
                               class="w-full text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Notas</label>
                        <textarea v-model="pagarForm.notas" rows="2"
                                  class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-5">
                    <button @click="pagarModal = null"
                            class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50">
                        Cancelar
                    </button>
                   <button @click="confirmarPago()" :disabled="pagarForm.processing"
                            class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700 disabled:opacity-50">
                        ✓ Confirmar Pago
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagados -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-green-50">
                <h2 class="font-bold text-green-800">✅ Créditos Pagados ({{ pagados.length }})</h2>
            </div>
                <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Cliente</th>
                        <th class="text-left px-4 py-3">Fecha Pago</th>
                        <th class="text-right px-4 py-3">Monto</th>
                        <th class="text-center px-4 py-3">Comprobante</th>
                        <th class="text-left px-4 py-3">Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in pagados" :key="c.id" class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ c.cliente?.nombre }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ c.fecha_pago }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-green-700">{{ fmt(c.monto) }}</td>
                        <td class="px-4 py-3 text-center">
                            <a v-if="c.comprobante" :href="`/storage/${c.comprobante}`" target="_blank"
                               class="text-blue-500 hover:underline text-xs">Ver comprobante</a>
                            <span v-else class="text-gray-400 text-xs">Sin comprobante</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ c.notas || '—' }}</td>
                    </tr>
                    <tr v-if="!pagados.length">
                        <td colspan="5" class="text-center py-6 text-gray-400">Sin créditos pagados aún</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>