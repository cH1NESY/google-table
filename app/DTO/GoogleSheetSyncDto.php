<?php

namespace App\DTO;

class GoogleSheetSyncDto
{
    /** @var array<int, array> */
    public array $records;
    /** @var array<int, string> */
    public array $comments;

    public function __construct(array $records, array $comments)
    {
        $this->records = $records;
        $this->comments = $comments;
    }
} 