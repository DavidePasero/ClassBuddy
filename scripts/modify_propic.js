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
    if (!imageDiv.classList.contains('dragover')) {
        imageDiv.classList.add('dragover');
    }
});

imageDiv.addEventListener('dragleave', function () {
    imageDiv.classList.remove('dragover');
});

imageDiv.addEventListener('drop', function (e) {
    e.preventDefault();
    imageDiv.classList.remove('dragover');
    console.log ("NOOOOOO");

    // Prendo il file
    const file = e.dataTransfer.files[0];
    // Aggiorno anche il fileInput
    fileInput.files = e.dataTransfer.files;

    // Se c'è un file lo mostro
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});