<?php

declare(strict_types=1);

function h(mixed $value): string
{
    return htmlspecialchars(is_scalar($value) ? (string)$value : '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function postedText(string $name, int $max, bool $required = true): string
{
    $value = $_POST[$name] ?? '';
    if (!is_string($value)) {
        throw new DomainException('Invalid ' . str_replace('_', ' ', $name) . '.');
    }
    $value = trim($value);
    if (($required && $value === '') || mb_strlen($value) > $max) {
        throw new DomainException('Check ' . str_replace('_', ' ', $name) . '.');
    }
    return $value;
}

function postedId(string $name): int
{
    $value = $_POST[$name] ?? null;
    if (!is_string($value) || !ctype_digit($value) || (int)$value < 1) {
        throw new DomainException('Select a valid ' . str_replace('_', ' ', $name) . '.');
    }
    return (int)$value;
}

function redirectTo(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function setNotice(string $message): void
{
    $_SESSION['workflow_notice'] = $message;
}

function takeNotice(): string
{
    $message = (string)($_SESSION['workflow_notice'] ?? '');
    unset($_SESSION['workflow_notice']);
    return $message;
}
