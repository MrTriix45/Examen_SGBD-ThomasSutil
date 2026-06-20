<script setup>
import { ref, computed } from 'vue';
import { useStream } from '@laravel/stream-vue';

const props = defineProps({
    models: Array,
    selectedModel: String,
})

const message = ref('');
const model = ref(props.selectedModel ?? '');

const { data, isFetching, isStreaming, send, cancel } = useStream(
    '/ask-stream',
    {
        onFinish: () => {
            message.value = '';
        },
        onError: (err) => {
            console.error('Erreur streaming:', err);
        },
    }
);

const streamedContent = computed(() => {
    if (!data.value) return '';
    return data.value
        .replace(/\[REASONING\][\s\S]*?\[\/REASONING\]/g, '')
        .trim();
});

const streamedReasoning = computed(() => {
    if (!data.value) return '';
    const matches = data.value.match(/\[REASONING\]([\s\S]*?)\[\/REASONING\]/g);
    if (!matches) return '';
    return matches
        .map(m => m.replace(/\[REASONING\]/g, '').replace(/\[\/REASONING\]/g, ''))
        .join('');
});

const submit = () => {
    if (!message.value.trim()) return;
    send({ message: message.value, model: model.value });
};
</script>