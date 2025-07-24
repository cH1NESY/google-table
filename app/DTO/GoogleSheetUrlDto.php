<?php

namespace App\DTO;

class GoogleSheetUrlDto
{
    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }
} 