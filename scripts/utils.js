export function fill_tutor_grid (tutors) {
    // Error handling
    if (tutors.error) {
        alert(tutors.error);
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

// Helper function to create an info div
function createInfoDiv(className, textContent) {
    const infoDiv = document.createElement('div');
    infoDiv.className = className;
    const textNode = document.createTextNode(textContent);
    infoDiv.appendChild(textNode);
    return infoDiv;
}