let form = document.getElementById ("form");
let sumbit = document.getElementById ("submit");

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

let small_error_messages = {
    "firstname": create_small_error_message ("Un nome vuoto non è valido"),
    "lastname": create_small_error_message ("Un cognome vuoto non è valido"),
    "email": create_small_error_message ("L'email non è valida"),
    "existing_email": create_small_error_message ("Questa email è già stata usata"),
    "pass": create_small_error_message ("La password deve avere almeno 8 caratteri"),
    "confirm": create_small_error_message ("Le due password devono essere uguali"),
    "submit": create_small_error_message ("Tutti i campi devono essere validi per completare la registrazione")
};

submit.addEventListener ("click", check_submit);


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

let email_ok = false

function check_email () {
    let email_div = document.getElementById ("email_div");
    let re = /\S+@\S+\.\S+/;
    let email_ok = re.test(email_div.children[0].value);
    if (!email_ok && email_div.children.length == 1)
        email_div.appendChild (small_error_messages["email"]);
    else if (email_ok && email_div.children.length > 1)
        email_div.removeChild (email_div.children [1]);

    if (email_ok) {
        // Checks if email is already in the database
        fetch ("email_exists.php",
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
        event.preventDefault();
        if (div.children.length == 1)
            div.appendChild (small_error_messages["submit"]);            
    }
}