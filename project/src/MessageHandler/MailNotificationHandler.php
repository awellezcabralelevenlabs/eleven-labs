<?php
 
namespace App\MessageHandler;
 
use App\Entity\User;
use App\Message\MailNotification;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
 
class MailNotificationHandler implements MessageHandlerInterface
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
 
    public function __invoke(MailNotification $message)
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $message->getUser(),
            (new TemplatedEmail())
                ->from(new Address($message->getFrom(), 'ELeven-Labs Admin'))
                ->to($message->getTo())
                ->subject('Please Confirm your Email')
                ->htmlTemplate($message->getDescription())
        );
    }
}