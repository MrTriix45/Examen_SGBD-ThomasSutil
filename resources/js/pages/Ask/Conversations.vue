<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({
    conversations: { type: Array, default: () => [] },
});

const toggleFavorite = (id) => {
    router.post(`/conversations/${id}/toggle-favorite`, {}, {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <div class="mt-6">
        <h2 class="mb-4 text-lg font-semibold text-slate-100">Conversations</h2>
        <ul class="space-y-2">
            <li
                v-for="conversation in conversations"
                :key="conversation.id"
                class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-900/60 p-4 text-slate-100 transition hover:bg-slate-800/80"
            >
                <Link :href="`/ask-stream?conversation_id=${conversation.id}`" class="flex-1 truncate">
                    {{ conversation.title }}
                </Link>
                <button
                    type="button"
                    class="ml-2 text-2xl transition hover:scale-110"
                    :class="conversation.is_favorite ? 'text-yellow-400' : 'text-slate-600 hover:text-yellow-300'"
                    :title="conversation.is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris'"
                    @click.stop="toggleFavorite(conversation.id)"
                >
                    {{ conversation.is_favorite ? '★' : '☆' }}
                </button>
            </li>
        </ul>
    </div>
</template>