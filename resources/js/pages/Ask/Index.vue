<script setup>
import { useForm } from '@inertiajs/vue3'
import { ask } from '@/actions/App/Http/Controllers/AskController'
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
        return ''; // use external default escaping
    }
});

const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
})

const form = useForm({
    message: props.message ?? '',
    model: props.selectedModel,
})

const submit = () => {
    form.post(ask())
}
</script>
<template>
    <div class="flex flex-col gap-4">
        <form @submit.prevent="submit" class="flex flex-col gap-4">
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
            <textarea v-model="form.message" placeholder="Posez votre question ici..." class="p-2 border rounded h-32"></textarea>
            <button type="submit" class="bg-blue-500 text-white p-2 rounded">Envoyer</button>
        </form>

        <div v-if="props.response" class="p-4 border rounded bg-gray-100">
            <h3 class="font-bold mb-2">Réponse :</h3>
            <div v-html="md.render(props.response)"></div>
        </div>

        <div v-if="props.error" class="p-4 border rounded bg-red-100 text-red-700">
            {{ props.error }}
        </div>
    </div>
</template>