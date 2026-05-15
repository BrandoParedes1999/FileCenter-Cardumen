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
        return ['mail', 'database'];
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

    public function toArray(object $notifiable): array
    {
        $solicitante = $this->solicitud->solicitante;
        $carpeta     = $this->solicitud->carpeta;

        return [
            'tipo'         => 'solicitud_acceso_recibida',
            'titulo'       => 'Nueva solicitud de acceso',
            'mensaje'      => "{$solicitante->nombre_completo} solicita acceso a \"{$carpeta->nombre}\".",
            'url'          => route('solicitudes.show', $this->solicitud),
            'solicitud_id' => $this->solicitud->id,
        ];
    }
}
