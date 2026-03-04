import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const themeButtons = document.querySelectorAll('[data-theme-toggle]');
    const applyThemeLabel = () => {
        const isDark = root.classList.contains('dark');
        themeButtons.forEach((btn) => {
            const label = btn.querySelector('[data-theme-label]');
            const sun = btn.querySelector('[data-theme-icon-sun]');
            const moon = btn.querySelector('[data-theme-icon-moon]');

            if (label) {
                label.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            } else {
                btn.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            }

            if (sun && moon) {
                sun.classList.toggle('hidden', !isDark);
                moon.classList.toggle('hidden', isDark);
            }
        });
    };

    applyThemeLabel();
    themeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            root.classList.toggle('dark');
            const mode = root.classList.contains('dark') ? 'dark' : 'light';
            window.localStorage.setItem('theme_mode', mode);
            applyThemeLabel();
        });
    });

    const userDropdown = document.getElementById('userDropdown');
    document.addEventListener('click', (event) => {
        if (!userDropdown || !userDropdown.open) return;
        if (!userDropdown.contains(event.target)) {
            userDropdown.open = false;
        }
    });

    document.querySelectorAll('[data-live-search-target]').forEach((input) => {
        const targetSelector = input.getAttribute('data-live-search-target');
        const table = targetSelector ? document.querySelector(targetSelector) : null;
        if (!table) return;

        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const onSearch = () => {
            const query = String(input.value || '').toLowerCase().trim();
            rows.forEach((row) => {
                const rowText = row.textContent?.toLowerCase() ?? '';
                row.style.display = query === '' || rowText.includes(query) ? '' : 'none';
            });
        };

        input.addEventListener('input', onSearch);
    });

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const toggle = document.getElementById('sidebarToggle');
    const mainWrap = document.getElementById('mainWrap');
    const desktopMedia = window.matchMedia('(min-width: 1280px)');
    const desktopSidebarKey = 'sidebar_desktop_collapsed_v1';

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('open');
    };

    const applyDesktopState = () => {
        if (!sidebar || !mainWrap) return;
        const isDesktop = desktopMedia.matches;
        const collapsed = window.localStorage.getItem(desktopSidebarKey) === '1';
        sidebar.classList.toggle('desktop-collapsed', isDesktop && collapsed);
        mainWrap.classList.toggle('desktop-expanded', isDesktop && collapsed);
    };

    applyDesktopState();
    desktopMedia.addEventListener('change', applyDesktopState);

    toggle?.addEventListener('click', () => {
        if (desktopMedia.matches) {
            const collapsed = sidebar?.classList.contains('desktop-collapsed');
            window.localStorage.setItem(desktopSidebarKey, collapsed ? '0' : '1');
            applyDesktopState();
            return;
        }

        sidebar?.classList.toggle('open');
        overlay?.classList.toggle('open');
    });

    overlay?.addEventListener('click', closeSidebar);

    const menuGroups = Array.from(document.querySelectorAll('#sidebar details[data-menu-group]'));
    if (menuGroups.length > 0) {
        const storageKey = 'sidebar_open_groups_v1';
        let savedGroups = [];
        try {
            const raw = window.localStorage.getItem(storageKey);
            savedGroups = raw ? JSON.parse(raw) : [];
        } catch (error) {
            savedGroups = [];
        }

        const savedSet = new Set(Array.isArray(savedGroups) ? savedGroups : []);

        menuGroups.forEach((group) => {
            const key = group.dataset.menuGroup;
            const defaultOpen = group.dataset.defaultOpen === '1';
            if (savedSet.size > 0) {
                group.open = savedSet.has(key) || defaultOpen;
            } else {
                group.open = group.open || defaultOpen;
            }

            group.addEventListener('toggle', () => {
                const openedKeys = menuGroups
                    .filter((item) => item.open)
                    .map((item) => item.dataset.menuGroup)
                    .filter(Boolean);
                window.localStorage.setItem(storageKey, JSON.stringify(openedKeys));
            });
        });
    }

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
