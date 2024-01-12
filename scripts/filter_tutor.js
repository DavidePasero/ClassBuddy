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

    // Make fetch API call to filter_tutors.php
    fetch('../backend/filter_tutors.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        return response.json();})
    .then(tutors => {
        if (tutors.error != null) {
            alert(tutors.error);
            return;
        }
        const tutorGrid = document.getElementById('image-grid');
        tutorGrid.innerHTML = '';

        for (const [_, tutor] of Object.entries(tutors)) {
        const div = document.createElement('div');
        div.className = 'tutor';

        // Create an img element for the profile picture
        let link = document.createElement('a');
        link.href = `profile.php?email=${tutor["email"]}`;
        const img = document.createElement('img');
        img.src = tutor["propic"];
        img.alt = `${tutor["firstname"]} ${tutor["lastname"]}`;
        img.className = 'profile-image';

        // Create divs for each piece of information
        const nameDiv = createInfoDiv('name', `${tutor["firstname"]} ${tutor["lastname"]}`);
        // const emailDiv = createInfoDiv('email', tutor["email"]);
        const cityDiv = createInfoDiv('city', tutor["citta"]);
        const onlineDiv = createInfoDiv('online', tutor["online"] ? 'Disponibile online' : ' ');
        const presenceDiv = createInfoDiv('presence', tutor["presenza"] ? 'Disponibile in presenza' : ' ');
        const insegnamentoDiv = createInfoDiv ("insegnamento", `${tutor["materia"]}: ${tutor["tariffa"]}€/ora`);

        link.appendChild(img);
        div.appendChild(link);
        div.appendChild(nameDiv);
        div.appendChild(cityDiv);
        div.appendChild(insegnamentoDiv);
        div.appendChild(onlineDiv);
        div.appendChild(presenceDiv);

        tutorGrid.appendChild(div);
    };
    })
    .catch(error => console.error('Error:', error));
});

function createInfoDiv(className, textContent) {
    const infoDiv = document.createElement('div');
    infoDiv.className = className;
    const textNode = document.createTextNode(textContent);
    infoDiv.appendChild(textNode);
    return infoDiv;
}
