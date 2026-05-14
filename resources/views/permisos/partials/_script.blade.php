<script>
function abrirModalEditar(id, nombre, carpeta, leer, subir, editar, borrar, descargar, heredar) {
    document.getElementById('formEditar').action = '{{ url("/permisos-globales") }}/' + id;
    document.getElementById('modalEditarSub').textContent =
        nombre + '  ·  Carpeta: ' + carpeta;

    const vals = {
        puede_leer: leer, puede_descargar: descargar,
        puede_subir: subir, puede_editar: editar,
        puede_borrar: borrar, heredar: heredar,
    };
    Object.entries(vals).forEach(([k, v]) => {
        const el = document.getElementById('modal_' + k);
        if (el) el.checked = v;
    });

    document.getElementById('modalEditar').classList.add('open');
}

function confirmarRevocar(id, nombre, carpeta) {
    document.getElementById('formRevocar').action = '{{ url("/permisos-globales") }}/' + id;
    document.getElementById('modalRevocarSub').innerHTML =
        'Se revocará el acceso de <strong>' + nombre + '</strong> a la carpeta <strong>' + carpeta + '</strong>.';
    document.getElementById('modalRevocar').classList.add('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.pg-modal-overlay.open, .fc-modal-overlay.open')
                .forEach(m => m.classList.remove('open'));
    }
});
</script>
