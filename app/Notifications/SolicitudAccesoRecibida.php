<?php

namespace App\Notifications;

use App\Models\SolicitudAcceso;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolicitudAccesoRecibida extends Notification
{
    use Queueable;

    public function __construct(public readonly SolicitudAcceso $solicitud) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $solicitante = $this->solicitud->solicitante;
        $carpeta     = $this->solicitud->carpeta;

        return (new MailMessage)
            ->subject('Nueva solicitud de acceso — FileCenter')
            ->greeting('Hola, ' . $notifiable->nombre_completo)
            ->line("{$solicitante->nombre_completo} ha solicitado acceso ({$this->solicitud->tipo_acceso}) a la carpeta \"{$carpeta->nombre}\".")
            ->line('Motivo: ' . $this->solicitud->razon)
            ->action('Revisar solicitud', route('solicitudes.show', $this->solicitud))
            ->line('Puedes aprobar o rechazar la solicitud desde el panel de administración.')
            ->salutation('— FileCenter');
    }
}
