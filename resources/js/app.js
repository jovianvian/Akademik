import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    const openModal = (modal) => {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('open');
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('open');
    };

    const assignFormValues = (form, values) => {
        Object.entries(values).forEach(([name, rawValue]) => {
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return;

            if (el.type === 'checkbox') {
                el.checked = Boolean(rawValue) && String(rawValue) !== '0';
                return;
            }

            el.value = rawValue ?? '';
        });
    };

    const parseDatasetJson = (raw) => {
        if (!raw) return null;
        const normalized = String(raw)
            .replaceAll('&quot;', '"')
            .replaceAll('&#34;', '"')
            .replaceAll('&#039;', "'")
            .replaceAll('&apos;', "'")
            .replaceAll('&amp;', '&');

        try {
            return JSON.parse(normalized);
        } catch (error) {
            return null;
        }
    };

    document.querySelectorAll('[data-modal-open]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const modalId = btn.getAttribute('data-modal-open');
            const modal = modalId ? document.getElementById(modalId) : null;
            if (!modal) return;

            const form = modal.querySelector('[data-modal-form]');
            const title = modal.querySelector('[data-modal-title]');
            const submitLabel = modal.querySelector('[data-modal-submit-label]');
            const methodInput = form?.querySelector('[data-modal-method]');

            if (form) {
                form.reset();
                if (form.dataset.defaultAction) {
                    form.action = form.dataset.defaultAction;
                }
                if (title && form.dataset.defaultTitle) {
                    title.textContent = form.dataset.defaultTitle;
                }
                if (submitLabel && form.dataset.defaultSubmit) {
                    submitLabel.textContent = form.dataset.defaultSubmit;
                }
                if (methodInput && form.dataset.defaultMethod) {
                    methodInput.value = form.dataset.defaultMethod;
                }
            }

            if (form && btn.dataset.formAction) {
                form.action = btn.dataset.formAction;
            }

            if (title && btn.dataset.formTitle) {
                title.textContent = btn.dataset.formTitle;
            }

            if (submitLabel && btn.dataset.formSubmit) {
                submitLabel.textContent = btn.dataset.formSubmit;
            }

            if (methodInput && btn.dataset.formMethod) {
                methodInput.value = btn.dataset.formMethod.toUpperCase();
            }

            if (form && btn.dataset.formValues) {
                const values = parseDatasetJson(btn.dataset.formValues);
                if (values && typeof values === 'object') {
                    assignFormValues(form, values);
                }
            }

            openModal(modal);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('[data-modal]');
            closeModal(modal);
        });
    });

    document.querySelectorAll('[data-modal]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.querySelectorAll('[data-modal-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitBtn = form.querySelector('[data-modal-submit]');
            const label = form.querySelector('[data-modal-submit-label]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70');
            }
            if (label) {
                label.textContent = 'Menyimpan...';
            }
        });
    });

    const toastStack = document.querySelector('.toast-stack');
    const showDeleteConfirmToast = (form) => {
        if (!toastStack) return;

        const active = toastStack.querySelector('[data-toast-confirm]');
        if (active) {
            active.remove();
        }

        const label = form.dataset.deleteLabel || 'data ini';
        const toast = document.createElement('div');
        toast.className = 'toast-item toast-error';
        toast.setAttribute('data-toast-confirm', '1');

        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-triangle-exclamation';

        const text = document.createElement('span');
        text.textContent = `Yakin mau hapus ${label}?`;

        const actions = document.createElement('div');
        actions.className = 'toast-actions';

        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'toast-btn toast-btn-cancel';
        cancelBtn.textContent = 'Batal';

        const confirmBtn = document.createElement('button');
        confirmBtn.type = 'button';
        confirmBtn.className = 'toast-btn toast-btn-confirm';
        confirmBtn.textContent = 'Hapus';

        cancelBtn.addEventListener('click', () => toast.remove());
        confirmBtn.addEventListener('click', () => {
            form.dataset.deleteConfirmed = '1';
            toast.remove();
            form.submit();
        });

        actions.appendChild(cancelBtn);
        actions.appendChild(confirmBtn);
        toast.appendChild(icon);
        toast.appendChild(text);
        toast.appendChild(actions);
        toastStack.prepend(toast);

        window.setTimeout(() => {
            if (toast.isConnected) {
                toast.remove();
            }
        }, 9000);
    };

    document.querySelectorAll('form[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.deleteConfirmed === '1') {
                return;
            }

            event.preventDefault();
            showDeleteConfirmToast(form);
        });
    });

    document.querySelectorAll('[data-toast]').forEach((toast) => {
        window.setTimeout(() => {
            toast.classList.add('opacity-0');
            toast.classList.add('transition-opacity');
            toast.classList.add('duration-300');
            window.setTimeout(() => toast.remove(), 320);
        }, 4200);
    });

    window.setTimeout(() => {
        document.querySelectorAll('[data-table-skeleton]').forEach((el) => el.classList.add('hidden'));
    }, 280);

    const applyTableRowNumbers = () => {
        document.querySelectorAll('table.table-base').forEach((table) => {
            if (table.dataset.numbered === '1') return;

            const headRow = table.querySelector('thead tr');
            const bodyRows = Array.from(table.querySelectorAll('tbody tr'));
            if (!headRow || bodyRows.length === 0) return;

            const firstHead = headRow.querySelector('th');
            const firstHeadText = firstHead?.textContent?.trim().toLowerCase();
            if (firstHeadText === 'no') {
                table.dataset.numbered = '1';
                return;
            }

            const noTh = document.createElement('th');
            noTh.className = 'px-4 py-3 table-no-col';
            noTh.style.width = '72px';
            noTh.textContent = 'No';
            headRow.prepend(noTh);

            let no = 1;
            bodyRows.forEach((row) => {
                const cells = row.querySelectorAll('td,th');
                if (cells.length === 0) return;

                if (cells.length === 1) {
                    const onlyCell = cells[0];
                    const currentColspan = Number(onlyCell.getAttribute('colspan') || 1);
                    onlyCell.setAttribute('colspan', String(currentColspan + 1));
                    return;
                }

                const noTd = document.createElement('td');
                noTd.className = 'px-4 py-3 font-semibold text-gray-500 table-no-col';
                noTd.textContent = String(no++);
                row.prepend(noTd);
            });

            table.dataset.numbered = '1';
        });
    };

    applyTableRowNumbers();

    const root = document.documentElement;
    const themeButtons = document.querySelectorAll('[data-theme-toggle]');

    const getCookieTheme = () => {
        const match = document.cookie.match(/(?:^|;\s*)theme_mode=(dark|light)(?:;|$)/);
        return match ? match[1] : null;
    };

    const getStoredTheme = () => {
        try {
            const mode = window.localStorage.getItem('theme_mode');
            if (mode === 'dark' || mode === 'light') {
                return mode;
            }
        } catch (error) {
            // Fallback ke cookie.
        }
        return getCookieTheme();
    };

    const setStoredTheme = (mode) => {
        try {
            window.localStorage.setItem('theme_mode', mode);
        } catch (error) {
            // Abaikan, cookie tetap jadi fallback.
        }
        document.cookie = `theme_mode=${mode}; path=/; max-age=31536000; SameSite=Lax`;
    };

    const syncThemeFromStorage = () => {
        const mode = getStoredTheme();
        if (mode === 'dark') {
            root.classList.add('dark');
            return;
        }
        if (mode === 'light') {
            root.classList.remove('dark');
        }
    };
    const applyThemeLabel = () => {
        const isDark = root.classList.contains('dark');
        themeButtons.forEach((btn) => {
            const label = btn.querySelector('[data-theme-label]');
            const sun = btn.querySelector('[data-theme-icon-sun]');
            const moon = btn.querySelector('[data-theme-icon-moon]');
            const modeLabel = isDark ? 'Mode Terang' : 'Mode Gelap';

            if (label) {
                label.textContent = modeLabel;
            } else if (!sun && !moon) {
                btn.textContent = modeLabel;
            }

            if (sun && moon) {
                sun.classList.toggle('hidden', !isDark);
                moon.classList.toggle('hidden', isDark);
            }

            btn.setAttribute('aria-label', modeLabel);
        });
    };

    syncThemeFromStorage();
    applyThemeLabel();
    themeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            root.classList.toggle('dark');
            const mode = root.classList.contains('dark') ? 'dark' : 'light';
            setStoredTheme(mode);
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
