// Creo il messaggio di errore che verrà mostrato solo in caso di errore.
let error = document.createElement("p");
let textnode = document.createTextNode("L'email non è valida");
error.appendChild (textnode);
error.setAttribute ("class", "form-element small-error-message");

let email = document.getElementById ("email");

// A ogni input nella textbox della email, controllo se è valida o meno.
email.addEventListener ("input", function () {
    let email_div = document.getElementById ("email_div");
    let re = /\S+@\S+\.\S+/;
    let valid = re.test(email_div.children[0].value);
    if (!valid && email_div.children.length == 1)
        email_div.appendChild (error);
    else if (valid && email_div.children.length > 1)
        email_div.removeChild (email_div.children [1]);
});