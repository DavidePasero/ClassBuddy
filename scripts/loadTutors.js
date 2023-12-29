fetch('../backend/tutor.php')
  .then(response => response.json())
  .then(tutors => {
    console.log('Tutors data:', tutors); // Log the data to the console for inspection
    
    const tutorGrid = document.querySelector('.image-grid');
    tutorGrid.innerHTML = '';

    tutors.forEach(tutor => {
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
      const emailDiv = createInfoDiv('email', tutor["email"]);
      const cityDiv = createInfoDiv('city', tutor["citta"]);
      const onlineDiv = createInfoDiv('online', tutor["online"] ? 'Disponibile online' : ' ');
      const presenceDiv = createInfoDiv('presence', tutor["presenza"] ? 'Disponibile in presenza' : ' ');

      // Access the array of insegnamento directly
      const subjectDiv = createInfoDiv('subject', tutor["insegnamento"] ? tutor["insegnamento"].map(item => item["materia"]).join(', ') : ' ');
      const rateDiv = createInfoDiv('rate', tutor["insegnamento"] ? tutor["insegnamento"].map(item => item["tariffa"]).join(', ') : ' ');

      link.appendChild(img);
      div.appendChild(link);
      div.appendChild(nameDiv);
      div.appendChild(cityDiv);
      div.appendChild(subjectDiv);
      div.appendChild(rateDiv);
      div.appendChild(onlineDiv);
      div.appendChild(presenceDiv);

      tutorGrid.appendChild(div);
    });
  })
  .catch(error => {
    console.error('Error loading tutors:', error);
  });

// Helper function to create an info div
function createInfoDiv(className, textContent) {
    const infoDiv = document.createElement('div');
    infoDiv.className = className;
  
    // Check if the className is 'rate' and the textContent is not empty
    if (className === 'rate' && textContent.trim() !== '') {
      textContent += '€/ora';
    }
  
    const textNode = document.createTextNode(textContent);
    infoDiv.appendChild(textNode);
    return infoDiv;
  }
