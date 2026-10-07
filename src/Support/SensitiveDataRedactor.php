<?php

namespace EdwardTich\FailedJobsManager\Support;

class SensitiveDataRedactor
{
    public static function redact(string $value): string
    {
        $value = preg_replace(
            '/("(?:api[_-]?key|access[_-]?token|refresh[_-]?token|token|secret|password|authorization|cookie)"\s*:\s*")[^"]*(")/i',
            '$1[REDACTED]$2',
            $value
        ) ?: $value;

        $value = preg_replace(
            '/\b(api[_-]?key|access[_-]?token|refresh[_-]?token|token|secret|password)\b(\s*[=:]\s*)([^\s,;"\']+)/i',
            '$1$2[REDACTED]',
            $value
        ) ?: $value;

        return preg_replace('/\bBearer\s+[A-Za-z0-9._~+\/=\-]+/i', 'Bearer [REDACTED]', $value) ?: $value;
    }
}
