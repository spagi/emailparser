<?php declare(strict_types=1);


namespace Spagi\EmailParser;

class PaymentsService
{


    public function __construct()
    {

    }

    /**
     * @param array $data
     */
    public function store(array $data): void
    {
        $payments  = new Payments();
        $payments->fill($data);
        $payments->save();
    }
}
