import 'bootstrap';

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

import 'bootstrap';

// ===== Date and Time ===== //
function updateDateTime() {
    const now = new Date();

    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    const dayName = days[now.getDay()];
    const monthName = months[now.getMonth()];
    const day = String(now.getDate()).padStart(2, '0');
    const year = now.getFullYear();

    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    hours = String(hours).padStart(2, '0');

    document.getElementById('live-date').textContent = `${dayName}, ${monthName} ${day}, ${year}`;
    document.getElementById('live-time').textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
}

updateDateTime();
setInterval(updateDateTime, 1000);

// ===== Navigation Scroll Buttons ===== //
const nav = document.getElementById('navigationBar');
const scrollLeft = document.getElementById('scrollLeft');
const scrollRight = document.getElementById('scrollRight');

function updateButtons() {
    scrollLeft.style.display = nav.scrollLeft > 0 ? 'block' : 'none';
    scrollRight.style.display = nav.scrollLeft + nav.clientWidth < nav.scrollWidth ? 'block' : 'none';
}

scrollLeft.addEventListener('click', () => {
    nav.scrollBy({ left: -200, behavior: 'smooth' });
});

scrollRight.addEventListener('click', () => {
    nav.scrollBy({ left: 200, behavior: 'smooth' });
});

nav.addEventListener('scroll', updateButtons);
window.addEventListener('resize', updateButtons);

updateButtons();

// ===== Password Visibility Toggle ===== //
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-toggle-password]');
    if (!btn) return;
    const input = document.getElementById(btn.getAttribute('data-toggle-password'));
    if (!input) return;
    const reveal = input.type === 'password';
    input.type = reveal ? 'text' : 'password';
    btn.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
    btn.classList.toggle('is-on', reveal);
});