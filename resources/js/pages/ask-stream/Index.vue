<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { useStream } from '@laravel/stream-vue';
import { router } from '@inertiajs/vue3';
import MarkdownIt from 'markdown-it';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';

const md = new MarkdownIt({
    highlight(str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try { return hljs.highlight(str, { language: lang }).value; } catch (e) {}
        }
        return '';
    },
});
const renderMarkdown = (content) => md.render(content);

const props = defineProps({
    models: Array,
    selectedModel: String,
    selectedConversationId: [Number, String],
    messages: Array,
});

const message = ref('');
const model = ref(props.selectedModel ?? '');

// Messages affichés (historique DB + optimiste pendant le stream)
const liveMessages = ref([...(props.messages ?? [])]);
const currentConversationId = ref(props.selectedConversationId ?? null);
const messagesEnd = ref(null);

// Quand la prop messages change (rechargement Inertia), on resynchronise
watch(() => props.messages, (m) => {
    liveMessages.value = [...(m ?? [])];
});
watch(() => props.selectedConversationId, (id) => {
    currentConversationId.value = id ?? null;
});

// Scroll auto vers le bas
const scrollToBottom = async () => {
    await nextTick();
    messagesEnd.value?.scrollIntoView({ behavior: 'smooth' });
};

const { data, isFetching, isStreaming, send, cancel } = useStream('/ask-stream', {
    onData: () => scrollToBottom(),
    onFinish: () => {
        // 1. Récupère l'id de conversation depuis le flux (au cas où c'est une nouvelle conv)
        const match = data.value?.match(/\[CONVID\](\d+)\[\/CONVID\]/);
        if (match) currentConversationId.value = Number(match[1]);

        // 2. Ajoute la réponse de Manuel à l'affichage pour qu'elle NE disparaisse PAS
        const content = streamedContent.value;
        if (content) {
            liveMessages.value.push({
                id: `assistant-${Date.now()}`,
                role: 'assistant',
                content,
            });
        }

        message.value = '';
        scrollToBottom();

        // 3. Rafraîchit juste la sidebar (liste des conversations), sans toucher aux messages affichés
        if (currentConversationId.value) {
            router.reload({ only: ['conversations'] });
        }
    },
    onError: (err) => console.error('Erreur streaming:', err),
});

// Récupère l'id de conversation envoyé dans le flux
watch(data, (val) => {
    const m = val?.match(/\[CONVID\](\d+)\[\/CONVID\]/);
    if (m) currentConversationId.value = Number(m[1]);
});

// Texte propre (sans les marqueurs techniques)
const streamedContent = computed(() => {
    if (!data.value) return '';
    return data.value
        .replace(/\[CONVID\]\d+\[\/CONVID\]/g, '')
        .replace(/\[REASONING\][\s\S]*?\[\/REASONING\]/g, '')
        .trim();
});

const streamedReasoning = computed(() => {
    if (!data.value) return '';
    const matches = data.value.match(/\[REASONING\]([\s\S]*?)\[\/REASONING\]/g);
    if (!matches) return '';
    return matches.map(m => m.replace(/\[REASONING\]|\[\/REASONING\]/g, '')).join('');
});

const submit = () => {
    if (!message.value.trim() || isStreaming.value) return;

    // Affichage optimiste du message utilisateur
    liveMessages.value.push({
        id: `tmp-${Date.now()}`,
        role: 'user',
        content: message.value,
    });
    scrollToBottom();

    send({
        message: message.value,
        model: model.value,
        conversation_id: currentConversationId.value,
    });
};
</script>

<template>
    <div class="min-h-full bg-gradient-to-br from-yellow-50 via-orange-50 to-yellow-100 text-slate-800">
        <div class="mx-auto flex max-w-5xl flex-col gap-6 px-6 py-8">

            <!-- En-tête -->
            <div class="rounded-xl border-4 border-dashed border-orange-400 bg-white p-4 shadow-lg">
                <h1 class="mb-1 text-4xl font-black uppercase tracking-widest text-orange-600">
                    🏗️ CHANTIER DE L'IA 🏗️
                </h1>
                <p class="text-sm font-bold text-yellow-700">
                    ⚠️ ATTENTION — Chantier en cours. Sardines non incluses. Fado possible à tout moment. ⚠️
                </p>
            </div>

            <!-- Messages -->
            <div
                v-if="liveMessages.length > 0 || isStreaming || isFetching"
                class="rounded-xl border-4 border-blue-600 bg-white p-4 shadow-2xl"
            >
                <h2 class="mb-4 text-lg font-black uppercase tracking-widest text-blue-700">
                    🗣️ Conversation en cours, mon vieux !
                </h2>

                <div class="space-y-3">
                    <!-- Historique -->
                    <div
                        v-for="m in liveMessages"
                        :key="m.id"
                        class="rounded-lg border-2 px-4 py-3"
                        :class="m.role === 'user'
                            ? 'ml-8 border-orange-400 bg-orange-50'
                            : 'mr-8 border-blue-500 bg-blue-50'"
                    >
                        <div
                            class="mb-1 text-xs font-black uppercase tracking-widest"
                            :class="m.role === 'user' ? 'text-orange-600' : 'text-blue-700'"
                        >
                            {{ m.role === 'user' ? '👷 Le Chef' : '🤖 Manuel La Truelle' }}
                        </div>
                        <div
                            v-if="m.role === 'assistant'"
                            class="prose prose-sm max-w-none text-sm leading-6 text-slate-800"
                            v-html="renderMarkdown(m.content)"
                        />
                        <div v-else class="whitespace-pre-wrap text-sm leading-6 text-slate-800">
                            {{ m.content }}
                        </div>
                    </div>

                    <!-- Message en cours de streaming -->
                    <div
                        v-if="isStreaming || isFetching"
                        class="mr-8 rounded-lg border-2 border-blue-500 bg-blue-50 px-4 py-3"
                    >
                        <div class="mb-1 text-xs font-black uppercase tracking-widest text-blue-700">
                            🤖 Manuel La Truelle
                        </div>

                        <div
                            v-if="streamedReasoning"
                            class="mb-2 rounded border-l-4 border-purple-400 bg-purple-50 p-2 text-xs italic text-purple-700"
                        >
                            🤔 {{ streamedReasoning }}
                        </div>

                        <div class="whitespace-pre-wrap text-sm leading-6 text-slate-800">{{ streamedContent }}</div>

                        <div v-if="isFetching" class="mt-1 text-xs font-bold text-blue-400">⏳ Connexion...</div>
                        <div v-else class="mt-1 animate-pulse text-xs font-bold text-orange-500">✍️ écrit...</div>
                    </div>
                </div>
                <div ref="messagesEnd" />
            </div>

            <!-- Formulaire -->
            <form
                class="space-y-4 rounded-xl border-4 border-blue-600 bg-white p-4 shadow-2xl"
                @submit.prevent="submit"
            >
                <div>
                    <label class="mb-2 block font-black uppercase tracking-wide text-blue-700">
                        🧱 Choisissez le modèle (avec sagesse, ami)
                    </label>
                    <select
                        v-model="model"
                        class="w-full rounded-lg border-2 border-blue-500 bg-blue-50 px-3 py-2 font-medium text-blue-900"
                    >
                        <option v-for="m in models" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block font-black uppercase tracking-wide text-blue-700">
                        🐟 Votre message (peut être en français)
                    </label>
                    <textarea
                        v-model="message"
                        rows="5"
                        :disabled="isStreaming"
                        class="w-full rounded-lg border-2 border-blue-500 bg-blue-50 px-3 py-2 text-slate-800"
                        placeholder="Écrivez ici, chef..."
                    />
                </div>

                <button
                    v-if="!isStreaming"
                    type="submit"
                    class="rounded-lg border-4 border-orange-500 bg-orange-400 px-6 py-3 text-lg font-black uppercase tracking-widest text-white hover:bg-orange-500"
                >
                    🚀 ENVOYER, ALLEZ !
                </button>
                <button
                    v-else
                    type="button"
                    @click="cancel"
                    class="rounded-lg border-4 border-red-500 bg-red-400 px-6 py-3 text-lg font-black uppercase tracking-widest text-white hover:bg-red-500"
                >
                    🛑 STOP
                </button>
            </form>

        </div>
    </div>
</template>