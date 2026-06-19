<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type Conversation = {
    id: number | string;
    title: string;
};

defineProps<{
    conversations: Conversation[];
}>();
</script>
<template>
    <!-- Conversation List -->
    <div class="mt-6">
        <h2 class="mb-4 text-lg font-semibold text-slate-100">
            Conversations
        </h2>

        <ul class="space-y-2">
            <li
                v-for="conversation in conversations"
                :key="conversation.id"
                class="rounded-lg border border-slate-800 bg-slate-900/60 p-4 text-slate-100 transition hover:bg-slate-800/80"
            >
                <Link
                    :href="`/ask?conversation_id=${conversation.id}`"
                    class="block"
                >
                    {{ conversation.title }}
                </Link>
            </li>
        </ul>
    </div>
</template>