<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChange extends Notification
{
    public function __construct(private readonly TravelOrder $travelOrder)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('O status do seu pedido foi alterado!')
            ->greeting("Olá {$notifiable->name}")
            ->line("Tô aqui para te informar que o status do seu pedido de viagem foi alterado para: {$this->travelOrder->status->value}")
            ->line('Caso tenha alguma dúvida é só entrar em contato por Email. =)')
            ->line('Obrigado por utilizar o Travly.');
    }
}
