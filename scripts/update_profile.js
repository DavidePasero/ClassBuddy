import { showPopup } from "./utils.js";

let editInfoButton = document.getElementById('edit-info-btn');
let editButton = document.getElementById('edit-button');
let fileInput = document.getElementById('propic');

let show_info = document.getElementById('show_info');
let edit_info = document.getElementById('edit_info');
editInfoButton.addEventListener('click', function (e) {
    e.preventDefault();
    if (show_info.style.display === 'none') {
        show_info.style.display = 'flex';
        edit_info.style.display = 'none';
    }
    else {
        show_info.style.display = 'none';
        edit_info.style.display = 'flex';
    }
});

editButton.addEventListener('click', openFileInput);
fileInput.addEventListener('change', previewImage);

function openFileInput(event) {
    event.preventDefault(); // input type file fa il submit del form, quindi lo evito
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

// Abilito drag and drop
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
    fetch('../res/insegnamenti.txt')
    .then(response => response.text())
    .then(data => {
        insegnamenti = data.split('\n');
    }).catch(err => console.error(err));

    // Event listener per il pulsante che aggiunge un insegnamento
    add_insegnamento.addEventListener('click', function (_) {
        let insegnamento = document.createElement('li');
        insegnamento.classList.add('insegnamento');

        // Rimuove gli insegnamenti presenti dall'elenco degli insegnamenti disponibili
        let insegnamenti_presenti = Array.from(insegnamenti_list.querySelectorAll('[name="materia[]"]')).map(e => e.textContent);
        let insegnamenti_disponibili = insegnamenti.filter(e => !insegnamenti_presenti.includes(e));
        
        // Crea un dropdown menu con gli insegnamenti disponibili
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

        // Crea un input type number per la tariffa
        let input_tariffa = document.createElement('input');
        input_tariffa.type = 'number';
        input_tariffa.min = 1;
        input_tariffa.max = 1000;
        input_tariffa.name = 'tariffa[]';
        input_tariffa.placeholder = '€/ora';
        input_tariffa.setAttribute('required', 'true');
        input_tariffa.classList.add('input-box');

        // Crea il pulsante per rimuovere l'insegnamento
        let removeButton = document.createElement('button');
        let removeIcon = document.createElement('img');
        removeIcon.src = '../res/icons/remove.svg';
        removeIcon.alt = 'Remove icon';
        removeButton.type = 'button';
        removeButton.appendChild(removeIcon);
        removeButton.classList.add('only-icon-button');
        removeButton.classList.add('btn');
        removeButton.classList.add('remove_insegnamento');

        // Aggiunge l'event listener per rimuovere l'insegnamento
        removeButton.addEventListener('click', function () {
            insegnamenti_list.removeChild(insegnamento);
        });

        insegnamento.appendChild(input_materia);
        insegnamento.appendChild(input_tariffa);
        insegnamento.appendChild(removeButton);

        insegnamenti_list.appendChild(insegnamento);
});

    // Aggiunge un event listener per ogni pulsante di rimozione insegnamento
    let removeButtons = document.getElementsByClassName('remove_insegnamento');
    for (let i = 0; i < removeButtons.length; i++) {
        removeButtons[i].addEventListener('click', function () {
            // Crea un input type hidden con il nome dell'insegnamento da rimuovere
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_insegnamento[]';
            input.value = removeButtons[i].parentElement.children[0].textContent;
            insegnamenti_list.parentElement.appendChild(input);
            insegnamenti_list.removeChild(removeButtons[i].parentElement);
        });
    }
}

// Il pulsante di submit invia una chiamata fetch al server per aggiornare il profilo
const submitButton = document.getElementById('submit_button');
if (submitButton !== null) {
    submitButton.addEventListener('click', (event) => {
    event.preventDefault();
    const form = document.forms.update_profile;

    // Verifica che nessun campo sia vuoto
    let inputs = form.querySelectorAll('input[type="text"], input[type="number"], select');
    let compiled = true;
    inputs.forEach(element => {
        if (element.value == "")
            compiled = false;
    });

    if (compiled) {
        // Crea un oggetto FormData per raccogliere i dati del modulo
        const formData = new FormData(form);

        // Per il nome e cognome ricostruisco la span con i nuovi valori (simulo un click sul pulsante di modifica)
        if (show_info.style.display === 'none') {
            document.getElementById('name-span').textContent = formData.get('firstname') + " " + formData.get('lastname');
            editInfoButton.click();
        }

        if (is_tutor) {
            // Converti gli elementi materia e tariffa in elementi <span>
            // Recupera tutti gli elementi di input
            const materie = insegnamenti_list.querySelectorAll('select[name="materia[]"]');
            const tariffe = insegnamenti_list.querySelectorAll('[name="tariffa[]"][type="number"]');

            function replaceWithSpan(elems) {
                for (let i = 0; i < elems.length; i++) {
                    // Crea un elemento span con lo stesso contenuto dell'elemento di input
                    const span = document.createElement('span');
                    span.textContent = elems[i].value + " ";
                    // Aggiungi €/ora se l'elemento di input è l'elemento tariffa
                    if (elems[i].name === 'tariffa[]')
                        span.textContent += '€/ora';
                    
                    // Sostituisci l'elemento di input con l'elemento span
                    elems[i].parentElement.replaceChild(span, elems[i]);
                }
            }

            replaceWithSpan(materie);
            replaceWithSpan(tariffe);
        }

        // Invia i dati del modulo al server tramite fetch
        fetch('../backend/update_profile.php', {
            method: 'POST',
            body: formData
        })
            .then((response) => response.json())
            .then((data) => {
            if (data.error) {
                showPopup(data.error, true);
                return;
            }
            showPopup(data.status, false);
            })
            .catch((err) => console.error(err));
    } else {
      showPopup('Compila tutti i campi', true);
    }
  });
}
