import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const toggle = document.getElementById('sidebarToggle');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('open');
    };

    toggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        overlay?.classList.toggle('open');
    });

    overlay?.addEventListener('click', closeSidebar);

    const checkboxes = document.querySelectorAll('.krs-checkbox');
    const sksTotal = document.getElementById('sksTotal');
    const totalSksInput = document.getElementById('totalSksInput');
    const maxSks = Number(totalSksInput?.dataset.maxSks ?? 0);

    const updateSks = (changedCheckbox = null) => {
        let total = 0;
        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                total += Number(checkbox.dataset.sks || 0);
            }
        });

        if (total > maxSks) {
            if (changedCheckbox) {
                changedCheckbox.checked = false;
            }
            total = 0;
            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    total += Number(checkbox.dataset.sks || 0);
                }
            });
            alert(`Total SKS melebihi batas maksimal ${maxSks} SKS.`);
        }

        if (sksTotal) {
            sksTotal.textContent = `${total} SKS`;
        }
        if (totalSksInput) {
            totalSksInput.value = String(total);
        }
    };

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => updateSks(checkbox));
    });

    if (checkboxes.length > 0) {
        updateSks();
    }
});
