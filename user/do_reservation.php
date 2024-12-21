<?php
    require_once '../includes/header.php';


?>

<div class="containerR">
        <div class="form-sectionR">
            <h2>Reserva tu Mesa</h2>
            <form>
                <label for="date">Fecha</label>
                <input type="date" id="date" name="date" required>

                <label for="time">Hora</label>
                <input type="time" id="time" name="time" required>

                <label for="guests">Número de Personas</label>
                <input type="number" id="guests" name="guests" min="1" max="10" required>

                <label for="occasion">Ocasión Especial</label>
                <input type="text" id="occasion" name="occasion" placeholder="Cumpleaños, Aniversario, etc.">

                <button type="submit">Reservar Ahora</button>
            </form>
        </div>

        <div class="map-section">
            <h2>Distribución de Mesas</h2>
            <div>
                <div class="table available">1</div>
                <div class="table occupied">2</div>
                <div class="table available">3</div>
                <div class="table available">4</div>
                <div class="table available">5</div>
                <div class="table available">6</div>
                <div class="table available">7</div>
                <div class="table available">8</div>
                <div class="table available">9</div>
                <div class="table available">10</div>
            </div>
            <div class="legend">
                <div><span class="available-color"></span> Disponible</div>
                <div><span class="occupied-color"></span> Ocupada</div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>