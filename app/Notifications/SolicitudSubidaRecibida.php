<?php

namespace App\Notifications;

use App\Models\SolicitudSubida;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolicitudSubidaRecibida extends Notification
{
    use Queueable;

    public function __construct(public readonly SolicitudSubida $solicitud) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $solicitante = $this->solicitud->solicitante;
        $carpeta     = $this->solicitud->carpeta;

        return (new MailMessage)
            ->subject('Archivo pendiente de aprobación — FileCenter')
            ->greeting('Hola, ' . $notifiable->nombre_completo)
            ->line("{$solicitante->nombre_completo} quiere subir el archivo \"{$this->solicitud->nombre_original}\" a la carpeta \"{$carpeta->nombre}\".")
            ->line('Tamaño: ' . $this->solicitud->tamanioFormateado())
            ->action('Revisar subida', route('solicitudes-subida.show', $this->solicitud))
            ->line('Debes aprobar o rechazar el archivo desde el panel de administración.')
            ->salutation('— FileCenter');
    }

    public function toArray(object $notifiable): array
    {
        $carpeta = $this->solicitud->carpeta;

        return [
            'tipo'         => 'solicitud_subida_recibida',
            'titulo'       => 'Archivo pendiente de aprobación',
            'mensaje'      => "\"{$this->solicitud->nombre_original}\" espera aprobación en \"{$carpeta->nombre}\".",
            'url'          => route('solicitudes-subida.show', $this->solicitud),
            'solicitud_id' => $this->solicitud->id,
        ];
    }
}
