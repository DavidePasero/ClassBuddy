import { fill_tutor_grid, showPopup } from "./utils.js";

let i = 0;

function load () {
  i++;
  fetch('../backend/tutor.php',
    {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'action=get_all_tutor_info&i=' + i
    })
    .then(response => response.json())
    .then(tutors => {
      if (tutors.error) {
        showPopup(tutors.error, true);
        return;
      }
      fill_tutor_grid(tutors, true);
    })
    .catch(error => {
      console.error('Error loading tutors:', error);
  });
}

load();

document.getElementById('altri_tutor').addEventListener('click', load);