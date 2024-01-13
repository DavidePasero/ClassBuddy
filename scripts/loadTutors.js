fetch('../backend/tutor.php')
  .then(response => {
    if (!response.ok) {
      throw new Error(`HTTP error: status ${response.status}`);
    }
    return response.json();
  })
  .then(tutors => {
    const tutorGrid = document.querySelector('.image-grid');
    tutorGrid.innerHTML = '';

    // Object.values(tutors) restituisce un array contenente un oggetto con le informazioni di ogni tutor in "tutors"
    for (const tutor of Object.values(tutors)) { 
      const div = document.createElement('div');
      div.className = 'tutor';

      // Crea un elemento img per la foto profilo e un link alla pagina del tutor
      let link = document.createElement('a');
      link.href = `profile.php?email=${tutor["email"]}`;
      const img = document.createElement('img');
      img.src = tutor["propic"];
      img.alt = `${tutor["firstname"]} ${tutor["lastname"]}`;
      img.className = 'profile-image';

      // Crea un div per ogni informazione del tutor
      const nameDiv = createInfoDiv('name', `${tutor["firstname"]} ${tutor["lastname"]}`);
      const cityDiv = createInfoDiv('city', tutor["citta"]);
      const onlineDiv = createInfoDiv('online', tutor["online"] ? 'Disponibile online' : ' ');
      const presenceDiv = createInfoDiv('presence', tutor["presenza"] ? 'Disponibile in presenza' : ' ');

      link.appendChild(img);
      div.appendChild(link);
      div.appendChild(nameDiv);
      div.appendChild(cityDiv);

      // Crea un div per ogni materia e relativa tariffa del tutor
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
  })
  .catch(error => {
    console.error('Errore nel caricamento dei tutor:', error);
  });

function createInfoDiv(className, textContent) {
    const infoDiv = document.createElement('div');
    infoDiv.className = className;
    const textNode = document.createTextNode(textContent);
    infoDiv.appendChild(textNode);
    return infoDiv;
  }
