<?php


namespace Spagi\EmailParser;
use Illuminate\Support\Facades\Log;

class EmailParserIntegration
{
    private $messageService;
    private $paymentServise;

    public function __construct(EmailMessageService $messageService, PaymentsService $paymentsService)
    {
        $this->paymentServise = $paymentsService;
        $this->messageService = $messageService;
    }

    /**
     * @param \DateTimeImmutable $date
     * @throws \Webklex\PHPIMAP\Exceptions\ConnectionFailedException
     * @throws \Webklex\PHPIMAP\Exceptions\MailboxFetchingException
     */
    public function synchronize(\DateTimeImmutable $date): void
    {
        $count = $this->messageService->fetchAndSaveMessages($date);

        Log::info(sprintf("Imported %d email messages", $count));

        $messages = $this->messageService->findMessagesToParse();

        foreach ($messages as $message) {

            $parsed = $this->messageService->parseMessage($message->message);

            $details = $this->messageService->findDetails(['From', 'To', 'Price', 'Date', 'KS', 'VS', 'SS', 'Note'], $parsed);

            $priceDetails = $this->messageService->getPriceDetails($details->toArray());

            $details->put('price', $priceDetails['price']);
            $details->put('currency', $priceDetails['currency']);

            $category = $this->messageService->findCategoryNote($details->toArray());

            $details->put('category', $category);

            $this->paymentServise->store($details->toArray());

        }

    }
}
