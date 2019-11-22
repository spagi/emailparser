# Emailparser
Download, save, parse payment info from email messages

## Instalation

Install the EmailParser package by running the following command: `composer require spagi/email-parser`  
Run the command below to publish the package config file `config/emailparser.php`: \
`php artisan vendor:publish` 
### Configuration
            'host'  =>  'imap.seznam.cz',
            'port'  => env('IMAP_PORT', 993),
            'protocol'  => env('IMAP_PROTOCOL', 'imap'), //might also use imap, [pop3 or nntp (untested)]
            'encryption'    => env('IMAP_ENCRYPTION', 'ssl'), // Supported: false, 'ssl', 'tls', 'notls', 'starttls'
            'validate_cert' => env('IMAP_VALIDATE_CERT', true),
            'username' => env('IMAP_USERNAME', 'some@email.com'),
            'password' => env('IMAP_PASSWORD', 'pass') 


#### Example

             $date = new \DateTimeImmutable('19.11.2019'); 
             $this->emailMessagesService->synchronize( $date);
