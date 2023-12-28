fetch('../backend/tutor.php')
.then(response => {
    return response.json();
}).then(tutors => {
    const tutorGrid = document.querySelector('.image-grid');
    tutorGrid.innerHTML = '';

    tutors.forEach(tutor => {
        const div = document.createElement('div');
        div.className = 'tutor';

        // Create an img element for the profile picture
        let link = document.createElement('a');
        link.href = `profile.php?email=${tutor["email"]}`;
        const img = document.createElement('img');
        img.src = tutor["propic"]; // tutor.propic should be the data URI or image URL
        img.alt = `${tutor["firstname"]} ${tutor["lastname"]}`;
        img.className = 'profile-image';

        // Create divs for each piece of information
        const nameDiv = createInfoDiv('name', `${tutor["firstname"]} ${tutor["lastname"]}`);
        const emailDiv = createInfoDiv('email', tutor["email"]);
        const cityDiv = createInfoDiv('city', tutor["citta"]);
        const onlineDiv = createInfoDiv('online', tutor["online"] ? 'Disponibile online' : ' ');
        const presenceDiv = createInfoDiv('presence', tutor["presenza"] ? 'Disponibile in presenza' : ' ');

        // Append elements to the main div
        link.appendChild(img);
        div.appendChild(link);
        div.appendChild(nameDiv);
        div.appendChild(cityDiv);
        div.appendChild(onlineDiv);
        div.appendChild(presenceDiv);

        tutorGrid.appendChild(div);
    });
})
.catch ((error) => {
    console.error('Error loading tutors:', error);
});

// Helper function to create an info div
function createInfoDiv(className, textContent) {
    const infoDiv = document.createElement('div');
    infoDiv.className = className;
    const textNode = document.createTextNode(textContent);
    infoDiv.appendChild(textNode);
    return infoDiv;
}