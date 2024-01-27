import { fill_tutor_grid } from "./utils.js";

// Riempie il menu a discesa con le città
const selectCity = document.getElementById("cittaDropdown");
const cittaInput = document.getElementById("cittaInput");

// Recupera l'elenco delle città dal file
// legge il file cities.txt che è un elenco di tutte le città italiane separate da un a capo e le inserisce in un array
const cities = [];
fetch('../res/citta.txt')
  .then(response => response.text())
  .then(data => {
    cities = data.split('\n');
    // Inserisce l'elenco delle città nel menu a discesa
    cities.forEach(city => {
      const option = document.createElement("option");
      option.textContent = city;
      option.value = city;
      selectCity.appendChild(option);
    });
  })
  .catch(err => console.error(err));

// Quando l'utente seleziona una città dal menu a discesa, il campo di input viene compilato automaticamente
selectCity.addEventListener("change", function() {
  cittaInput.value = selectCity.value;
});

// Riempie il dropdown menu di materia
const materia = document.getElementById("materia");
const insegnamenti = [];
// Recupera l'elenco degli insegnamenti
fetch('../res/insegnamenti.txt')
  .then(response => response.text())
  .then(data => {
    insegnamenti = data.split('\n');
    insegnamenti.forEach(insegnamento => {
      const option = document.createElement("option");
      option.textContent = insegnamento;
      option.value = insegnamento;
      materia.appendChild(option);
    });
  }).catch(err => console.error(err));


document.getElementById('submit-button').addEventListener('click', function (e) {
  e.preventDefault();
  // Recupera i dati del modulo
  const formData = new FormData(document.getElementById('filter-form'));
  formData.append("action", "filter_tutors");
  // Esegue la chiamata API fetch a filter_tutors.php
  fetch('../backend/tutor.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    return response.json();})
  .then(tutors => {
    fill_tutor_grid(tutors);
  })
  .catch(error => console.error('Errore:', error));
});
