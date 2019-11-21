<?php declare(strict_types=1);

namespace Spagi\EmailParser;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $fillable = ['from', 'to', 'price', 'ks', 'vs', 'ss', 'note', 'category','currency','date'];
    protected $table = 'payments';
}
