<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    preferences: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    humour_level: props.preferences.humour_level,
    sarcasm_level: props.preferences.sarcasm_level,
    pedagogy_level: props.preferences.pedagogy_level,
    patience_level: props.preferences.patience_level,
    anger_level: props.preferences.anger_level,
    web_plugin_enabled: props.preferences.web_plugin_enabled,
});
const page = usePage()
const flash = computed(() => page.props.flash)

const submit = () => {
    form.post('/iasettings');
};

const levels = [
    { key: 'humour_level',   label: '😂 Humour',      description: '1 = Sérieux comme un notaire, 10 = Clown professionnel' },
    { key: 'sarcasm_level',  label: '😏 Sarcasme',    description: '1 = Diplomate pur, 10 = Ironique jusqu\'à l\'os' },
    { key: 'pedagogy_level', label: '📚 Pédagogie',   description: '1 = Répond sec, 10 = Explique tout comme à un enfant' },
    { key: 'patience_level', label: '🧘 Patience',    description: '1 = Perd vite les nerfs, 10 = Zen absolu' },
    { key: 'anger_level',    label: '🔥 Colère',      description: '1 = Calme plat, 10 = Volcan en éruption' }, 
];

function levelLabel(val) {
    if (val <= 2) return 'Très faible';
    if (val <= 4) return 'Faible';
    if (val <= 6) return 'Modéré';
    if (val <= 8) return 'Élevé';
    return 'Maximum';
}
</script>

<template>
    <div class="min-h-full bg-gradient-to-br from-yellow-50 via-orange-50 to-yellow-100 text-slate-800">
        <div class="mx-auto flex max-w-5xl flex-col gap-6 px-6 py-8">

            <!-- En-tête -->
            <div class="rounded-xl border-4 border-dashed border-orange-400 bg-white p-4 shadow-lg">
                <h1 class="mb-1 text-4xl font-black uppercase tracking-widest text-orange-600">
                    ⚙️ RÉGLAGES DE MANUEL ⚙️
                </h1>
                <p class="text-sm font-bold text-yellow-700">
                    ⚠️ ATTENTION — Tu touches à la personnalité du maçon portugais. Manie les curseurs avec précaution, chef. ⚠️
                </p>
            </div>

            <!-- Formulaire de réglages -->
            <form
                class="space-y-4 rounded-xl border-4 border-blue-600 bg-white p-6 shadow-2xl"
                @submit.prevent="submit"
            >
                <h2 class="mb-2 text-lg font-black uppercase tracking-widest text-blue-700">
                    🛠️ Curseurs de personnalité
                </h2>

                <!-- Bandeau "zone de chantier" -->
                <div class="flex items-center gap-2 rounded-lg bg-yellow-400 px-3 py-2">
                    <span class="text-sm font-black uppercase text-yellow-900">
                        🚧 Zone de réglage — Manuel obéit à ces niveaux 🚧
                    </span>
                </div>

                <!-- Sliders 1-10 -->
                <div
                    v-for="level in levels"
                    :key="level.key"
                    class="rounded-lg border-2 border-blue-200 bg-blue-50 px-4 py-3"
                >
                    <div class="mb-2 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <span class="block text-sm font-black uppercase tracking-widest text-blue-700">
                                {{ level.label }}
                            </span>
                            <p class="mt-0.5 text-xs font-medium text-blue-500">
                                {{ level.description }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-2xl font-black text-orange-600">{{ form[level.key] }}</span>
                            <span class="text-xs font-bold uppercase text-yellow-700">
                                {{ levelLabel(form[level.key]) }}
                            </span>
                        </div>
                    </div>

                    <input
                        type="range"
                        min="1"
                        max="10"
                        v-model.number="form[level.key]"
                        class="h-2 w-full cursor-pointer appearance-none rounded-full bg-blue-200 accent-orange-500"
                    />
                    <div class="mt-1 flex justify-between text-xs font-bold text-blue-400">
                        <span>1</span>
                        <span>5</span>
                        <span>10</span>
                    </div>
                </div>

                <!-- Toggle Recherche web -->
                <label class="flex cursor-pointer items-center justify-between gap-3 rounded-lg border-2 border-blue-200 bg-blue-50 px-4 py-3">
                    <div class="flex-1">
                        <span class="block text-sm font-black uppercase tracking-widest text-blue-700">
                            🌐 Recherche web
                        </span>
                        <p class="mt-0.5 text-xs font-medium text-blue-500">
                            Laisse Manuel fouiller sur la toile avant de répondre (ça va coûter sur la facture, chef).
                        </p>
                    </div>
                    <input
                        type="checkbox"
                        v-model="form.web_plugin_enabled"
                        class="h-6 w-6 accent-orange-500"
                    />
                </label>

                <!-- Bouton Sauvegarder -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg border-4 border-orange-500 bg-orange-400 px-6 py-3 text-lg font-black uppercase tracking-widest text-white shadow-md transition hover:bg-orange-500 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ form.processing ? '⏳ Sauvegarde en cours...' : '🚀 SAUVEGARDER, ALLEZ !' }}
                </button>
            </form>

            <!-- Flash message -->
            <div
                v-if="flash?.success"
                class="rounded-xl border-4 border-green-500 bg-green-50 p-4 font-bold text-green-700"
            >
                ✅ {{ flash.success }}
            </div>

        </div>
    </div>
</template>