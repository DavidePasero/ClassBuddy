let form = document.getElementById ("form");
let sumbit = document.getElementById ("submit");
let getCurrentLocationButton = document.getElementById ("getCurrentLocation");
let selectCity = document.getElementById("cittaDropdown");
let cittaInput = document.getElementById("cittaInput");

let small_error_messages = {
    "firstname": create_small_error_message ("Un nome vuoto non è valido"),
    "lastname": create_small_error_message ("Un cognome vuoto non è valido"),
    "email": create_small_error_message ("L'email non è valida"),
    "existing_email": create_small_error_message ("Questa email è già stata usata"),
    "pass": create_small_error_message ("La password deve avere almeno 8 caratteri"),
    "confirm": create_small_error_message ("Le due password devono essere uguali"),
    "role": create_small_error_message ("Il ruolo non è valido"),
    "city": create_small_error_message ("La città non è valida"),
    "online_presenza": create_small_error_message ("Devi scegliere se vuoi fare lezioni online o in presenza"),
    "submit": create_small_error_message ("Tutti i campi devono essere validi per completare la registrazione")
};

function create_small_error_message (text) {
    let p = document.createElement("p");
    let textnode = document.createTextNode(text);
    p.appendChild (textnode);
    p.setAttribute ("class", "form-element small-error-message");
    return p;
}

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

form.addEventListener ("input", function (event) {
    switch (event.target.id) {
        case "firstname":
            check_firstname ();
            break;
        case "lastname":
            check_lastname ();
            break;
        case "email":
            check_email ();
            break;
        case "pass":
            check_pass ();
            break;
        case "confirm":
            check_pass ();
            break;
        case "student":
            check_role ();
            break;
        case "tutor":
            check_role ();
            break;
        case "online":
            check_online_presenza ();
            break;
        case "presenza":
            check_online_presenza ();
            break;
        case "cittaInput":
            check_city ();
            break;
    }
});

submit.addEventListener ("click", check_submit);


// Add an event listener for the data list
cittaInput.addEventListener("input", function(event) {
    // Get the user's input
    var input = event.target.value;

    // Filter the list of cities based on the user's input
    var filteredCities = cities.filter(function(city) {
        return city.toLowerCase().indexOf(input.toLowerCase()) !== -1;
    });

    // Clear the dropdown menu
    while (selectCity.firstChild) {
        selectCity.removeChild(selectCity.firstChild);
    }

    // Insert the filtered list of cities into the dropdown menu
    filteredCities.forEach(function(city) {
        var option = document.createElement("option");
        option.text = city;
        option.value = city;
        selectCity.appendChild(option);
    });
});


/* 
    getCurrentLocationButton listens to the click event and uses the geolocation API 
    to get current location of the user
*/
getCurrentLocationButton.addEventListener ("click", function (event) {
    // Check if the Geolocation API is supported
    if (navigator.geolocation) {
        // Get the current position
        navigator.geolocation.getCurrentPosition(function(position) {
        // Use the latitude and longitude to get the city name
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        var url = "https://nominatim.openstreetmap.org/reverse?lat=" + lat + "&lon=" + lon + "&format=json&accept-language=it";
        fetch(url)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                let city;
                // Check if city is defined, if not checks if town is defined, if not checks if village is defined, then extracts the name
                if (data.address.city != undefined) {
                    city = data.address.city;
                }
                else if (data.address.town != undefined) {
                    city = data.address.town;
                }
                else {
                    city = data.address.village;
                }

                //city = (data.address.city != undefined) ? data.address.city : (data.address.town != undefined) ? data.address.town : data.address.village;
                
                // Set the value of the input field
                cittaInput.value = city;
            });
        },
        function(error) {
            console.log ("Error during geolocation: " + error.message);
        },
        {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 10000
        });
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
});


// Check nome e cognome
function check_text_field (div, text_field) {
    let last_div_elem = div.lastElementChild;
    // Se il campo è vuoto e non c'è già un messaggio di errore, lo aggiunge
    if (text_field.value.length == 0 && last_div_elem.className.indexOf ("small-error-message") === -1)
        div.appendChild (small_error_messages[text_field.id]);
    // Se il campo non è vuoto e c'è un messaggio di errore, lo rimuove
    else if (text_field.value.length != 0 && last_div_elem.className.indexOf ("small-error-message") !== -1) 
        div.removeChild (last_div_elem);

    return text_field.value.length != 0;
}


function check_firstname () {
    return check_text_field (document.getElementById ("firstname_div"), document.getElementById ("firstname"));
}

function check_lastname () {
    return check_text_field (document.getElementById ("lastname_div"), document.getElementById ("lastname"));
}


// Check email
// I need the global variable for the fetch function
let email_ok = false;

function check_email () {
    let email_div = document.getElementById ("email_div");
    let email = document.getElementById ("email");
    let last_div_elem = email_div.lastElementChild;
    let re = /\S+@\S+\.\S+/;
    email_ok = re.test(email.value);

    if (!email_ok && last_div_elem.className.indexOf ("small-error-message") === -1)
        email_div.appendChild (small_error_messages["email"]);
    else if (email_ok && last_div_elem.className.indexOf ("small-error-message") !== -1)
        email_div.removeChild (last_div_elem);

    // Update di last_div_elem
    last_div_elem = email_div.lastElementChild;

    if (email_ok) {
        // Checks if email is already in the database
        fetch ("../backend/email_exists.php",
        {
            method: "POST",
            body: "email=" + email.value,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            }
        }).then(response => response.text())
        .then(data => {
            // Mutuamente esclusivi: una email non valida non può essere già nel database
            if (data == "true" && last_div_elem.className.indexOf ("small-error-message") === -1) {
                email_ok = false;
                email_div.appendChild (small_error_messages["existing_email"]);
            }
            else if (data == "false" && last_div_elem.className.indexOf ("small-error-message") !== -1)
                email_div.removeChild (last_div_elem);
        })
        .catch(error => {
            console.error("Error:", error);
        });
    }
}

// Check password e conferma password
function check_pass () {
    let pass = document.getElementById ("pass");
    let confirm = document.getElementById ("confirm");
    let pass_div = document.getElementById ("pass_div");
    let last_pass_div_elem = pass_div.lastElementChild;
    let confirm_div = document.getElementById ("confirm_div");
    let last_confirm_div_elem = confirm_div.lastElementChild;

    if (pass.value.length < 8 && last_pass_div_elem.className.indexOf ("small-error-message") === -1)
        pass_div.appendChild (small_error_messages["pass"]);
    else if (pass.value.length >= 8 && last_pass_div_elem.className.indexOf ("small-error-message") !== -1)
        pass_div.removeChild (last_pass_div_elem);

    if (confirm.value != pass.value && last_confirm_div_elem.className.indexOf ("small-error-message") === -1)
        confirm_div.appendChild (small_error_messages["confirm"]);
    else if (confirm.value == pass.value && last_confirm_div_elem.className.indexOf ("small-error-message") !== -1)
        confirm_div.removeChild (last_confirm_div_elem);

    return pass.value.length >= 8 && confirm.value == pass.value;
}


// Check ruolo
function check_role () {
    let student = document.getElementById ("student");
    let tutor = document.getElementById ("tutor");
    let role_div = document.getElementById ("role_div");
    let last_div_elem = role_div.lastElementChild;

    let online_presenza_div = document.getElementById ("online_presenza_div");
    let location_div = document.getElementById ("location_div");

    if (tutor.checked) {
        online_presenza_div.style.display = "flex";
        location_div.style.display = "flex";
    } else {
        online_presenza_div.style.display = "none";
        location_div.style.display = "none";
    }

    if (!student.checked && !tutor.checked) {
        if (last_div_elem.className.indexOf ("small-error-message") === -1)
            role_div.appendChild (small_error_messages["role"]);
        return false;
    }
    else {
        if (last_div_elem.className.indexOf ("small-error-message") !== -1)
            role_div.removeChild (last_div_elem);
        return true;
    }
}


// Check if at least one of the two checkboxes is checked
function check_online_presenza () {
    let online = document.getElementById ("online");
    let presenza = document.getElementById ("presenza");
    let online_presenza_div = document.getElementById ("online_presenza_div");
    let last_div_elem = online_presenza_div.lastElementChild;

    if (!online.checked && !presenza.checked) {
        if (last_div_elem.className.indexOf ("small-error-message") === -1)
            online_presenza_div.appendChild (small_error_messages["online_presenza"]);
        return false;
    }
    else {
        if (last_div_elem.className.indexOf ("small-error-message") !== -1)
            online_presenza_div.removeChild (last_div_elem);
        return true;
    }
}


// Check città
function check_city () {
    let city = document.getElementById ("cittaInput");
    let location_div = document.getElementById ("location_div");
    let last_div_elem = location_div.lastElementChild;

    if (cities.includes (city.value)) {
        // Remove the error message if it's there
        if (last_div_elem.className.indexOf ("small-error-message") !== -1)
            location_div.removeChild (last_div_elem);
        return true;
    } else {
        if (last_div_elem.className.indexOf ("small-error-message") === -1)
            location_div.appendChild (small_error_messages["city"]);
        return false;
    }
}


// Checks the entire form to see if it's ready to be submitted
function check_submit (event) {
    let div = document.getElementById ("submit_div");
    let tutor = document.getElementById ("tutor");
    // Check everything if the tutor checkbox is checked, else check only the student fields (no online/presenza and no citta)
    if (!(check_firstname () && check_lastname () && email_ok && check_pass () && check_role () &&
        (!tutor.checked || (check_online_presenza () && check_city ())))) {
        event.preventDefault();
        if (div.children.length == 1)
            div.appendChild (small_error_messages["submit"]);            
    }
}