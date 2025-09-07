<?php
namespace QyroSdk\Models;


class Message
{
    public string $id;
    public string $role;
    public string $content;


    public function __construct(string $id, string $role, string $content)
    {
        $this->id = $id;
        $this->role = $role;
        $this->content = $content;
    }
}