<script>
function setView(type) {
    const grid    = document.getElementById('carpetasGrid');
    const btnGrid = document.getElementById('btnGrid');
    const btnList = document.getElementById('btnList');
    const cards   = grid.querySelectorAll('.carpeta-card');
    if (type === 'grid') {
        grid.classList.remove('list-view');
        cards.forEach(c => c.classList.remove('list-view'));
        btnGrid.classList.add('active');
        btnList.classList.remove('active');
    } else {
        grid.classList.add('list-view');
        cards.forEach(c => c.classList.add('list-view'));
        btnList.classList.add('active');
        btnGrid.classList.remove('active');
    }
}
function selectCarpeta(el) {
    document.querySelectorAll('.carpeta-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}
</script>