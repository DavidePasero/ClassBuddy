let editButton = document.getElementById('edit-button');
let fileInput = document.getElementById('propic');

editButton.addEventListener('click', openFileInput);
fileInput.addEventListener('change', previewImage);

function openFileInput() {
    fileInput.click();
}

// Preview image
function previewImage(event) {
    const fileInput = event.target;
    const file = fileInput.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Enable drag and drop
const imageDiv = document.getElementById('image_div');
imageDiv.addEventListener('dragover', function (e) {
    e.preventDefault();
    imageDiv.classList.add('dragover');
});

imageDiv.addEventListener('dragleave', function () {
    imageDiv.classList.remove('dragover');
});

imageDiv.addEventListener('drop', function (e) {
    e.preventDefault();
    imageDiv.classList.remove('dragover');

    const file = e.dataTransfer.files[0];

    if (file) {
            const reader = new FileReader();
            reader.onload = function (ev) {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.src = ev.target.result;
            // Aggiorno anche il fileInput
            fileInput.value = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});

let submitButton = document.getElementById('submit');
submitButton.addEventListener('click', function (event) {
    event.preventDefault();
    console.log (fileInput.value);
});