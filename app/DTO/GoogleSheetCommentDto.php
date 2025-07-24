<?php

namespace App\DTO;

class GoogleSheetCommentDto
{
    public string $id;
    public string $comment;

    public function __construct(string $id, string $comment)
    {
        $this->id = $id;
        $this->comment = $comment;
    }
} 