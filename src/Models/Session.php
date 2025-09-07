<?php
namespace QyroSdk\Models;


class Session
{
    public string $id;


    public function __construct(string $id)
    {
        $this->id = $id;
    }
}