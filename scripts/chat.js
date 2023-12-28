let chat = document.getElementById("chat-container");
// Scrollo automaticamente la chat fino all'ultimo messaggio
chat.scrollTop = chat.scrollHeight;

let msg = document.getElementById("message");
let submit = document.getElementById("submit");

submit.addEventListener("click", send_msg);

function send_msg (event) {
    event.preventDefault();
    var testo_msg = msg.value;
    msg.value = "";
    // Inserisco immediatamente il messaggio inviato dall'utente nella chat
    var new_msg = document.createElement("div");
    new_msg.classList.add("message");
    new_msg.classList.add("sent");
    var txt = document.createTextNode(testo_msg);
    new_msg.appendChild(txt);
    chat.appendChild(new_msg);
    chat.scrollTop = chat.scrollHeight;

    // Invio il messaggio al server che lo inserisce nel database in modo asincrono
    fetch("../backend/send_message.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + testo_msg + "&recipient=" + document.getElementById("recipient").value
    });
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
            chat.appendChild(newMessage);
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