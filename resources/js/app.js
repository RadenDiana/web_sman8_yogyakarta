import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {

    const navbarToggle = document.getElementById('navbarToggle');
    const navbarMenu = document.getElementById('navbarMenu');

    if (navbarToggle && navbarMenu) {

        navbarToggle.addEventListener('click', () => {

            navbarMenu.classList.toggle('show');

            const icon = navbarToggle.querySelector('i');

            if (navbarMenu.classList.contains('show')) {

                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');

            } else {

                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Close mobile menu when clicking a link
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.nav-link').forEach(link => {

        link.addEventListener('click', () => {

            navbarMenu?.classList.remove('show');

            const icon = navbarToggle?.querySelector('i');

            if (icon) {

                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');

            }

        });

    });

});