<?php

declare(strict_types=1);

namespace App\Http\Controllers;
use App\Models\Conversations;

use App\Services\SimpleAskStreamService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller pour la démonstration du streaming SSE.
 *
 * Exemple pédagogique : streaming temps réel avec Laravel + Vue.
 */
class AskStreamController extends Controller
{
    public function __construct(
        private SimpleAskStreamService $streamService
    ) {}

    /**
     * Affiche la page de streaming.
     */
    public function index(Request $request): Response
    {
        $conversation = null;
        $messages = [];

        // Si on ouvre une conversation existante, on charge ses messages
        if ($request->filled('conversation_id')) {
            $conversation = Conversations::query()
                ->where('id', $request->integer('conversation_id')) 
                ->where('user_id', Auth::id())
                ->with('messages')
                ->first();

            if ($conversation) {
                $messages = $conversation->messages()
                    ->get()
                    ->map(fn ($m) => [
                        'id' => $m->id,
                        'role' => $m->is_user ? 'user' : 'assistant',
                        'content' => $m->content,
                    ])
                    ->values()
                    ->all();
            }
        }

        $modelId = $request->input('model')
            ?? Auth::user()?->preferred_model
            ?? SimpleAskStreamService::DEFAULT_MODEL;

        return Inertia::render('ask-stream/Index', [
            'models' => $this->streamService->getModelsLight(),
            'selectedModel' => $modelId,
            'conversations' => Conversations::getListConversationsByUserId(Auth::id()),
            'selectedConversationId' => $conversation?->id,
            'messages' => $messages,
        ]);
    }

    /**
     * Endpoint de streaming.
     */
    public function stream(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:100000',
            'model' => 'required|string',
            'conversation_id' => 'nullable|integer|exists:conversations,id',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'reasoning_effort' => 'nullable|string|in:low,medium,high',
        ]);

        $user = Auth::user();

        // Trouve la conversation existante (ou en crée une nouvelle)
        $conversation = null;
        if (! empty($validated['conversation_id'])) {
            $conversation = Conversations::query()
                ->where('id', $validated['conversation_id'])
                ->where('user_id', $user->id)
                ->first();
        }

        if (! $conversation) {
            $conversation = Conversations::create([
                'title' => mb_strlen($validated['message']) > 20
                    ? mb_substr($validated['message'], 0, 20) . '...'
                    : $validated['message'],
                'user_id' => $user->id,
            ]);
        }

        // Sauvegarde le message de l'utilisateur
        $userMessage = $conversation->messages()->create([
            'content' => $validated['message'],
            'is_user' => true,
        ]);

        // Construit l'historique (20 derniers messages)
        $history = $conversation->messages()
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => [
                'role' => $m->is_user ? 'user' : 'assistant',
                'content' => $m->content,
            ])
            ->all();

        $model = $validated['model'];
        $temperature = (float) ($validated['temperature'] ?? 1.0);
        $reasoningEffort = $validated['reasoning_effort'] ?? null;

        return response()->stream(
            function () use ($conversation, $userMessage, $history, $model, $temperature, $reasoningEffort): void {
                // 1. On signale d'abord l'id de la conversation au front
                echo "[CONVID]{$conversation->id}[/CONVID]";

                // 2. On streame la réponse et on récupère le texte complet
                $fullText = $this->streamService->streamToOutput(
                    $history,
                    $model,
                    $temperature,
                    $reasoningEffort,
                    [
                        'conversation_id' => $conversation->id,
                        'message_id'      => $userMessage->id,
                    ],
                );

                // 3. On sauvegarde la réponse de l'assistant en DB
                if (trim($fullText) !== '') {
                    $conversation->messages()->create([
                        'content' => $fullText,
                        'is_user' => false,
                    ]);
                }
            },
            headers: [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Cache-Control' => 'no-cache, no-store',
                'X-Accel-Buffering' => 'no',
            ]
        );
    }
    // TOGGLE FAVORITE
    public function toggleFavorite(int $id)
    {
        $conversation = Conversations::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $conversation->update([
            'is_favorite' => ! $conversation->is_favorite,
        ]);

        return back();
    }
}