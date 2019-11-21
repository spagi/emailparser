<?php declare(strict_types=1);

namespace Spagi\EmailParser;

use Illuminate\Database\Eloquent\Model;

class EmailMessages extends Model
{
    protected $table = 'email_messages';
    protected $fillable = ['message', 'email', 'date'];
}
