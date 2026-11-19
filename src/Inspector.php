<?php

declare(strict_types=1);

namespace ImageInputInspector;

use InvalidArgumentException;
use RuntimeException;

final class Inspector
{
    /**
     * Inspect a local image and compare it to caller-provided upload limits.
     * Returns dimensions as stored; it does not apply EXIF orientation.
     *
     * @param list<string> $allowedMimeTypes
     * @return array{width: int, height: int, bytes: int, mime: string, accepted: bool, reasons: list<string>}
     */
    public static function inspect(string $path, int $maxBytes, array $allowedMimeTypes): array
    {
        if ($maxBytes <= 0) {
            throw new InvalidArgumentException('The maximum byte count must be positive.');
        }
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException('The image must be a readable local file.');
        }

        $size = @filesize($path);
        $image = @getimagesize($path);
        if ($size === false || $image === false || !isset($image['mime'])) {
            throw new RuntimeException('Could not read image metadata.');
        }

        $reasons = [];
        if ($size > $maxBytes) {
            $reasons[] = 'file_too_large';
        }
        if (!in_array($image['mime'], $allowedMimeTypes, true)) {
            $reasons[] = 'unsupported_type';
        }

        return [
            'width' => $image[0],
            'height' => $image[1],
            'bytes' => $size,
            'mime' => $image['mime'],
            'accepted' => $reasons === [],
            'reasons' => $reasons,
        ];
    }
}
