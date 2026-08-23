{{--
    Reusable custom dropdown for the Cheque module.
    Progressive enhancement: any <select class="js-cs"> is upgraded into a styled
    dropdown while the native <select> stays in the DOM (hidden) as the source of
    truth — so form submission and existing `change` handlers keep working.
    Add `data-cs-inline` to a select for the compact (pagination) variant.
    Call window.cqmEnhanceSelects(rootEl) after injecting selects via AJAX.
--}}
<style>
    .cqm-cs { position: relative; display: block; }
    .cqm-cs > select.js-cs { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
    .cqm-cs-trigger { display: flex; align-items: center; justify-content: space-between; gap: 10px; width: 100%; height: 44px; padding: 0 13px; border: 1px solid #d0d8e4; border-radius: 8px; background: #fff; font-family: 'Manrope', sans-serif; font-size: 14px; color: #1f2733; cursor: pointer; transition: border-color .15s ease, box-shadow .15s ease; }
    .cqm-cs-trigger:hover { border-color: var(--primary, #427AB5); }
    .cqm-cs.open .cqm-cs-trigger { border-color: var(--primary, #427AB5); box-shadow: 0 0 0 3px rgba(66,122,181,.14); }
    .cqm-cs-value { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
    .cqm-cs-value.is-placeholder { color: #98a2b3; font-weight: 400; }
    .cqm-cs-chev { width: 15px; height: 15px; flex-shrink: 0; color: #8893a3; transition: transform .18s ease; }
    .cqm-cs.open .cqm-cs-chev { transform: rotate(180deg); }
    .cqm-cs-menu { position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 50; display: none; flex-direction: column; gap: 2px; min-width: 100%; max-height: 260px; overflow-y: auto; padding: 6px; background: #fff; border: 1px solid #e1e7ef; border-radius: 10px; box-shadow: 0 12px 30px rgba(20,30,50,.16); }
    .cqm-cs.open .cqm-cs-menu { display: flex; }
    .cqm-cs-opt { display: flex; align-items: center; gap: 8px; width: 100%; border: none; background: none; padding: 9px 11px; border-radius: 7px; text-align: left; font-family: 'Manrope', sans-serif; font-size: 14px; color: #1f2733; cursor: pointer; white-space: nowrap; }
    .cqm-cs-opt:hover { background: rgba(66,122,181,.08); }
    .cqm-cs-opt.sel { background: rgba(66,122,181,.12); font-weight: 600; }
    .cqm-cs-tick { width: 14px; height: 14px; flex-shrink: 0; color: var(--primary, #427AB5); opacity: 0; }
    .cqm-cs-opt.sel .cqm-cs-tick { opacity: 1; }
    /* Compact variant for the pagination "rows per page" select */
    .cqm-cs--inline { display: inline-block; vertical-align: middle; }
    .cqm-cs--inline .cqm-cs-trigger { width: auto; min-width: 74px; height: 36px; font-size: 13px; }
    .cqm-cs--inline .cqm-cs-menu { right: auto; min-width: 100%; }
</style>
<script>
    (function () {
        if (window.cqmEnhanceSelects) return; // define once
        const CHEV = '<svg class="cqm-cs-chev" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 7.5 10 12.5 15 7.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        const TICK = '<svg class="cqm-cs-tick" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 10.5 8.5 15 16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        function closeAll(except) {
            document.querySelectorAll('.cqm-cs.open').forEach(function (w) {
                if (w !== except) { w.classList.remove('open'); w.querySelector('.cqm-cs-trigger')?.setAttribute('aria-expanded', 'false'); }
            });
        }

        function enhance(select) {
            if (select.dataset.csReady) return;
            select.dataset.csReady = '1';

            const wrap = document.createElement('div');
            wrap.className = 'cqm-cs' + (select.hasAttribute('data-cs-inline') ? ' cqm-cs--inline' : '');
            select.parentNode.insertBefore(wrap, select);
            wrap.appendChild(select);

            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'cqm-cs-trigger';
            trigger.setAttribute('aria-haspopup', 'listbox');
            trigger.setAttribute('aria-expanded', 'false');
            const value = document.createElement('span');
            value.className = 'cqm-cs-value';
            trigger.appendChild(value);
            trigger.insertAdjacentHTML('beforeend', CHEV);

            const menu = document.createElement('div');
            menu.className = 'cqm-cs-menu';
            menu.setAttribute('role', 'listbox');

            wrap.appendChild(trigger);
            wrap.appendChild(menu);

            function syncLabel() {
                const opt = select.options[select.selectedIndex];
                value.textContent = opt ? opt.textContent.trim() : '';
                value.classList.toggle('is-placeholder', !!(opt && opt.disabled));
            }

            function renderMenu() {
                menu.innerHTML = '';
                Array.from(select.options).forEach(function (o) {
                    if (o.disabled) return;
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'cqm-cs-opt' + (o.selected ? ' sel' : '');
                    b.innerHTML = TICK + '<span></span>';
                    b.querySelector('span').textContent = o.textContent.trim();
                    b.addEventListener('click', function () {
                        if (select.value !== o.value) {
                            select.value = o.value;
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                        wrap.classList.remove('open');
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                    menu.appendChild(b);
                });
            }

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                const willOpen = !wrap.classList.contains('open');
                closeAll(wrap);
                wrap.classList.toggle('open', willOpen);
                trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                if (willOpen) menu.querySelector('.cqm-cs-opt.sel')?.scrollIntoView({ block: 'nearest' });
            });

            // Keep the custom UI in sync when the value changes (click, or programmatic).
            select.addEventListener('change', function () { renderMenu(); syncLabel(); });
            // Re-render when options are added/removed (e.g. a new bank account is appended).
            new MutationObserver(function () { renderMenu(); syncLabel(); }).observe(select, { childList: true });

            renderMenu();
            syncLabel();
        }

        window.cqmEnhanceSelects = function (root) {
            (root || document).querySelectorAll('select.js-cs').forEach(enhance);
        };

        document.addEventListener('click', function () { closeAll(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(); });

        window.cqmEnhanceSelects(document);
    })();
</script>
