<?php

namespace model;

class Item
{
    private int $itemId;
    private string $name;
    private bool $checked;

    private $editing = false;

    public function __construct($id, $name)
    {
        $this->name = $name;
        $this->checked = false;
        $this->itemId = $id;
    }

    public function getId(): int
    {
        return $this->itemId;
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

    public function isEditing(): bool
    {
        return $this->editing;
    }
    public function setEditing($editing): void
    {
        $this->editing = $editing;
    }
}