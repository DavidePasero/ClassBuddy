let form = document.getElementById("form");
let submit = document.getElementById("submit");
let getCurrentLocationButton = document.getElementById("getCurrentLocation");
let selectCity = document.getElementById("cittaDropdown");
let cityInput = document.getElementById("cittaInput");

let small_error_messages = {
    "firstname": create_small_error_message("Un nome vuoto non è valido"),
    "lastname": create_small_error_message("Un cognome vuoto non è valido"),
    "email": create_small_error_message("L'email non è valida"),
    "existing_email": create_small_error_message("Questa email è già stata usata"),
    "pass": create_small_error_message("La password deve avere almeno 8 caratteri"),
    "confirm": create_small_error_message("Le due password devono essere uguali"),
    "role": create_small_error_message("Il ruolo non è valido"),
    "city": create_small_error_message("La città non è valida"),
    "online_presenza": create_small_error_message("Devi scegliere se vuoi fare lezioni online o in presenza"),
    "submit": create_small_error_message("Tutti i campi devono essere validi per completare la registrazione")
};

function create_small_error_message(text) {
    let p = document.createElement("p");
    let textnode = document.createTextNode(text);
    p.appendChild(textnode);
    p.setAttribute("class", "form-element small-error-message");
    return p;
}

// Ottieni l'elenco delle città dal file
// legge il file cities.txt che è un elenco di tutte le città italiane separate da una nuova riga e le inserisce in un array
let cities = [];
fetch('../res/citta.txt')
    .then(response => response.text())
    .then(data => {
        cities = data.split('\n');
        // Inserisci l'elenco delle città nel menu a discesa
        cities.forEach(function (city) {
            var option = document.createElement("option");
            option.text = city;
            option.value = city;
            selectCity.appendChild(option);
        });
    })
    .catch(err => console.error(err));

form.addEventListener("input", function (event) {
    switch (event.target.id) {
        case "firstname":
            check_firstname();
            break;
        case "lastname":
            check_lastname();
            break;
        case "email":
            check_email();
            break;
        case "pass":
            check_pass();
            break;
        case "confirm":
            check_pass();
            break;
        case "student":
            check_role();
            break;
        case "tutor":
            check_role();
            break;
        case "online":
            check_online_presenza();
            break;
        case "presenza":
            check_online_presenza();
            break;
        case "cittaInput":
            check_city();
            break;
    }
});

submit.addEventListener("click", check_submit);

// Aggiungi un listener per la lista dei dati
cityInput.addEventListener("input", function (event) {
    // Ottieni l'input dell'utente
    var input = event.target.value;

    // Filtra l'elenco delle città in base all'input dell'utente
    var filteredCities = cities.filter(function (city) {
        return city.toLowerCase().indexOf(input.toLowerCase()) !== -1;
    });

    // Cancella il dropdown menu
    while (selectCity.firstChild) {
        selectCity.removeChild(selectCity.firstChild);
    }

    // Inserisci l'elenco filtrato delle città nel dropdown menu
    filteredCities.forEach(function (city) {
        var option = document.createElement("option");
        option.text = city;
        option.value = city;
        selectCity.appendChild(option);
    });
});

/*
    getCurrentLocationButton ascolta l'evento di click e utilizza l'API di geolocalizzazione
    per ottenere la posizione attuale dell'utente
*/
getCurrentLocationButton.addEventListener("click", function (event) {
    // Verifica se l'API di geolocalizzazione è supportata
    if (navigator.geolocation) {
        // Ottieni la posizione attuale
        navigator.geolocation.getCurrentPosition(function (position) {
            // Utilizza la latitudine e la longitudine per ottenere il nome della città
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            var url = "https://nominatim.openstreetmap.org/reverse?lat=" + lat + "&lon=" + lon + "&format=json&accept-language=it";
            fetch(url)
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    let city;
                    // Verifica se city è definito, se no verifica se town è definito, se no verifica se village è definito, quindi estrae il nome
                    if (data.address.city != undefined)
                        city = data.address.city;
                    else if (data.address.town != undefined)
                        city = data.address.town;
                    else
                        city = data.address.village;

                    // Imposta il valore del campo di input
                    cityInput.value = city;
                });
            },
            function (error) {
                console.log("Errore durante la geolocalizzazione: " + error.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 10000
            });
    } else {
        console.log("La geolocalizzazione non è supportata da questo browser.");
    }
});

// Controlla nome e cognome
function check_text_field(div, text_field) {
    let last_div_elem = div.lastElementChild;
    // Se il campo è vuoto e non c'è già un messaggio di errore, lo aggiunge
    if (text_field.value.length == 0 && last_div_elem.className.indexOf("small-error-message") === -1)
        div.appendChild(small_error_messages[text_field.id]);
    // Se il campo non è vuoto e c'è un messaggio di errore, lo rimuove
    else if (text_field.value.length != 0 && last_div_elem.className.indexOf("small-error-message") !== -1)
        div.removeChild(last_div_elem);

    return text_field.value.length != 0;
}

function check_firstname() {
    return check_text_field(document.getElementById("firstname_div"), document.getElementById("firstname"));
}

function check_lastname() {
    return check_text_field(document.getElementById("lastname_div"), document.getElementById("lastname"));
}

// Controlla l'email
// Ho bisogno della variabile globale per la funzione di fetch
let email_ok = false;

function check_email() {
    let email_div = document.getElementById("email_div");
    let email = document.getElementById("email");
    let last_div_elem = email_div.lastElementChild;
    let re = /\S+@\S+\.\S+/;
    email_ok = re.test(email.value);

    if (!email_ok && last_div_elem.className.indexOf("small-error-message") === -1)
        email_div.appendChild(small_error_messages["email"]);
    else if (email_ok && last_div_elem.className.indexOf("small-error-message") !== -1)
        email_div.removeChild(last_div_elem);

    // Aggiornamento di last_div_elem
    last_div_elem = email_div.lastElementChild;

    if (email_ok) {
        // Verifica se l'email è già nel database
        fetch("../backend/email_exists.php",
            {
                method: "POST",
                body: "email=" + email.value,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                }
            }).then(response => response.text())
            .then(data => {
                // Mutuamente esclusivi: una email non valida non può essere già nel database
                if (data == "true" && last_div_elem.className.indexOf("small-error-message") === -1) {
                    email_ok = false;
                    email_div.appendChild(small_error_messages["existing_email"]);
                } else if (data == "false" && last_div_elem.className.indexOf("small-error-message") !== -1)
                    email_div.removeChild(last_div_elem);
            })
            .catch(error => {
                console.error("Errore:", error);
            });
    }
}

// Controlla password e conferma password
function check_pass() {
    let pass = document.getElementById("pass");
    let confirm = document.getElementById("confirm");
    let pass_div = document.getElementById("pass_div");
    let last_pass_div_elem = pass_div.lastElementChild;
    let confirm_div = document.getElementById("confirm_div");
    let last_confirm_div_elem = confirm_div.lastElementChild;

    if (pass.value.length < 8 && last_pass_div_elem.className.indexOf("small-error-message") === -1)
        pass_div.appendChild(small_error_messages["pass"]);
    else if (pass.value.length >= 8 && last_pass_div_elem.className.indexOf("small-error-message") !== -1)
        pass_div.removeChild(last_pass_div_elem);

    if (confirm.value != pass.value && last_confirm_div_elem.className.indexOf("small-error-message") === -1)
        confirm_div.appendChild(small_error_messages["confirm"]);
    else if (confirm.value == pass.value && last_confirm_div_elem.className.indexOf("small-error-message") !== -1)
        confirm_div.removeChild(last_confirm_div_elem);

    return pass.value.length >= 8 && confirm.value == pass.value;
}

// Controlla ruolo
function check_role() {
    let student = document.getElementById("student");
    let tutor = document.getElementById("tutor");
    let role_div = document.getElementById("role_div");
    let last_div_elem = role_div.lastElementChild;

    let online_presenza_div = document.getElementById("online_presenza_div");
    let location_div = document.getElementById("location_div");

    if (tutor.checked) {
        online_presenza_div.style.display = "flex";
        location_div.style.display = "flex";
    } else {
        online_presenza_div.style.display = "none";
        location_div.style.display = "none";
    }

    if (!student.checked && !tutor.checked) {
        if (last_div_elem.className.indexOf("small-error-message") === -1)
            role_div.appendChild(small_error_messages["role"]);
        return false;
    } else {
        if (last_div_elem.className.indexOf("small-error-message") !== -1)
            role_div.removeChild(last_div_elem);
        return true;
    }
}

// Controlla se almeno una delle due checkbox è selezionata
function check_online_presenza() {
    let online = document.getElementById("online");
    let presenza = document.getElementById("presenza");
    let online_presenza_div = document.getElementById("online_presenza_div");
    let last_div_elem = online_presenza_div.lastElementChild;

    if (!online.checked && !presenza.checked) {
        if (last_div_elem.className.indexOf("small-error-message") === -1)
            online_presenza_div.appendChild(small_error_messages["online_presenza"]);
        return false;
    } else {
        if (last_div_elem.className.indexOf("small-error-message") !== -1)
            online_presenza_div.removeChild(last_div_elem);
        return true;
    }
}

// Controlla città
function check_city() {
    let city = document.getElementById("cittaInput");
    let location_div = document.getElementById("location_div");
    let last_div_elem = location_div.lastElementChild;

    if (cities.includes(city.value)) {
        // Rimuovi il messaggio di errore se presente
        if (last_div_elem.className.indexOf("small-error-message") !== -1)
            location_div.removeChild(last_div_elem);
        return true;
    } else {
        if (last_div_elem.className.indexOf("small-error-message") === -1)
            location_div.appendChild(small_error_messages["city"]);
        return false;
    }
}

// Controlla l'intero modulo per vedere se è pronto per essere inviato
function check_submit(event) {
    let div = document.getElementById("submit_div");
    let tutor = document.getElementById("tutor");
    // Se la checkbox tutor è selezionata allora controlla anche online_presenza e city
    if (!(check_firstname() && check_lastname() && email_ok && check_pass() && check_role() &&
        (!tutor.checked || (check_online_presenza() && check_city())))) {
        event.preventDefault();
        if (div.children.length == 1)
            div.appendChild(small_error_messages["submit"]);
    }
}
