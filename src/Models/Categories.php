<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Trivia;
use Illuminate\Support\Facades\Auth;
class Categories extends Model
{
    protected $table = 'trv_categories';
    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active'
    ];

    public function trivia()
    {
        return $this->hasMany(Trivia::class, 'category_id', 'id');
    }

    public function availableTrivia()
    {
        return $this->trivia()->where('is_active', true)->where(function($q) {
            if(!Auth::check()) {
                $q->where('private', false);
                return;
            }
            $q->where('private', false)->orWhere('user_id', auth()->user()->id);
        })->orderBy('private', 'desc')->orderBy('created_at', 'asc');
    }
}