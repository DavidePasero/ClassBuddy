let chat = document.getElementById("chat-container");
let chatMessages = document.getElementById("chat-messages");
// Scrollo automaticamente la chat fino all'ultimo messaggio
chat.scrollTop = chat.scrollHeight;

let msg = document.getElementById("message");
let submit = document.getElementById("send-button");

submit.addEventListener("click", send_msg);

function send_msg (event) {
    event.preventDefault();
    var testo_msg = msg.value;
    let recipient = document.getElementById("recipient").value;
    if (testo_msg !== "" && recipient !== "") {
        msg.value = "";
        // Inserisco immediatamente il messaggio inviato dall'utente nella chat
        var new_msg = document.createElement("div");
        new_msg.classList.add("message");
        new_msg.classList.add("sent");
        var txt = document.createTextNode(testo_msg);
        new_msg.appendChild(txt);
        chat.scrollTop = chat.scrollHeight;
        chatMessages.appendChild(new_msg);

        // Invio il messaggio al server che lo inserisce nel database in modo asincrono
        fetch("../backend/send_message.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "message=" + testo_msg + "&recipient=" + recipient
        }).then (response => response.text ())
        .then (response_txt => {
            if (response_txt !== "OK")
                alert (response_txt);
        });
    } else {
        alert ("Inserisci un messaggio e seleziona un destinatario!");
    }
}

var latestTimestamp = getFormattedTimestamp();

// Function to fetch new messages from the server
function fetchNewMessages() {

    // Fetch new messages from the server
    fetch("../backend/fetch_messages.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "timestamp=" + latestTimestamp + "&recipient=" + document.getElementById("recipient").value
    })
    .then(response => response.json())
    .then(data => {
        // data = JSON.parse(data);
        // Process the new messages and update the chat UI
        // For simplicity, this example appends the new messages to the chat container
        data.forEach(message => {
            var newMessage = document.createElement("div");
            newMessage.classList.add("message");
            newMessage.classList.add("received");
            var textNode = document.createTextNode(message["testo"]);
            newMessage.appendChild(textNode);
            chatMessages.appendChild(newMessage);
        });

        // Update the latest timestamp for the next fetch
        if (data.length > 0) {
            latestTimestamp = data[data.length - 1]["timestamp"];
            chat.scrollTop = chat.scrollHeight;
        }
    })
    .catch(error => console.error("Error fetching new messages:", error));
}

// Set up an interval to fetch new messages every 5 seconds
setInterval(fetchNewMessages, 5000);


function getFormattedTimestamp() {
    const currentDate = new Date();

    // Extract components of the date
    const year = currentDate.getFullYear();
    const month = (currentDate.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
    const day = currentDate.getDate().toString().padStart(2, '0');
    const hours = currentDate.getHours().toString().padStart(2, '0');
    const minutes = currentDate.getMinutes().toString().padStart(2, '0');
    const seconds = currentDate.getSeconds().toString().padStart(2, '0');

    // Create the formatted timestamp
    const formattedTimestamp = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    return formattedTimestamp;
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
    fetch("../backend/search_messages.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "ricerca=" + search + "&recipient=" + document.getElementById("recipient").value
    })
    .then(response => response.json())
    .then(data => {
        // Rimuovo tutti i messaggi dalla chat
        while (chatMessages.firstChild) {
            chatMessages.removeChild(chatMessages.firstChild);
        }
        // Inserisco i messaggi che rispondono alla query dell'utente nella chat
        data.forEach(message => {
            var newMessage = document.createElement("div");
            newMessage.classList.add("message");
            if (message["mittente"] == document.getElementById("recipient").value)
                newMessage.classList.add("received");
            else
                newMessage.classList.add("sent");
            var textNode = document.createTextNode(message["testo"]);
            newMessage.appendChild(textNode);
            chatMessages.appendChild(newMessage);
        });
    })
    .catch(error => console.error("Error fetching new messages:", error));
});

// SIDEBAR
const userItems = document.querySelectorAll(".user-item");

userItems.forEach((item) => {
    item.addEventListener("click", () => {
        document.querySelectorAll(".selected").forEach((item) => {item.classList.remove("selected")});
        item.classList.add("selected");
        const recipient = item.getAttribute("data-recipient");
        loadChat(recipient, item);
    });
});

function loadChat(recipient, item) {
    // Fetch chat messages based on the selected recipient
    fetch("../backend/fetch_chat.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "timestamp=0&recipient=" + recipient
    })
    .then(response => response.json())
    .then(data => {
        // Clear existing chat messages
        chatMessages.innerHTML = "";
        // Display the fetched chat messages
        data.forEach(message => {
            const newMessage = document.createElement("div");
            newMessage.classList.add("message");
            newMessage.classList.add(message.mittente === recipient ? "received" : "sent");
            newMessage.textContent = message.testo;
            chatMessages.appendChild(newMessage);
        });
    })
    .catch(error => console.error("Error fetching chat:", error));

    document.getElementById("recipient").value = recipient;
    document.getElementById("no-recipient-selected").style.display = "none";
    document.getElementById("chat").removeAttribute("hidden");
    let propic = item.querySelectorAll(".profile-pic")[0].getAttribute("src");
    let name = item.querySelectorAll(".user-name")[0].textContent;
    document.getElementById("chat-propic").setAttribute("src", propic);
    document.getElementById("chat-username").textContent = name;
}

if (document.getElementById("recipient").value != "")
    loadChat(document.getElementById("recipient").value);