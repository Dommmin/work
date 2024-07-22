<?php

declare(strict_types=1);

namespace App\Dto;

class ErrorDto
{
    public function __construct(
        public bool $success,
        public array $errors
    ) {
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'errors' => $this->errors,
        ];
    }
}
