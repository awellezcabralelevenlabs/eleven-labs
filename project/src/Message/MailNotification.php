<?php
 
namespace App\Message;
 
class MailNotification
{
    private $user;
    private $description;
    private $from;
    private $to;
 
    public function __construct(object $user, string $description, string $from, string $to)
    {
        $this->user = $user;
        $this->description = $description;
        $this->from = $from;
        $this->to = $to;
    }
    
    public function getUser(): object
    {
        return $this->user;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
 
    public function getFrom(): string
    {
        return $this->from;
    }
 
    public function getTo(): string
    {
        return $this->to;
    }
}