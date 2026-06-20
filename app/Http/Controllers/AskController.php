<?php

namespace App\Http\Controllers;

use App\Services\SimpleAskService;
use App\Models\Conversations;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AskController extends Controller
{
    public function __construct(private SimpleAskService $askService) {}

    private function generateTitle(string $message): string
    {
        return mb_strlen($message) > 20 ? mb_substr($message, 0, 20) . '...' : $message;
    }

    private function formatMessages(Conversations $conversation): array
    {
        return $conversation->messages()
            ->get()
            ->map(fn ($message) => [
                'id' => $message->id,
                'role' => $message->is_user ? 'user' : 'assistant',
                'content' => $message->content,
            ])
            ->values()
            ->all();
    }

    private function renderIndex(array $props = [])
    {
        return Inertia::render('ask/Index', array_merge([
            'models' => $this->askService->getModels(),
            'selectedModel' => Auth::user()?->preferred_model ?? SimpleAskService::DEFAULT_MODEL,
            'conversations' => Conversations::getListConversationsByUserId(Auth::id()),
            'selectedConversationId' => null,
            'messages' => [],
            'message' => '',
            'response' => null,
            'error' => null,
        ], $props));
    }

    public function index(Request $request)
    {
        $conversation = null;
        $messages = [];

        if ($request->filled('conversation_id')) {
            $conversation = Conversations::query()
                ->where('id', $request->integer('conversation_id'))
                ->where('user_id', Auth::id())
                ->with('messages')
                ->first();

            if ($conversation) {
                $messages = $this->formatMessages($conversation);
            }
        }

        return $this->renderIndex([
            'selectedConversationId' => $conversation?->id,
            'messages' => $messages,
        ]);
    }

    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required|string',
            'conversation_id' => 'nullable|integer|exists:conversations,id',
        ]);

        $user = Auth::user();

        $conversation = null;

        if ($request->filled('conversation_id')) {
            $conversation = Conversations::query()
                ->where('id', $request->integer('conversation_id'))
                ->where('user_id', $user->id)
                ->first();
        }

        if (! $conversation) {
            $conversation = Conversations::create([
                'title' => $this->generateTitle($request->message),
                'user_id' => $user->id,
            ]);
        }

        $conversation->messages()->create([
            'content' => $request->message,
            'is_user' => true,
        ]);

        $messages = $conversation->messages()
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

        try {
            $response = $this->askService->sendMessage(
                messages: $messages,
                model: $request->model
            );
        } catch (\Exception $e) {
            return $this->renderIndex([
                'selectedModel' => $request->model,
                'message' => $request->message,
                'selectedConversationId' => $conversation->id,
                'messages' => $this->formatMessages($conversation),
                'error' => $e->getMessage(),
            ]);
        }

        $conversation->messages()->create([
            'content' => $response,
            'is_user' => false,
        ]);

        $conversation->load('messages');

        return $this->renderIndex([
            'selectedModel' => $request->model,
            'selectedConversationId' => $conversation->id,
            'messages' => $this->formatMessages($conversation),
            'response' => $response,
        ]);
    }
}