const stars = document.querySelectorAll('.star');
const ratingContainer = document.getElementById('rating');
const valutaz = document.getElementById('valutaz');

ratingContainer.addEventListener('mouseleave', () => {
    highlightStars(0, 'hovered');
});

stars.forEach(star => {
    star.addEventListener('click', () => {
        const value = parseInt(star.getAttribute('data-value'));
        valutaz.value = value;
        highlightStars(value, 'active');
    });
    star.addEventListener('mouseover', () => {
        const value = parseInt(star.getAttribute('data-value'));
        highlightStars(value, 'hovered');
    });
});

function highlightStars(value, classname) {
    stars.forEach(star => {
        const starValue = parseInt(star.getAttribute('data-value'));
        if (starValue <= value) star.classList.add(classname);
        else star.classList.remove(classname);
    });
}