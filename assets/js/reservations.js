document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const form = document.getElementById('reservationForm');
    const tables = document.querySelectorAll('.table');
    const submitButton = document.getElementById('submitReservation');
    const tableInput = document.getElementById('table_number');
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const guestsInput = document.getElementById('guests');

    // Configurar fecha mínima
    const today = new Date().toISOString().split('T')[0];
    dateInput.min = today;

    // Función para mostrar alertas
    function showAlert(message, type = 'success') {
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        document.body.appendChild(alert);

        // Remover alerta después de 3 segundos
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }

    // Manejar selección de mesa
    tables.forEach(table => {
        table.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Verificar si la mesa está ocupada
            if (this.classList.contains('occupied')) {
                showAlert('Esta mesa no está disponible', 'danger');
                return;
            }

            // Remover selección previa
            tables.forEach(t => t.classList.remove('selected'));
            
            // Seleccionar mesa actual
            this.classList.add('selected');
            tableInput.value = this.dataset.table;
            
            // Activar/desactivar botón de submit
            checkFormValidity();
            showAlert(`Mesa ${this.dataset.table} seleccionada`);
        });
    });

    // Validar formulario completo
    function checkFormValidity() {
        const isValid = tableInput.value && 
                       dateInput.value && 
                       timeInput.value && 
                       guestsInput.value;
        
        submitButton.disabled = !isValid;
    }

    // Escuchar cambios en todos los campos
    [dateInput, timeInput, guestsInput].forEach(input => {
        input.addEventListener('change', checkFormValidity);
    });

    // Prevenir envío si no hay mesa seleccionada
    form.addEventListener('submit', function(e) {
        if (!tableInput.value) {
            e.preventDefault();
            showAlert('Por favor, selecciona una mesa', 'danger');
        }
    });
});
