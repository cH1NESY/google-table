<?php

namespace App\DTO;

class RecordDto
{
    public string $text;
    public string $status;

    public function __construct(array $data)
    {
        $this->text = $data['text'];
        $this->status = $data['status'];
    }
} 