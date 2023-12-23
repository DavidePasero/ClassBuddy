let form = document.getElementById ("form");
let sumbit = document.getElementById ("submit");
let getCurrentLocationButton = document.getElementById ("getCurrentLocation");
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


form.addEventListener ("input", function (event) {
    switch (event.target.type) {
        case "text":
            check_text_fields (event.target);
            break;
        case "email":
            check_email ();
            break;
        case "password":
            check_pass ();
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
    to get current location of the user and console.logs the name of the city
*/
getCurrentLocationButton.addEventListener ("click", function (event) {
    // Check if the Geolocation API is supported
    if (navigator.geolocation) {
        // Get the current position
        navigator.geolocation.getCurrentPosition(function(position) {
        // Use the latitude and longitude to get the city name
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        var url = "https://nominatim.openstreetmap.org/reverse?lat=" + lat + "&lon=" + lon + "&format=json";
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
            maximumAge: Infinity
        });
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
});


let small_error_messages = {
    "firstname": create_small_error_message ("Un nome vuoto non è valido"),
    "lastname": create_small_error_message ("Un cognome vuoto non è valido"),
    "email": create_small_error_message ("L'email non è valida"),
    "existing_email": create_small_error_message ("Questa email è già stata usata"),
    "pass": create_small_error_message ("La password deve avere almeno 8 caratteri"),
    "confirm": create_small_error_message ("Le due password devono essere uguali"),
    "submit": create_small_error_message ("Tutti i campi devono essere validi per completare la registrazione")
};

function create_small_error_message (text) {
    let p = document.createElement("p");
    let textnode = document.createTextNode(text);
    p.appendChild (textnode);
    p.setAttribute ("class", "form-element small-error-message");
    return p;
}

let text_fields_ok = false;

function check_text_fields () {
    let divs = [document.getElementById ("firstname_div"), document.getElementById ("lastname_div")];
    for (let div of divs) {
        if (div.children[0].value == "" && div.children.length == 1)
            div.appendChild (small_error_messages[div.children[0].id]);
        else if (div.children[0].value != "" && div.children.length > 1)
            div.removeChild (div.children[1]);
    }

    text_fields_ok = divs[0].children[0].value != "" && divs[1].children[0].value != "";
}

let email_ok = false;

function check_email () {
    let email_div = document.getElementById ("email_div");
    let re = /\S+@\S+\.\S+/;
    email_ok = re.test(email_div.children[0].value);
    if (!email_ok && email_div.children.length == 1)
        email_div.appendChild (small_error_messages["email"]);
    else if (email_ok && email_div.children.length > 1)
        email_div.removeChild (email_div.children [1]);

    console.log ("Email ok in check_email prima: " + email_ok);

    if (email_ok) {
        // Checks if email is already in the database
        fetch ("../backend/email_exists.php",
        {
            method: "POST",
            body: "email=" + email_div.children[0].value,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
        }).then(response => response.text())
        .then(data => {
            // Mutuamente esclusivi: una email non valida non può essere già nel database
            if (data == "true" && email_div.children.length == 1) {
                email_ok = false;
                email_div.appendChild (small_error_messages["existing_email"]);
            }
            else if (data == "false" && email_div.children.length > 1)
                email_div.removeChild (email_div.children [1]);

            console.log ("Email ok in check_email dopo: " + email_ok);
        })
        .catch(error => {
            console.error("Error:", error);
        });
    }
}

let pass_ok = false;

function check_pass () {
    let pass = document.getElementById ("pass");
    let confirm = document.getElementById ("confirm");
    let pass_div = document.getElementById ("pass_div");
    let confirm_div = document.getElementById ("confirm_div");

    if (pass.value.length < 8 && pass_div.children.length == 1)
        pass_div.appendChild (small_error_messages["pass"]);
    else if (pass.value.length >= 8 && pass_div.children.length > 1)
        pass_div.removeChild (pass_div.children [1]);

    if (confirm.value != pass.value && confirm_div.children.length == 1)
        confirm_div.appendChild (small_error_messages["confirm"]);
    else if (confirm.value == pass.value && confirm_div.children.length > 1)
        confirm_div.removeChild (confirm_div.children [1]);

    pass_ok = pass.value.length >= 8 && confirm.value == pass.value;
}

function check_submit (event) {
    let div = document.getElementById ("submit_div");
    if (!text_fields_ok || !email_ok || !pass_ok) {
        console.log ("text fields: " + text_fields_ok + "; email_ok: " + email_ok + "; pass_ok: " + pass_ok);
        console.log ("CACCAPUPU");
        event.preventDefault();
        if (div.children.length == 1)
            div.appendChild (small_error_messages["submit"]);            
    }
}