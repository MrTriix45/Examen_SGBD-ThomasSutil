<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({ my_user: Object })
const page = usePage()
const flash = computed(() => page.props.flash)

const form = useForm({
    name: props.my_user.name,
    email: props.my_user.email, 
    user_info: props.my_user.user_info ?? '',
})

const submit = () => {
    form.put('/user/' + props.my_user.id)
}
</script>

<template>
    <div class="min-h-full bg-gradient-to-br from-yellow-50 via-orange-50 to-yellow-100 text-slate-800">
        <div class="mx-auto flex max-w-5xl flex-col gap-6 px-6 py-8">

            <!-- En-tête -->
            <div class="rounded-xl border-4 border-dashed border-orange-400 bg-white p-4 shadow-lg">
                <h1 class="mb-1 text-4xl font-black uppercase tracking-widest text-orange-600">
                    👷 Ma Boîte à Tartine 👷
                </h1>
                <p class="text-sm font-bold text-yellow-700">
                    ⚠️ ATTENTION — Les tartines faites par Maria sont précieuses. Alors déconne pas Patron. ⚠️
                </p>
            </div>

            <!-- Infos utilisateur -->
            <div class="rounded-xl border-4 border-blue-600 bg-white p-6 shadow-2xl">
                <h2 class="mb-4 text-lg font-black uppercase tracking-widest text-blue-700">
                    🪪 Les différentes Tartines
                </h2>

                <!-- Formulaire de modification -->
                <div class="mt-6 space-y-3">
                    <!-- NOM -->
                    <div class="flex items-center gap-3 rounded-lg border-2 border-blue-200 bg-blue-50 px-4 py-3">
                        <span class="w-28 text-xs font-black uppercase tracking-widest text-blue-500">👷 Nom</span>
                        <input type="text" class="font-medium text-slate-800" v-model="form.name">
                    </div>
                    <!-- MAIL -->
                    <div class="flex items-center gap-3 rounded-lg border-2 border-blue-200 bg-blue-50 px-4 py-3">
                        <span class="w-28 text-xs font-black uppercase tracking-widest text-blue-500">📧 Email</span>
                        <input type="text" class="font-medium text-slate-800" v-model="form.email">
                    </div>
                    <!-- INFO -->
                    <div class="flex items-center gap-3 rounded-lg border-2 border-blue-200 bg-blue-50 px-4 py-3">
                        <span class="w-28 text-xs font-black uppercase tracking-widest text-blue-500">📝 Info</span>
                        <input type="text" class="font-medium text-slate-800" v-model="form.user_info">
                    </div>
                    <!-- CREATED_AT & UPDATED_AT (non modifiables) -->
                    <div class="flex items-center gap-3 rounded-lg border-2 border-yellow-200 bg-yellow-50 px-4 py-3">
                        <span class="w-28 text-xs font-black uppercase tracking-widest text-yellow-600">📅 Créé le</span>
                        <span class="font-medium text-slate-800">{{ props.my_user.created_at }}</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border-2 border-yellow-200 bg-yellow-50 px-4 py-3">
                        <span class="w-28 text-xs font-black uppercase tracking-widest text-yellow-600">🔄 Modifié</span>
                        <span class="font-medium text-slate-800">{{ props.my_user.updated_at }}</span>
                    </div>
                    <button type="button" class="mt-4 rounded-lg border-2 border-blue-400 bg-blue-500 text-white" @click="submit">
                        Enregistrer
                    </button>
                </div>
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