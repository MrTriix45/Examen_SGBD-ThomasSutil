<script setup>
import { Link, usePage } from '@inertiajs/vue3';

defineProps({
    stats: { type: Array, default: () => [] },
});

</script>

<template>
    <div class="min-h-full bg-gradient-to-br from-yellow-50 via-orange-50 to-yellow-100 text-slate-800">
        <div class="mx-auto flex max-w-5xl flex-col gap-6 px-6 py-8">

            <!-- En-tête -->
            <div class="rounded-xl border-4 border-dashed border-orange-400 bg-white p-4 shadow-lg">
                <h1 class="mb-1 text-4xl font-black uppercase tracking-widest text-orange-600">
                    👷 Mes Petites Statistiques 👷
                </h1>
            </div>

            <!-- Infos utilisateur -->
            <div class="rounded-xl border-4 border-blue-600 bg-white p-6 shadow-2xl">
                <h2 class="mb-4 text-lg font-black uppercase tracking-widest text-blue-700">
                    🪪 Statistiques
                </h2>

                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-blue-100 text-left text-sm font-semibold text-blue-700">
                            <th class="border-b border-blue-300 px-4 py-2">Conversation ID</th>
                            <th class="border-b border-blue-300 px-4 py-2">Titre Conversation</th>
                            <th class="border-b border-blue-300 px-4 py-2">Total de Tokens</th>
                            <th class="border-b border-blue-300 px-4 py-2">Coût Total (USD)</th>
                            <th class="border-b border-blue-300 px-4 py-2">Nombre de Requêtes</th>
                            <th class="border-b border-blue-300 px-4 py-2">Latence Moyenne (ms)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in stats"
                            :key="row.conversation_id"
                            class="text-sm text-gray-700"
                        >
                            <td class="border-b border-blue-300 px-4 py-2">
                                <Link
                                    :href="`/ask?conversation_id=${row.conversation_id}`"
                                    class="font-bold text-orange-600 hover:underline"
                                >
                                    {{ row.conversation_id }}
                                </Link>
                            </td>
                            <td class="border-b border-blue-300 px-4 py-2">{{ row.conversation_title ?? '(sans titre)' }}</td>
                            <td class="border-b border-blue-300 px-4 py-2">{{ Number(row.total_tokens) }}</td>
                            <td class="border-b border-blue-300 px-4 py-2">${{ Number(row.total_cost).toFixed(4) }}</td>
                            <td class="border-b border-blue-300 px-4 py-2">{{ row.total_requests }}</td>
                            <td class="border-b border-blue-300 px-4 py-2">{{ Number(row.average_latency).toFixed(0) }} ms</td>
                        </tr>
                        <tr v-if="stats.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                Aucune conversation enregistrée.
                            </td>
                        </tr>
                    </tbody>
                </table>
                
            </div>

            <!-- Flash message -->
            <div
                v-if="flash?.success"
                class="rounded-lg border-2 border-green-400 bg-green-50 px-4 py-3 text-sm font-bold text-green-700"
            >
                ✅ {{ flash.success }}
            </div>

        </div>
    </div>
</template>