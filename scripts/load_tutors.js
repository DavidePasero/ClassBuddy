import { fill_tutor_grid } from "./utils.js";

fetch('../backend/tutor.php',
  {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'action=get_all_tutor_info'
  })
  .then(response => response.json())
  .then(tutors => {
    fill_tutor_grid(tutors);
  })
  .catch(error => {
    console.error('Error loading tutors:', error);
  });
