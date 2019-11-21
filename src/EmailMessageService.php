<?php declare(strict_types=1);


namespace Spagi\EmailParser;


use Illuminate\Support\Collection;
use Spagi\EmailParser\helpers\Strings;

class EmailMessageService
{
    /**
     * @var \Spagi\EmailParser\ImapClientFactory
     */
    private $clientFactory;

    public function __construct(ImapClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param string             $query
     * @param \DateTimeImmutable $date
     * @return \Illuminate\Support\Collection
     * @throws \Webklex\PHPIMAP\Exceptions\ConnectionFailedException
     * @throws \Webklex\PHPIMAP\Exceptions\MailboxFetchingException
     */
    public function getEmailMessagesByQueryAndDate(string $query, \DateTimeImmutable $date): Collection
    {
        $client = $this->clientFactory->getClientWithConfig();
        $client->connect();
        $folders = $client->getFolders();

        $result = collect([]);
        $folders->each(function ($folder) use ($result, $query, $date) {
            $folder->query()->text($query)->since($date->format('d.m.Y'))->get()
                ->each(function ($message) use ($result, $date) {
                    $bodies = $message->bodies;
                    $attributes = $message->getAttributes();
                    $textBody = $bodies['text'];
                    $from = collect($attributes['from'])->map(function ($item) {
                        return $item->mail;
                    });

                    $result->push(['body' => $textBody->content, 'date' => $attributes['date'], 'from' => $from[0]]);
                });

        });
        return $result;
    }

    /**
     * @param array $data
     */
    public function storeMessage(array $data): void
    {
        $message = new EmailMessages();
        $message->fill($data);
        $message->save();
    }

    /**
     * @param string $message
     * @return \Illuminate\Support\Collection
     */
    public function parseMessage(string $message): Collection
    {
        $parts = explode("\r\n", $message);
        return collect($parts)->filter(function ($item) {
            return !Strings::isEmpty($item);
        });
    }

    /**
     * @param array                          $details
     * @param \Illuminate\Support\Collection $data
     * @return \Illuminate\Support\Collection
     */
    public function findDetails(array $details, Collection $data): Collection
    {
        $return = collect([]);
        foreach ($details as $detail) {
            $data->each(function ($item) use ($detail, $return) {
                if (strpos($item, $detail) !== false) {
                    $data = explode(':', $item, 2);
                    $return->put(strtolower($detail), $data[1]);
                }
            });
        }
        return $return;

    }

    /**
     * @param array $toArray
     * @return string|null
     */
    public function findCategoryNote(array $toArray): ?string
    {
        if (!isset($toArray['note'])) {
            return null;
        }

        if (strpos(strtolower($toArray['note']), "restaurant") !== false) {
            return "Jidlo";
        }

    }

    /**
     * @param array $toArray
     * @return array
     */
    public function getPriceDetails(array $toArray): array
    {
        if (!isset($toArray['price'])) {
            return null;
        }

        $priceDetail = explode(" ", $toArray['price']);

        $price = $priceDetail[0];
        $currency = isset($priceDetail[1]) ? $priceDetail[1] : null;

        return ['price' => floatval($price), 'currency' => $currency];
    }

    /**
     * @param \DateTimeImmutable $date
     * @return int
     * @throws \Webklex\PHPIMAP\Exceptions\ConnectionFailedException
     * @throws \Webklex\PHPIMAP\Exceptions\MailboxFetchingException
     */
    public function fetchAndSaveMessages(\DateTimeImmutable $date): int
    {
        $messages = $this->getEmailMessagesByQueryAndDate('Info o platbě', $date);

        $messages->each(function ($message) {
            $date = $message['date'];
            $email = new EmailMessages();
            $email->message = $message['body'];
            $email->date = $date->format('Y-m-d H:i:s');
            $email->email = $message['from'];
            $email->save();
        });

        return $messages->count();

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function findMessagesToParse(): Collection
    {
        return EmailMessages::where('parsed', false)->get();
    }
}
