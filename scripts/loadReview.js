document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('form[name="valutazione"]');
    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var valutaz = document.getElementById('valutaz').value;
            var commento = document.getElementById('commento').value;
            var tutor = document.querySelector('input[name="tutor"]').value;
            var studente = document.querySelector('input[name="studente"]').value;

            // Invio della richiesta al backend utilizzando Fetch API
            fetch('../backend/submit_review.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'valutaz=' + encodeURIComponent(valutaz) +
                    '&commento=' + encodeURIComponent(commento) +
                    '&tutor=' + encodeURIComponent(tutor)
                })
                .then(response => response.json())
                .then(newReview => {
                    if (newReview.error) {
                        alert(newReview.error);
                        return;
                    }
                    // Aggiornamento dinamico della pagina con la nuova recensione
                    var reviewsContainer = document.getElementById('reviews-container');

                    var newReviewElement = document.createElement('div');
                    newReviewElement.classList.add('review');

                    var parameterValutazione = document.createElement('div');
                    parameterValutazione.classList.add('parameter');
                    parameterValutazione.textContent = 'Valutazione';
                    newReviewElement.appendChild(parameterValutazione);

                    var ratingElement = document.createElement('div');
                    ratingElement.classList.add('rating');

                    // Aggiungi le stelline in base al valore della valutazione
                    for (let i = 0; i < 5; i++) {
                        var starElement = document.createElement('span');
                        starElement.classList.add('star');
                        if (i < newReview.valutaz)
                            starElement.classList.add('active');
                        starElement.textContent = '\u2605'; // Stellina piena
                        ratingElement.appendChild(starElement);
                    }

                    newReviewElement.appendChild(ratingElement);

                    var parameterCommento = document.createElement('div');
                    parameterCommento.classList.add('parameter');
                    parameterCommento.textContent = 'Commento';
                    newReviewElement.appendChild(parameterCommento);

                    var commentoElement = document.createElement('p');
                    commentoElement.textContent = newReview.commento;
                    newReviewElement.appendChild(commentoElement);

                    var parameterScrittaDa = document.createElement('div');
                    parameterScrittaDa.classList.add('parameter');
                    parameterScrittaDa.textContent = 'Scritta da';
                    newReviewElement.appendChild(parameterScrittaDa);

                    var studenteElement = document.createElement('p');
                    studenteElement.textContent = newReview.studente;
                    newReviewElement.appendChild(studenteElement);

                    reviewsContainer.appendChild(newReviewElement);

                    // Rimuovi l'intero form, l'elemento h2 dal DOM e il messaggio di nessuna recensione
                    form.remove();
                    document.querySelector('h2').remove();
                    const no_rev = document.getElementById('no-rev');
                    if (no_rev)
                        no_rev.remove();
                })
                .catch(error => {
                    console.error('Errore durante la richiesta al server:', error);
                });
        });
    }
});
