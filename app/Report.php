<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'git_repository'
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'git_repository' => $this->git_repository,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
