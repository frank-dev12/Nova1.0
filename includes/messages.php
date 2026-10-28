<?php

declare(strict_types=1);

function messages_path(): string
{
    return project_root() . '/data/messages.php';
}

function &messages_bucket(): array
{
    static $items = null;
    if ($items === null) {
        $path = messages_path();
        if (is_file($path) && function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }
        $loaded = is_file($path) ? require $path : [];
        $items = is_array($loaded) ? array_values($loaded) : [];
    }

    return $items;
}

function messages_save(): void
{
    $items = messages_bucket();
    $content = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export(array_values($items), true) . ";\n";
    file_put_contents(messages_path(), $content, LOCK_EX);
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate(messages_path(), true);
    }
}

function messages_all(): array
{
    $items = messages_bucket();
    usort($items, static fn (array $a, array $b): int => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));

    return $items;
}

function messages_unread_count(): int
{
    return count(array_filter(messages_bucket(), static fn (array $message): bool => empty($message['read'])));
}

function message_create(string $name, string $email, string $phone, string $body): void
{
    $items = &messages_bucket();
    $items[] = [
        'id' => bin2hex(random_bytes(8)),
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $body,
        'read' => false,
        'created_at' => lima_now(),
    ];
    messages_save();
}

function message_mark_read(string $id): void
{
    $items = &messages_bucket();
    foreach ($items as $index => $message) {
        if (($message['id'] ?? '') === $id) {
            $items[$index]['read'] = true;
            messages_save();
            return;
        }
    }
}
