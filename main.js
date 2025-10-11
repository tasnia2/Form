function openModal(id) {
    document.getElementById(`modal-${id}`).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(`modal-${id}`).classList.add('hidden');
}
