<?php

namespace App\Notifications;

use App\Models\SolicitudAcceso;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolicitudAccesoResuelta extends Notification
{
    use Queueable;

    public function __construct(public readonly SolicitudAcceso $solicitud) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $aprobada = $this->solicitud->fueAprobada();
        $carpeta  = $this->solicitud->carpeta;
        $revisor  = $this->solicitud->revisor;

        $message = (new MailMessage)
            ->subject(($aprobada ? '✓ Acceso aprobado' : '✗ Acceso denegado') . ' — FileCenter')
            ->greeting('Hola, ' . $notifiable->nombre_completo);

        if ($aprobada) {
            $message
                ->line("Tu solicitud de acceso a la carpeta \"{$carpeta->nombre}\" fue **aprobada** por {$revisor->nombre_completo}.")
                ->action('Ver carpeta', route('carpetas.show', $carpeta));

            if ($this->solicitud->caduca_en) {
                $message->line('El acceso expira el: ' . $this->solicitud->caduca_en->format('d/m/Y'));
            }
        } else {
            $message
                ->line("Tu solicitud de acceso a la carpeta \"{$carpeta->nombre}\" fue **rechazada**.");
        }

        if ($this->solicitud->comentario_revisor) {
            $message->line('Comentario: ' . $this->solicitud->comentario_revisor);
        }

        return $message->salutation('— FileCenter');
    }

    public function toArray(object $notifiable): array
    {
        $aprobada = $this->solicitud->fueAprobada();
        $carpeta  = $this->solicitud->carpeta;

        return [
            'tipo'    => 'solicitud_acceso_resuelta',
            'titulo'  => $aprobada ? 'Acceso aprobado' : 'Acceso denegado',
            'mensaje' => $aprobada
                ? "Tu acceso a \"{$carpeta->nombre}\" fue aprobado."
                : "Tu solicitud de acceso a \"{$carpeta->nombre}\" fue rechazada.",
            'url'     => $aprobada ? route('carpetas.show', $carpeta) : route('solicitudes.index'),
        ];
    }
}
