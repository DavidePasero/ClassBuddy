export function fill_tutor_grid (tutors) {
    // Error handling
    if (tutors.error) {
        showPopup(tutors.error, true);
        return;
    }

    const tutorGrid = document.getElementById('image-grid');
    tutorGrid.innerHTML = '';

    for (const [_, tutor] of Object.entries(tutors)) {
      const div = document.createElement('div');
      div.className = 'tutor';

      // Create an img element for the profile picture
      let link = document.createElement('a');
      link.href = `profile.php?email=${tutor["email"]}`;
      const img = document.createElement('img');
      img.src = tutor["propic"];
      img.alt = `${tutor["firstname"]} ${tutor["lastname"]}`;
      img.className = 'profile-image';

      // Create divs for each piece of information
      const nameDiv = createInfoDiv('name', `${tutor["firstname"]} ${tutor["lastname"]}`);
      //const emailDiv = createInfoDiv('email', tutor["email"]);
      const cityDiv = createInfoDiv('city', tutor["citta"]);
      const onlineDiv = createInfoDiv('online', tutor["online"] ? 'Disponibile online' : ' ');
      const presenceDiv = createInfoDiv('presence', tutor["presenza"] ? 'Disponibile in presenza' : ' ');

      link.appendChild(img);
      div.appendChild(link);
      div.appendChild(nameDiv);
      div.appendChild(cityDiv);

      let i = 0;
      // For each (materia, tariffa) create a div
      for (i = 0; i < tutor["materia"].length && i < 3; i++) {
        let insegnamentoDiv = createInfoDiv ("insegnamento", `${tutor["materia"][i]}: ${tutor["tariffa"][i]}€/ora`);
        div.appendChild(insegnamentoDiv);
      }
      if (i < tutor["materia"].length) {
        let insegnamentoDiv = createInfoDiv ("insegnamento", "...");
        div.appendChild(insegnamentoDiv);
      }

      div.appendChild(onlineDiv);
      div.appendChild(presenceDiv);

      tutorGrid.appendChild(div);
    };
}

// Funzione per creare un div con una classe e un testo
function createInfoDiv(className, textContent) {
    const infoDiv = document.createElement('div');
    infoDiv.className = className;
    const textNode = document.createTextNode(textContent);
    infoDiv.appendChild(textNode);
    return infoDiv;
}

// Funzione per mostrare un popup
export function showPopup(message_text, is_error) {
  document.getElementById('popup')?.remove(); // Rimuove il popup se già presente
  const popup = document.createElement('div');
  popup.id = 'popup';
  popup.className = `${is_error ? 'error' : 'success'}`;
  
  const closeBtn = document.createElement('span');
  closeBtn.className = 'close-btn';
  closeBtn.innerHTML = '&times;'; // simbolo "×" per chiudere il popup
  closeBtn.addEventListener ("click", function() {
      popup.style.opacity = '0'; // Setto l'opacità a 0 per una scomparsa graduale
            setTimeout(() => {
                document.body.removeChild(popup);
            }, 300);
  });

  const message = document.createElement('p');
  message.textContent = message_text;

  popup.appendChild(closeBtn);
  popup.appendChild(message);

  document.body.appendChild(popup);
  popup.style.display = 'block'; // Mostro il popup
  setTimeout(() => {
      popup.style.opacity = '1'; // Setto l'opacità a 1
  }, 10); // Aspetto 10ms prima di settare l'opacità a 1
}