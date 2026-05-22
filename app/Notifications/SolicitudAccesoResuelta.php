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
        $recurso  = $carpeta?->nombre
                 ?? $this->solicitud->archivo?->nombre_original
                 ?? 'el recurso solicitado';

        $message = (new MailMessage)
            ->subject(($aprobada ? '✓ Acceso aprobado' : '✗ Acceso denegado') . ' — FileCenter')
            ->greeting('Hola, ' . $notifiable->nombre_completo);

        if ($aprobada) {
            $message->line("Tu solicitud de acceso a \"{$recurso}\" fue **aprobada** por {$revisor->nombre_completo}.");

            if ($carpeta) {
                $message->action('Ver carpeta', route('carpetas.show', $carpeta));
            }

            if ($this->solicitud->caduca_en) {
                $message->line('El acceso expira el: ' . $this->solicitud->caduca_en->format('d/m/Y'));
            }
        } else {
            $message->line("Tu solicitud de acceso a \"{$recurso}\" fue **rechazada**.");
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
        $empresa  = $this->solicitud->empresaObjetivo?->nombre;
        $recurso  = $carpeta?->nombre
                 ?? $this->solicitud->archivo?->nombre_original
                 ?? ($empresa ? "acceso a {$empresa}" : 'la solicitud');

        return [
            'tipo'         => 'solicitud_acceso_resuelta',
            'titulo'       => $aprobada ? 'Acceso aprobado' : 'Acceso denegado',
            'mensaje'      => $aprobada
                ? "Tu solicitud de \"{$recurso}\" fue aprobada."
                : "Tu solicitud de \"{$recurso}\" fue rechazada.",
            'url'          => route('solicitudes.show', $this->solicitud),
            'solicitud_id' => $this->solicitud->id,
        ];
    }
}
