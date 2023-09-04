<?php

namespace model;

class Item
{
    private int $id;
    private string $name;
    private bool $checked;

    public function __construct(string $name, int $id = null, bool $checked = false)
    {
        $this->name = $name;
        if ($id != null)
            $this->id = $id;
        $this->checked = $checked;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function checkItem(): void
    {
        $this->checked = true;
    }

    public function uncheckItem(): void
    {
        $this->checked = false;
    }
}