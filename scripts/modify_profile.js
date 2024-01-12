let editButton = document.getElementById('edit-button');
let fileInput = document.getElementById('propic');

editButton.addEventListener('click', openFileInput);
fileInput.addEventListener('change', previewImage);

function openFileInput(event) {
    event.preventDefault(); // Prevents the form from submitting and avoids bugs with the file input
    fileInput.click();
}

// Preview image
function previewImage() {
    const file = fileInput.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Enable drag and drop
const imageDiv = document.getElementById('image_div');
imageDiv.addEventListener('dragover', function (e) {
    e.preventDefault();
    if (!imageDiv.classList.contains('dragover')) {
        imageDiv.classList.add('dragover');
    }
});

imageDiv.addEventListener('dragleave', function () {
    imageDiv.classList.remove('dragover');
});

imageDiv.addEventListener('drop', function (e) {
    e.preventDefault();
    imageDiv.classList.remove('dragover');

    // Prendo il file
    const file = e.dataTransfer.files[0];
    // Aggiorno anche il fileInput
    fileInput.files = e.dataTransfer.files;

    // Se c'è un file lo mostro
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});

let is_tutor = document.getElementById('insegnamenti_container') != null;
let insegnamenti = [];
let insegnamenti_list = document.getElementById('insegnamenti_list');
let add_insegnamento = document.getElementById('add_insegnamento');

// Aggiunta degli insegnamenti
if (is_tutor) {
    // Fetch the list of insegnamenti
    fetch('../res/insegnamenti.txt')
    .then(response => response.text())
    .then(data => {
        insegnamenti = data.split('\n');
    }).catch(err => console.error(err));

    // Add event listener for the button that adds an insegnamento
    add_insegnamento.addEventListener('click', function (_) {
        let insegnamento = document.createElement('li');
        insegnamento.classList.add('insegnamento');

        // Remove from insegnamenti the insegnamenti already present in the database
        let insegnamenti_presenti = Array.from(insegnamenti_list.querySelectorAll('[name="materia[]"]')).map(e => e.textContent);
        let insegnamenti_disponibili = insegnamenti.filter(e => !insegnamenti_presenti.includes(e));
        
        // Create a dropdown menu with name insegnamento with all the insegnamenti
        let input_materia = document.createElement('select');
        input_materia.name = 'materia[]';
        input_materia.setAttribute('required', 'true');
        input_materia.classList.add('input-box');
        insegnamenti_disponibili.forEach(element => {
            let option = document.createElement('option');
            option.value = element;
            option.text = element;
            input_materia.appendChild(option);
        });

        // Create a number input with name tariffa
        let input_tariffa = document.createElement('input');
        input_tariffa.type = 'number';
        input_tariffa.min = 1;
        input_tariffa.max = 1000;
        input_tariffa.name = 'tariffa[]';
        input_tariffa.placeholder = 'Tariffa oraria';
        input_tariffa.setAttribute('required', 'true');
        input_tariffa.classList.add('input-box');

        // Create a button to remove the insegnamento
        let removeButton = document.createElement('button');
        let removeIcon = document.createElement('img');
        removeIcon.src = '../res/icons/remove.svg';
        removeIcon.alt = 'Remove icon';
        removeButton.type = 'button'; // Ensure it doesn't submit the form
        removeButton.appendChild(removeIcon);
        removeButton.classList.add('only-icon-button');
        removeButton.classList.add('btn');
        removeButton.classList.add('remove_insegnamento');

        // Add event listener to the remove button
        removeButton.addEventListener('click', function () {
            insegnamenti_list.removeChild(insegnamento);
        });

        insegnamento.appendChild(input_materia);
        insegnamento.appendChild(input_tariffa);
        insegnamento.appendChild(removeButton);

        insegnamenti_list.appendChild(insegnamento);
});

    // Add event listener for the button that removes an insegnamento already present in the database
    let removeButtons = document.getElementsByClassName('remove_insegnamento');
    for (let i = 0; i < removeButtons.length; i++) {
        removeButtons[i].addEventListener('click', function () {
            // Creates an hidden input with the value of the removed insegnamento and adds it to the form
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_insegnamento[]';
            input.value = removeButtons[i].parentElement.children[0].textContent;
            insegnamenti_list.parentElement.appendChild(input);
            insegnamenti_list.removeChild(removeButtons[i].parentElement);
        });
    }
}

// The submit button executes a fetch call to the server to update the profile
let submitButton = document.getElementById('submit');
if (submitButton != null) {
    submitButton.addEventListener('click', function (event) {
        event.preventDefault();
        let form = document.forms.modify_profile;

        // Check if all the fields are not empty
        let inputs = form.querySelectorAll('input[type="text"], input[type="number"], select');
        let compiled = true;
        inputs.forEach(element => {
            if (element.value == "") {
                compiled = false;
            }
        });

        if (compiled) {
            // Create FormData object to gather form data
            var formData = new FormData(form);
            
            if (is_tutor) {
                // Make materia e tariffa elements to be <span> elements
                // Get all the input elements
                let materie = insegnamenti_list.querySelectorAll('select[name="materia[]"]');
                let tariffe = insegnamenti_list.querySelectorAll('[name="tariffa[]"][type="number"]');

                function replaceWithSpan(elems) {
                    for (let i = 0; i < elems.length; i++) {
                        // Create a span element with the same content of the input element
                        let span = document.createElement('span');
                        span.textContent = elems[i].value + " ";
                        // Add €/h if the input element is the tariffa input
                        if (elems[i].name == 'tariffa[]') {
                            span.textContent += '€/h';
                        }
                        // Replace the input element with the span element
                        elems[i].parentElement.replaceChild(span, elems[i]);
                    }
                }

                replaceWithSpan(materie);
                replaceWithSpan(tariffe);
            }
        
            // Send form data to the server using fetch
            fetch('../backend/modify_profile.php', {
                method: 'POST',
                body: formData
            })
            .catch(err => console.error(err));
        }
        else {
            alert('Compila tutti i campi');
        }
    });
}