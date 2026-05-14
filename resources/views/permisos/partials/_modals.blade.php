{{-- ══ Modal: edición completa de permisos ══════════════════════ --}}
<div class="pg-modal-overlay" id="modalEditar">
    <div class="pg-modal">
        <div class="pg-modal-title">Editar permisos</div>
        <div class="pg-modal-sub" id="modalEditarSub">—</div>

        <form id="formEditar" method="POST">
            @csrf @method('PUT')

            <div class="pg-modal-caps">
                @php
                    $modalCaps = [
                        'puede_leer'      => ['👁',  'Leer',      'Ver la carpeta y su lista de archivos'],
                        'puede_descargar' => ['⬇',  'Descargar', 'Descargar archivos al dispositivo'],
                        'puede_subir'     => ['⬆',  'Subir',     'Agregar nuevos archivos a la carpeta'],
                        'puede_editar'    => ['✏',  'Editar',    'Modificar descripción y metadatos'],
                        'puede_borrar'    => ['🗑', 'Borrar',    'Eliminar archivos de la carpeta'],
                        'heredar'         => ['↳',  'Heredar',   'Extender permisos a todas las subcarpetas'],
                    ];
                @endphp
                @foreach($modalCaps as $campo => [$icon, $label, $desc])
                <div class="pg-modal-cap-row">
                    <div class="pg-modal-cap-name">
                        <div class="cap-icon" style="background:#f1f5f9">{{ $icon }}</div>
                        {{ $label }}
                        <span>— {{ $desc }}</span>
                    </div>
                    <div class="pg-modal-cap-toggle">
                        <input type="hidden" name="{{ $campo }}" value="0">
                        <input type="checkbox" id="modal_{{ $campo }}" name="{{ $campo }}" value="1">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pg-modal-btns">
                <button type="button" class="fc-btn fc-btn-outline"
                        onclick="document.getElementById('modalEditar').classList.remove('open')">
                    Cancelar
                </button>
                <button type="submit" class="fc-btn fc-btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ Modal: confirmar revocar ═══════════════════════════════════ --}}
<div class="fc-modal-overlay" id="modalRevocar">
    <div class="fc-modal">
        <div class="fc-modal-title">¿Revocar este permiso?</div>
        <div class="fc-modal-sub" id="modalRevocarSub">—</div>
        <div class="fc-modal-btns">
            <button class="fc-modal-cancel"
                    onclick="document.getElementById('modalRevocar').classList.remove('open')">Cancelar</button>
            <form id="formRevocar" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="fc-modal-confirm danger">Revocar</button>
            </form>
        </div>
    </div>
</div>
