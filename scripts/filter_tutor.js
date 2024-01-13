import { fill_tutor_grid } from "./utils.js";

// Fill dropdown menu with cities
let selectCity = document.getElementById("cittaDropdown");
let cittaInput = document.getElementById("cittaInput");

// Get the list of cities from the file
// reads the file cities.txt which is a list of all the cities in Italy separated by a newline an puts them in an array
let cities = [];
fetch('../res/citta.txt')
  .then(response => response.text())
  .then(data => {
    cities = data.split('\n');
    // Insert the list of cities into the dropdown menu
    cities.forEach(function(city) {
        var option = document.createElement("option");
        option.text = city;
        option.value = city;
        selectCity.appendChild(option);
    });
})
.catch(err => console.error(err));

// When the user selects a city from the dropdown menu, the input field is automatically filled
selectCity.addEventListener('change', function() {
    cittaInput.value = selectCity.value;
});

// Fill materia dropdown menu
let materia = document.getElementById("materia");
let insegnamenti = [];
// Fetch the list of insegnamenti
fetch('../res/insegnamenti.txt')
.then(response => response.text())
.then(data => {
    insegnamenti = data.split('\n');
    insegnamenti.forEach(function(insegnamento) {
        var option = document.createElement("option");
        option.text = insegnamento;
        option.value = insegnamento;
        materia.appendChild(option);
    });
}).catch(err => console.error(err));


document.getElementById('submit-button').addEventListener('click', function (e) {
    e.preventDefault();
    // Get form data
    var formData = new FormData(document.getElementById('filter-form'));
    formData.append ('action', 'filter_tutors')
    // Make fetch API call to filter_tutors.php
    fetch('../backend/tutor.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        return response.json();})
    .then(tutors => {
        fill_tutor_grid(tutors);
    })
    .catch(error => console.error('Error:', error));
});