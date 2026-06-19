<script setup>
import { computed, ref, watch, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';

import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'

const md = new MarkdownIt({
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(str, { language: lang }).value;
            } catch (__) {}
        }
        return '';
    }
});

const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    conversations: Array,
    selectedConversationId: [Number, String],
    messages: Array,
    error: String,
});

const form = useForm({
    message: props.message ?? '',
    model: props.selectedModel ?? '',
    conversation_id: props.selectedConversationId ?? null,
});

const hasMessages = computed(() => (props.messages?.length ?? 0) > 0);
const messagesEnd = ref(null);

watch(
    () => props.messages,
    async () => {
        await nextTick();
        if (messagesEnd.value) {
            messagesEnd.value.scrollIntoView({ behavior: 'smooth' });
        }
    },
    { deep: true }
);

watch(
    () => props.selectedConversationId,
    (newId) => {
        form.conversation_id = newId ?? null;
    }
);

const submit = () => {
    form.post('/ask');
};

const renderMarkdown = (content) => md.render(content);
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
                v-if="hasMessages"
                class="rounded-xl border-4 border-blue-600 bg-white p-4 shadow-2xl"
            >
                <h2 class="mb-4 text-lg font-black uppercase tracking-widest text-blue-700">
                    🗣️ Conversation en cours, mon vieux !
                </h2>

                <div class="space-y-3">
                    <div
                        v-for="message in props.messages"
                        :key="message.id"
                        class="rounded-lg border-2 px-4 py-3"
                        :class="message.role === 'user'
                            ? 'ml-8 border-orange-400 bg-orange-50'
                            : 'mr-8 border-blue-500 bg-blue-50'"
                    >
                        <div
                            class="mb-1 text-xs font-black uppercase tracking-widest"
                            :class="message.role === 'user' ? 'text-orange-600' : 'text-blue-700'"
                        >
                            {{ message.role === 'user' ? '👷 Le Chef' : '🤖 Manuel La Truelle' }}
                        </div>
                        <div
                            v-if="message.role === 'assistant'"
                            class="prose prose-sm max-w-none text-sm leading-6 text-slate-800"
                            v-html="renderMarkdown(message.content)"
                        />
                        <div v-else class="whitespace-pre-wrap text-sm leading-6 text-slate-800">
                            {{ message.content }}
                        </div>
                    </div>
                </div>
                <div ref="messagesEnd" />
            </div>

            <!-- Formulaire -->
            <form
                class="space-y-4 rounded-xl border-4 border-blue-600 bg-white p-4 shadow-2xl"
                @submit.prevent="submit"
            >
                <div class="flex items-center gap-2 rounded-lg bg-yellow-400 px-3 py-2">
                    <span class="text-sm font-black uppercase text-yellow-900">🔨 Zone de construction — touchez avec précaution</span>
                </div>

                <input
                    v-model="form.conversation_id"
                    type="hidden"
                >

                <div>
                    <label class="mb-2 block font-black uppercase tracking-wide text-blue-700">
                        🧱 Choisissez le modèle (avec sagesse, ami)
                    </label>

                    <select
                        v-model="form.model"
                        class="w-full rounded-lg border-2 border-blue-500 bg-blue-50 px-3 py-2 font-medium text-blue-900 shadow-inner outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-400"
                    >
                        <option
                            v-for="model in props.models"
                            :key="model.id"
                            :value="model.id"
                        >
                            {{ model.name }} (Prompt: {{ model.prompt_price }} $/1K tokens, Completion: {{ model.completion_price }} $/1K tokens)
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block font-black uppercase tracking-wide text-blue-700">
                        🐟 Votre message (peut être en français)
                    </label>

                    <textarea
                        v-model="form.message"
                        rows="8"
                        class="w-full rounded-lg border-2 border-blue-500 bg-blue-50 px-3 py-2 text-slate-800 placeholder:text-blue-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-400"
                        placeholder="Écrivez ici, chef... l'IA attend avec un café."
                    />
                </div>

                <div v-if="form.processing" class="mr-8 flex items-center gap-2 rounded-lg border-2 border-blue-500 bg-blue-50 px-4 py-3">
                    <span class="text-xs font-black uppercase text-blue-700">🤖 Manuel La Truelle réfléchit</span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-blue-500 [animation-delay:0ms]" />
                    <span class="h-2 w-2 animate-bounce rounded-full bg-blue-500 [animation-delay:150ms]" />
                    <span class="h-2 w-2 animate-bounce rounded-full bg-blue-500 [animation-delay:300ms]" />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg border-4 border-orange-500 bg-orange-400 px-6 py-3 text-lg font-black uppercase tracking-widest text-white shadow-md transition hover:bg-orange-500 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ form.processing ? '⏳ Manuel La Truelle réfléchit...' : '🚀 ENVOYER, ALLEZ !' }}
                </button>
            </form>

            <!-- Erreur -->
            <div
                v-if="props.error"
                class="rounded-xl border-4 border-red-500 bg-red-50 p-4 font-bold text-red-700"
            >
                😤 ERREUR, chef ! — {{ props.error }}
            </div>

        </div>
    </div>
</template>
