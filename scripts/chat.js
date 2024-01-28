import { showPopup } from "./utils.js";

let chat = document.getElementById("chat-container");
let chatMessages = document.getElementById("chat-messages");

let msg = document.getElementById("message");
let submit = document.getElementById("send-button");

submit.addEventListener("click", send_msg);

var interval_fetch_messages = 0;

function send_msg (event) {
    event.preventDefault();
    var testo_msg = msg.value;
    let recipient = document.getElementById("recipient").value;
    if (recipient === "") {
        showPopup("Seleziona un destinatario", true);
        return;
    }
    if (testo_msg === "") {
        showPopup("Il messaggio non può essere vuoto", true);
        return;
    }
    
    msg.value = "";
    // Inserisco immediatamente il messaggio inviato dall'utente nella chat per una risposta più veloce
    var new_msg = document.createElement("div");
    new_msg.classList.add("message");
    new_msg.classList.add("sent");
    var txt = document.createTextNode(testo_msg);
    new_msg.appendChild(txt);
    chatMessages.appendChild(new_msg);
    chat.scrollTop = chat.scrollHeight;

    // Invio il messaggio al server che lo inserisce nel database in modo asincrono
    fetch("../backend/chat_backend.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=send_msg&message=" + testo_msg + "&recipient=" + recipient
    }).then (response => response.json ())
    .then (response_json => {
        if (response_json.error) {
            showPopup (response_json.error, true);
            new_msg.remove (); // In caso di errore, rimuovo il messaggio dalla chat
            return;
        }
        // Aggiorno l'ultimo messaggio nella sidebar
        update_message_preview (testo_msg, document.getElementById("recipient").value);
    });
}

// Setto l'interval do fetchNewMessages solo se c'è un destinatario selezionato
if (document.getElementById("recipient").value !== "") {
    interval_fetch_messages = setInterval(fetchNewMessages, 5000);
    var latestTimestamp = getFormattedTimestamp();
}

// Funzione che recupera i nuovi messaggi dal server della conversazione aperta
function fetchNewMessages() {

    fetch("../backend/chat_backend.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=new_msgs&timestamp=" + latestTimestamp + "&recipient=" + document.getElementById("recipient").value
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showPopup(data.error, true);
            return;
        }

        addMessagesToChat (data);

        // Aggiorno l'ultimo timestamp
        if (data.length > 0) {
            update_message_preview (data[data.length - 1]["testo"], document.getElementById("recipient").value);
            latestTimestamp = data[data.length - 1]["timestamp"];
            chat.scrollTop = chat.scrollHeight;
        }
    })
    .catch(error => console.error("Error fetching new messages:", error));
}

// Search box
const searchButton = document.getElementById('search-button');
const searchBoxContainer = document.getElementById('search-box-container');

searchButton.addEventListener('click', function () {
    searchBoxContainer.style.display = searchBoxContainer.style.display === "block" ? "none" : "block";
});

let sendSearch = document.getElementById("send-search");

sendSearch.addEventListener("click", function () {
    let search = document.getElementById("search-box").value;
    fetch("../backend/chat_backend.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=search_msgs&ricerca=" + search + "&recipient=" + document.getElementById("recipient").value
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showPopup(data.error, true);
            return;
        }

        // Rimuovo tutti i messaggi dalla chat
        while (chatMessages.firstChild) {
            chatMessages.removeChild(chatMessages.firstChild);
        }
        // Inserisco i messaggi che rispondono alla query dell'utente nella chat
        addMessagesToChat (data);
    })
    .catch(error => console.error("Error fetching new messages:", error));
});

// SIDEBAR
function loadChat(recipient, item) {
    // Recupero tutti i messaggi della conversazione dal server
    fetch("../backend/chat_backend.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=fetch_chat&recipient=" + recipient
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showPopup(data.error, true);
            return;
        }
        // Rimuovo tutti i messaggi dalla chat
        chatMessages.innerHTML = "";
        addMessagesToChat (data);
        // Scrollo automaticamente la chat fino all'ultimo messaggio
        chat.scrollTop = chat.scrollHeight;
        clearInterval(interval_fetch_messages);
        interval_fetch_messages = setInterval(fetchNewMessages, 5000);

        if (data.length > 0)
            latestTimestamp = data[data.length - 1]["timestamp"];
    })
    .catch(error => console.error("Error fetching chat:", error));

    document.getElementById("recipient").value = recipient;
    document.getElementById("no-recipient-selected").style.display = "none";
    document.getElementById("chat").removeAttribute("hidden");
    // Aggiorno la foto profilo e il nome del destinatario, se è stato passato come GET a chat.php il caricamento
    // di queste informazioni avvengono nel php
    if (item) {
        let propic = item.querySelectorAll(".profile-pic")[0].getAttribute("src");
        let name = item.querySelectorAll(".user-name")[0].textContent;
        document.getElementById("chat-propic").setAttribute("src", propic);
        document.getElementById("profile-link").href = "profile.php?email=" + recipient;
        document.getElementById("chat-username").textContent = name;
    }
}

if (document.getElementById("recipient").value !== "")
    loadChat(document.getElementById("recipient").value);

let last_messages_timestamp = [];
const sidebar = document.getElementById("sidebar");
// Aggiornamento dinamico delle conversazioni
function get_convos () {
    fetch ("../backend/chat_backend.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=get_convos"
    }).then (response => response.json ())
    .then (response_json => {
        if (response_json.error) {
            showPopup (response_json.error, true);
            return;
        }
        
        // Aggiungo le nuove conversazioni
        response_json.forEach (convo => {
            let convo_item = sidebar.querySelector (`.user-item[data-recipient='${convo.recipient}']`);
            // Controllo se la conversazione è già presente nella sidebar e se sono arrivati nuovi messaggi
            if (convo_item) {
                if (convo.timestamp > last_messages_timestamp[convo.recipient]){ 
                    // Se c'è un nuovo messaggio, aggiorno l'ultimo messaggio e metto in cima la conversazione
                    update_message_preview (convo.testo, convo.recipient);
                    last_messages_timestamp[convo.recipient] = convo.timestamp;
                    sidebar.insertBefore (convo_item, sidebar.firstChild);
                }
                return; // Skip alla prossima iterazione
            }

            let new_convo = document.createElement ("div");
            new_convo.classList.add ("user-item");
            new_convo.setAttribute ("data-recipient", convo.recipient);

            // Propic dell'utente
            let propic_div = document.createElement ("div");
            let propic = document.createElement ("img");
            propic.classList.add ("profile-pic");
            propic.setAttribute ("src", convo.propic);
            propic.setAttribute ("alt", convo.firstname + " " + convo.lastname);
            propic_div.appendChild (propic);

            // User info: nome e ultimo messaggio della conversazione
            let user_info = document.createElement ("div");
            user_info.classList.add ("user-info");
            let user_name = document.createElement ("div");
            user_name.classList.add ("user-name");
            user_name.textContent = convo.firstname + " " + convo.lastname;
            let last_message = document.createElement ("div");
            last_message.classList.add ("last-message");

            user_info.appendChild (user_name);
            user_info.appendChild (last_message);

            new_convo.appendChild (propic_div);
            new_convo.appendChild (user_info);

            new_convo.addEventListener ("click", () => {
                document.querySelectorAll(".selected").forEach((item) => {item.classList.remove("selected")});
                new_convo.classList.add("selected");
                loadChat(convo.recipient, new_convo);
            });
            document.getElementById ("sidebar").appendChild (new_convo);

            update_message_preview (convo.testo, convo.recipient);
            // Aggiorno l'ultimo timestamp della conversazione
            last_messages_timestamp[convo.recipient] = convo.timestamp;
        });
    });
}

get_convos ();
// Aggiorno le conversazioni ogni 10 secondi
setInterval (get_convos, 10000);

// Funzione che restituisce la data e l'ora attuale nel formato "yyyy-mm-dd hh:mm:ss"
function getFormattedTimestamp() {
    const currentDate = new Date();

    const year = currentDate.getFullYear();
    const month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
    const day = currentDate.getDate().toString().padStart(2, '0');
    const hours = currentDate.getHours().toString().padStart(2, '0');
    const minutes = currentDate.getMinutes().toString().padStart(2, '0');
    const seconds = currentDate.getSeconds().toString().padStart(2, '0');

    const formattedTimestamp = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    return formattedTimestamp;
}

// Per ogni nuovo (possibile) messaggio, creo gli elementi del dom che li costituiscono e li aggiungo alla chat
function addMessagesToChat(messages) {
    messages.forEach(message => {
        const newMessage = document.createElement("div");
        newMessage.classList.add("message");
        newMessage.classList.add(message.mittente === document.getElementById("recipient").value ? "received" : "sent");
        newMessage.textContent = message.testo;
        chatMessages.appendChild(newMessage);
    });
}

// Funzione che aggiorna l'ultimo messaggio nella sidebar
function update_message_preview (message, recipient) {
    let preview = document.querySelector(`#sidebar .user-item[data-recipient='${recipient}'] .last-message`);
    preview.textContent = message;
}