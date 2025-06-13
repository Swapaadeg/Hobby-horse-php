document.addEventListener('DOMContentLoaded', () => {
    //Hover en croisé prenom sur resultat
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

    // Menu burger
    const burger = document.querySelector('.burger');
    const navbar = document.querySelector('.navbar');

    if (burger && navbar) {
        burger.addEventListener('click', () => {
            navbar.classList.toggle('active');
            burger.classList.toggle('active');
        });
    } else {
        console.error('Burger or Navbar element not found');
    }

    // Dropdown admin
    document.querySelectorAll('.admin-dropdown .dropdown > a').forEach(dropdown => {
        dropdown.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const parent = dropdown.parentElement;
                parent.classList.toggle('active');
            }
        });
    });
});
