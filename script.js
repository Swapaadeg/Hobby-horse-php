document.querySelectorAll('.result-cell').forEach(cell => {
    cell.addEventListener('mouseover', () => {
        const joueur1 = cell.dataset.joueur1;
        const joueur2 = cell.dataset.joueur2;

        document.querySelectorAll(`.player-row[data-joueur-id="${joueur1}"]`).forEach(el => el.classList.add('hovered'));
        document.querySelectorAll(`.player-col[data-joueur-id="${joueur2}"]`).forEach(el => el.classList.add('hovered'));
    });

    cell.addEventListener('mouseout', () => {
        document.querySelectorAll('.hovered').forEach(el => el.classList.remove('hovered'));
    });
});