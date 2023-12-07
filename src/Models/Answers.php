<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Questions;

class Answers extends Model
{
    protected $table = 'answers';
    protected $primaryKey = 'id';

    protected $fillable = ['answer', 'question_id', 'correct', 'type', 'file_url', 'file_type'];

    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id', 'id');
    }

    /*
     * +-------------+-----------------+------+-----+---------+----------------+
| Field       | Type            | Null | Key | Default | Extra          |
+-------------+-----------------+------+-----+---------+----------------+
| id          | bigint unsigned | NO   | PRI | NULL    | auto_increment |
| question_id | bigint unsigned | NO   | MUL | NULL    |                |
| answer      | varchar(255)    | NO   |     | NULL    |                |
| correct     | tinyint(1)      | NO   |     | 0       |                |
| type        | varchar(255)    | NO   |     | text    |                |
| file_url    | varchar(255)    | YES  |     | NULL    |                |
| file_type   | varchar(255)    | YES  |     | NULL    |                |
| created_at  | timestamp       | YES  |     | NULL    |                |
| updated_at  | timestamp       | YES  |     | NULL    |                |
+-------------+-----------------+------+-----+---------+----------------+

     */
}