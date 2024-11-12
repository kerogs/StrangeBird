console.log("main -> OK");
// ! Remove all code below
// ! (remove from /public/src/ts/main.ts) only
// ! be sure to write the command below
// ! cd public; npx tsc --watch
let objectifCards = document.querySelectorAll(".objectif__card");
let objectifCardsID;
let objectifCardsClose = document.querySelectorAll(".cardObj__close");
const blurbck = document.querySelector(".blurbck");
let totalCards = objectifCards.length;
let clickedCardsCount = 0;
const clickedCard = document.getElementById("clickedCard");
const totalCard = document.getElementById("totalCard");
const statusBar = document.getElementById("statusBar");
totalCard.innerHTML = totalCards.toString();
clickedCard.innerHTML = clickedCardsCount.toString();
// Ajoute un écouteur d'événements pour chaque carte
objectifCards.forEach(card => {
    card.addEventListener("click", () => {
        objectifCardsID = card.id;
        console.log("ID : " + objectifCardsID);
        const cardClass = card;
        if (!cardClass.classList.contains("active")) {
            clickedCardsCount++;
        }
        cardClass.classList.add("active");
        let percentage = (clickedCardsCount / totalCards) * 100;
        clickedCard.innerHTML = clickedCardsCount.toString();
        statusBar.style.width = percentage + "%";
        let cardClassIcon = cardClass.querySelector(".icon");
        if (cardClassIcon) {
            cardClassIcon.innerHTML = "<i class='bx bx-check-double'></i>";
        }
        // Rechercher si un data-objectif est pareille que le ID que l'on vient de récupérer et si OUI, alors donner au data-objectif la class .active)
        const dataObjectif = document.querySelector(`[data-objectif="${objectifCardsID}"]`);
        if (dataObjectif) {
            dataObjectif.classList.add("active");
            blurbck.classList.add("active");
        }
    });
});
// fermer popup
objectifCardsClose.forEach(card => {
    card.addEventListener("click", () => {
        const dataObjectif = document.querySelector(`[data-objectif="${objectifCardsID}"]`);
        if (dataObjectif) {
            dataObjectif.classList.remove("active");
            blurbck.classList.remove("active");
        }
    });
});
// pre copy
let preCopy = document.querySelectorAll("pre");
preCopy.forEach(pre => {
    pre.addEventListener("click", () => {
        let preElement = pre;
        let textToCopy = preElement.innerText;
        navigator.clipboard.writeText(textToCopy).then(() => {
            // Afficher un message de confirmation (facultatif)
            console.log("Texte copié dans le presse-papiers : ", textToCopy);
        }).catch(err => {
            // Afficher un message d'erreur (facultatif)
            console.error("Erreur lors de la copie du texte : ", err);
        });
        preElement.classList.add("pre-active");
        setTimeout(() => {
            preElement.classList.remove("pre-active");
        }, 1500);
    });
});
