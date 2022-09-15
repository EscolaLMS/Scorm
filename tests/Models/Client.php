<?php

namespace EscolaLms\Scorm\Tests\Models;

class Client extends \Laravel\Passport\Client
{
    public function getIdAttribute()
    {
        return $this->attributes['id'];
    }
}
