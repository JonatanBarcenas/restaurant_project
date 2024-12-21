document.addEventListener('DOMContentLoaded', function() {
    const userButton = document.querySelector('.user-button');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    userButton.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });

    // Cerrar el menú si se hace click fuera
    document.addEventListener('click', function(e) {
        if (!dropdownMenu.contains(e.target) && !userButton.contains(e.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
});
