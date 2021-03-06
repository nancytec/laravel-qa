<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $appends = ['url', 'avatar'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getUrlAttribute(){
//        return route('questions.show', $this->id);
        return '#';
    }

    //User Relationship model with the questions
    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function answers(){
        return $this->hasMany(Answer::class);
    }

    public function getAvatarAttribute(){
        $email = $this->email;
        $size  = 32;
        return "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?s=" .  $size;
    }

    public function favorites(){
        //To implemets many to may relationship
        $this->belongsToMany(Question::class, 'favorites'/** Not neccessary since we are following cnvention, 'user_id', 'question_id'**/)->withTimestamps(); //specify with a table

    }

    public function voteQuestions(){
        /*
         * use the singular form of the table name as the aurgument,
         * laravel will detect the the votable_id and votable_type
         * dynamically
         */
        return $this->morphedByMany(Question::class, 'votable');
    }

    public function voteAnswers(){
        /*
         * use the singular form of the table name as the aurgument,
         * laravel will detect the the votable_id and votable_type
         * dynamically
         */
        return $this->morphedByMany(Answer::class, 'votable');
    }

    public function voteQuestion(Question $question, $vote)
    {
       $voteQuestions =  $this->voteQuestions();
       return $this->_vote($voteQuestions, $question, $vote);

    }

    public function voteAnswer(Answer $answer, $vote)
    {
        $voteAnswers =  $this->voteAnswers();
        return $this->_vote($voteAnswers, $answer, $vote);
    }

    private function _vote($relationship, $model, $vote)
    {
        if ($relationship->where('votable_id', $model->id)->exists()){
            $relationship->updateExistingPivot($model, ['vote' => $vote]);
        }else{
            $relationship->attach($model, ['vote' => $vote]);
        }
        $model->load('votes');
        $downVotes = (int) $model->downVotes()->sum('vote');
        $upVotes = (int) $model->upVotes()->sum('vote');

        $model->votes_count = $upVotes + $downVotes;
        $model->save();
        return $model->votes_count;
    }


}
