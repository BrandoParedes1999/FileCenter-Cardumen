<?php

namespace App\Notifications;

use App\Models\SolicitudSubida;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolicitudSubidaResuelta extends Notification
{
    use Queueable;

    public function __construct(public readonly SolicitudSubida $solicitud) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $aprobada = $this->solicitud->fueAprobada();

        $message = (new MailMessage)
            ->subject(($aprobada ? '✓ Archivo publicado' : '✗ Subida rechazada') . ' — FileCenter')
            ->greeting('Hola, ' . $notifiable->nombre_completo);

        if ($aprobada) {
            $carpeta = $this->solicitud->carpeta;
            $message
                ->line("Tu archivo \"{$this->solicitud->nombre_original}\" fue **aprobado** y está disponible en la carpeta \"{$carpeta->nombre}\".")
                ->action('Ver carpeta', route('carpetas.show', $carpeta));
        } else {
            $message->line("Tu solicitud de subida del archivo \"{$this->solicitud->nombre_original}\" fue **rechazada**.");
        }

        if ($this->solicitud->comentario_revisor) {
            $message->line('Comentario del revisor: ' . $this->solicitud->comentario_revisor);
        }

        return $message->salutation('— FileCenter');
    }
}
