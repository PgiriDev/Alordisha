<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class PublicChatController extends Controller
{
    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = trim($validated['message']);
        $preferredLanguage = $this->detectLanguage($message);

        if ($this->isGreetingMessage($message)) {
            return response()->json([
                'reply' => $preferredLanguage === 'bn'
                    ? 'নমস্কার! আমি Alo Helpdesk। আলোর দিশা সম্পর্কে জানতে address, contact, course, fee, admission, director, teacher/student count—যেকোনো প্রশ্ন করতে পারেন।'
                    : 'Hello! I am Alo Helpdesk. You can ask anything about Alor Disha, such as address, contact, courses, fees, admission, director, and teacher/student count.',
                'source' => 'built_in',
            ]);
        }

        $localReply = $this->findLocalKnowledgeReply($message, $preferredLanguage);

        if ($localReply !== null) {
            return response()->json([
                'reply' => $localReply,
                'source' => 'knowledge_base',
            ]);
        }

        $aiReply = $this->askExternalAi($message, $preferredLanguage);

        if ($aiReply !== null) {
            return response()->json([
                'reply' => $aiReply,
                'source' => 'ai',
            ]);
        }

        return response()->json([
            'reply' => $preferredLanguage === 'bn'
                ? 'এখনই এই প্রশ্নের নির্ভুল উত্তর দিতে পারছি না। ভর্তি, কোর্স, সময়সূচি বা যোগাযোগের বিষয়ে জানতে Contact section দেখুন।'
                : 'I cannot answer this accurately right now. Please check the Contact section for admission, course, schedule, or support information.',
            'source' => 'fallback',
        ]);
    }

    private function findLocalKnowledgeReply(string $message, string $preferredLanguage): ?string
    {
        $messageLower = mb_strtolower($message);

        $items = KnowledgeItem::query()
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->get(['question', 'answer', 'keywords']);

        if ($items->isEmpty()) {
            return null;
        }

        $bestScore = 0;
        $bestAnswer = null;

        foreach ($items as $item) {
            $score = 0;

            $question = mb_strtolower((string) $item->question);
            if ($question !== '' && str_contains($messageLower, $question)) {
                $score += 5;
            }

            $itemAnswer = (string) $item->answer;
            $itemLooksBangla = $this->containsBangla($itemAnswer);
            if ($preferredLanguage === 'bn' && $itemLooksBangla) {
                $score += 2;
            }
            if ($preferredLanguage === 'en' && !$itemLooksBangla) {
                $score += 2;
            }

            $keywordsRaw = (string) ($item->keywords ?? '');
            if ($keywordsRaw !== '') {
                $keywords = collect(explode(',', $keywordsRaw))
                    ->map(fn (string $keyword) => trim(mb_strtolower($keyword)))
                    ->filter();

                foreach ($keywords as $keyword) {
                    if (str_contains($messageLower, $keyword)) {
                        $score += 2;
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestAnswer = $itemAnswer;
            }
        }

        return $bestScore >= 2 ? $bestAnswer : null;
    }

    private function askExternalAi(string $message, string $preferredLanguage): ?string
    {
        $apiKey = (string) config('services.ai.api_key');
        $model = (string) config('services.ai.model', 'gpt-4o-mini');
        $baseUrl = rtrim((string) config('services.ai.base_url', 'https://api.openai.com/v1'), '/');
        $timeoutSeconds = (int) config('services.ai.timeout', 20);

        if ($apiKey === '') {
            return null;
        }

        $replyLanguageInstruction = $preferredLanguage === 'bn'
            ? 'Reply in Bengali language.'
            : 'Reply in English language.';

        $systemPrompt = "You are Alo Helpdesk for Alor Disha. "
            . "If user asks about Alor Disha specifics and you are unsure, say to contact support instead of guessing. "
            . "For general educational questions, provide short clear answers. "
            . $replyLanguageInstruction;

        try {
            $response = Http::timeout($timeoutSeconds)
                ->withToken($apiKey)
                ->post($baseUrl . '/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $message],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 300,
                ]);

            if (!$response->successful()) {
                return null;
            }

            $content = (string) data_get($response->json(), 'choices.0.message.content', '');
            $content = trim($content);

            return $content !== '' ? $content : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function isGreetingMessage(string $message): bool
    {
        $normalized = mb_strtolower(trim($message));

        if ($normalized === '') {
            return false;
        }

        $greetings = [
            'hi',
            'hello',
            'hey',
            'helo',
            'hola',
            'namaskar',
            'namaste',
            'assalamu alaikum',
            'আসসালামু আলাইকুম',
            'নমস্কার',
            'হ্যালো',
            'ওই',
        ];

        return in_array($normalized, $greetings, true);
    }

    private function detectLanguage(string $message): string
    {
        return $this->containsBangla($message) ? 'bn' : 'en';
    }

    private function containsBangla(string $text): bool
    {
        return preg_match('/[\x{0980}-\x{09FF}]/u', $text) === 1;
    }
}
