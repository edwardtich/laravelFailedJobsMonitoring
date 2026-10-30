<?php

namespace EdwardTich\FailedJobsManager\Support;

use Illuminate\Support\Str;

class FailedJobException
{
    /**
     * @param array<int, string> $lines
     */
    private function __construct(
        private readonly string $raw,
        private readonly array $lines,
    ) {
    }

    public static function from(string $exception): self
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($exception));

        return new self($exception, array_values(array_filter($lines ?: [], static fn ($line) => $line !== '')));
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function headline(): string
    {
        return $this->lines[0] ?? __('failed-jobs-manager::failed-jobs-manager.exception.unknown_exception');
    }

    public function className(): ?string
    {
        if (preg_match('/^([A-Za-z_\\\\][A-Za-z0-9_\\\\]*):/', $this->headline(), $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function message(): string
    {
        $headline = $this->headline();
        $className = $this->className();

        if ($className) {
            $headline = trim(Str::after($headline, $className . ':'));
        }

        return trim(preg_replace('/\s+in\s+.+:\d+$/', '', $headline) ?: $headline);
    }

    /**
     * Secrets are always redacted; $masked additionally hides personal data (see SensitiveDataRedactor::mask()).
     */
    public function safeMessage(bool $masked = true): string
    {
        return $masked
            ? SensitiveDataRedactor::mask($this->message())
            : SensitiveDataRedactor::redact($this->message());
    }

    public function file(): ?string
    {
        if (preg_match('/\sin\s(.+):(\d+)$/', $this->headline(), $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function line(): ?int
    {
        if (preg_match('/\sin\s.+:(\d+)$/', $this->headline(), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * @return array<int, array{number: string|null, text: string, application: bool}>
     */
    public function frames(): array
    {
        return collect($this->lines)
            ->skipUntil(static fn ($line) => str_starts_with($line, '#0'))
            ->map(function (string $line) {
                preg_match('/^(#\d+)\s+(.*)$/', $line, $matches);

                $text = $matches[2] ?? $line;

                return [
                    'number' => $matches[1] ?? null,
                    'text' => $text,
                    'application' => str_contains($text, '/app/') || str_contains($text, '\\App\\'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * The masked summary leaves out the file and line: server paths and code layout
     * are only needed by those who handle the failure.
     *
     * @return array<string, mixed>
     */
    public function summary(bool $masked = true): array
    {
        $summary = [
            __('failed-jobs-manager::failed-jobs-manager.exception.class') => $this->className(),
            __('failed-jobs-manager::failed-jobs-manager.exception.message') => $this->safeMessage($masked),
        ];

        if ($masked) {
            return $summary;
        }

        return $summary + [
            __('failed-jobs-manager::failed-jobs-manager.exception.file') => $this->file(),
            __('failed-jobs-manager::failed-jobs-manager.exception.line') => $this->line(),
        ];
    }

    public function preview(int $limit, bool $masked = true): string
    {
        return Str::limit($this->safeMessage($masked), $limit);
    }
}
