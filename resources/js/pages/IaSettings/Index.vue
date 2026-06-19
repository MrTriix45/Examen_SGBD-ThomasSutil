<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    humour_level: number;
    sarcasm_level: number;
    pedagogy_level: number;
    patience_level: number;
    anger_level: number;
}>();

const form = useForm({
    humour_level: props.humour_level,
    sarcasm_level: props.sarcasm_level,
    pedagogy_level: props.pedagogy_level,
    patience_level: props.patience_level,
    anger_level: props.anger_level,
});

const submit = () => {
    form.post('/iasettings');
};

const levels = [
    { key: 'humour_level',   label: '😂 Humour',      description: '1 = Sérieux comme un notaire, 10 = Clown professionnel' },
    { key: 'sarcasm_level',  label: '😏 Sarcasme',    description: '1 = Diplomate pur, 10 = Ironique jusqu\'à l\'os' },
    { key: 'pedagogy_level', label: '📚 Pédagogie',   description: '1 = Répond sec, 10 = Explique tout comme à un enfant' },
    { key: 'patience_level', label: '🧘 Patience',    description: '1 = Perd vite les nerfs, 10 = Zen absolu' },
    { key: 'anger_level',    label: '🔥 Colère',      description: '1 = Calme plat, 10 = Volcan en éruption' },
] as const;

function levelLabel(val: number): string {
    if (val <= 2) return 'Très faible';
    if (val <= 4) return 'Faible';
    if (val <= 6) return 'Modéré';
    if (val <= 8) return 'Élevé';
    return 'Maximum';
}
</script>

<template>
    <Head title="Paramètres IA" />

    <div class="mx-auto max-w-2xl p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-100">⚙️ Paramètres de l'IA</h1>
            <p class="mt-1 text-sm text-slate-400">
                Ajuste la personnalité du maçon portugais selon tes envies.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div
                v-for="level in levels"
                :key="level.key"
                class="rounded-xl border border-slate-700 bg-slate-800/60 p-5"
            >
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-slate-100">{{ level.label }}</span>
                        <p class="mt-0.5 text-xs text-slate-400">{{ level.description }}</p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-2xl font-bold text-blue-400">{{ form[level.key] }}</span>
                        <span class="text-xs text-slate-400">{{ levelLabel(form[level.key]) }}</span>
                    </div>
                </div>

                <input
                    type="range"
                    min="1"
                    max="10"
                    v-model.number="form[level.key]"
                    class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-700 accent-blue-500"
                />
                <div class="mt-1 flex justify-between text-xs text-slate-500">
                    <span>1</span>
                    <span>5</span>
                    <span>10</span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p v-if="form.wasSuccessful" class="text-sm text-green-400">
                    ✅ Paramètres sauvegardés ! Força Portugal!
                </p>
                <p v-if="form.hasErrors" class="text-sm text-red-400">
                    ❌ Une erreur est survenue.
                </p>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="ml-auto rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-500 disabled:opacity-50"
                >
                    {{ form.processing ? 'Sauvegarde...' : 'Sauvegarder' }}
                </button>
            </div>
        </form>
    </div>
</template>