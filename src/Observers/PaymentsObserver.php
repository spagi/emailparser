<?php declare(strict_types=1);

namespace Spagi\EmailParser\Observers;

use Spagi\EmailParser\Payments;
use Carbon\Carbon;

class PaymentsObserver
{

    public function creating(Payments $payments)
    {
        $date = new Carbon($payments->date);
        $payments->date = $date->format('Y-m-d H:i:s');
    }

}
