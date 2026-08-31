/**
 * SMS 2 – Global Search
 * Searches modules and pages from window.SMS2_SEARCH_INDEX
 */
(function () {
    'use strict';

    /* ── wait for DOM ─────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', init);

    function init() {
        var index    = window.SMS2_SEARCH_INDEX || [];
        var input    = document.getElementById('globalSearch');
        var dropdown = document.getElementById('searchDropdown');
        var list     = document.getElementById('searchResultsList');
        var empty    = document.getElementById('searchEmpty');
        var clearBtn = document.getElementById('globalSearchClear');

        if (!input || !dropdown || !list) return;

        var activeIndex = -1;   // keyboard navigation cursor
        var lastQuery   = '';

        /* ── helpers ───────────────────────────────────────────── */
        function openDropdown() {
            dropdown.classList.add('open');
            input.setAttribute('aria-expanded', 'true');
        }

        function closeDropdown() {
            dropdown.classList.remove('open');
            input.setAttribute('aria-expanded', 'false');
            activeIndex = -1;
        }

        function showEmpty(show) {
            empty.classList.toggle('visible', show);
            list.style.display = show ? 'none' : '';
        }

        /* Escape HTML to prevent XSS in injected content */
        function esc(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        /* Wrap matched portion in <mark class="search-highlight"> */
        function highlight(text, query) {
            if (!query) return esc(text);
            var escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var re = new RegExp('(' + escaped + ')', 'gi');
            return esc(text).replace(re, '<mark class="search-highlight">$1</mark>');
        }

        /* ── search logic ──────────────────────────────────────── */
        function search(query) {
            query = query.trim().toLowerCase();
            if (query === lastQuery) return;
            lastQuery = query;

            list.innerHTML = '';
            activeIndex = -1;

            if (query.length < 1) {
                closeDropdown();
                return;
            }

            /* Score each item */
            var tokens = query.split(/\s+/).filter(Boolean);
            var scored = [];

            index.forEach(function (item) {
                var kw    = item.keywords;
                var label = item.label.toLowerCase();
                var score = 0;

                tokens.forEach(function (t) {
                    if (label === t)            score += 100;
                    else if (label.startsWith(t)) score += 60;
                    else if (label.includes(t))  score += 30;
                    else if (kw.includes(t))     score += 15;
                });

                if (score > 0) scored.push({ item: item, score: score });
            });

            /* Sort by score desc, modules before pages at same score */
            scored.sort(function (a, b) {
                if (b.score !== a.score) return b.score - a.score;
                if (a.item.type === 'module' && b.item.type !== 'module') return -1;
                if (b.item.type === 'module' && a.item.type !== 'module') return  1;
                return 0;
            });

            if (scored.length === 0) {
                showEmpty(true);
                openDropdown();
                return;
            }

            showEmpty(false);

            /* Separate modules & pages */
            var modules = scored.filter(function (s) { return s.item.type === 'module'; });
            var pages   = scored.filter(function (s) { return s.item.type === 'page'; });

            /* Limit total results */
            var MAX_MODULES = 3;
            var MAX_PAGES   = 8;
            modules = modules.slice(0, MAX_MODULES);
            pages   = pages.slice(0, MAX_PAGES);

            var fragment = document.createDocumentFragment();

            function makeGroupLabel(text) {
                var li = document.createElement('li');
                li.className = 'search-group-label';
                li.textContent = text;
                return li;
            }

            function makeResultItem(scored) {
                var s    = scored.item;
                var li   = document.createElement('li');
                li.className = 'search-result-item';
                li.setAttribute('role', 'option');

                var a = document.createElement('a');
                a.className = 'search-result-link';
                a.href = s.url;

                var iconDiv = document.createElement('div');
                iconDiv.className = 'search-result-icon';
                iconDiv.innerHTML = '<i class="fas ' + esc(s.icon) + '"></i>';

                var textDiv = document.createElement('div');
                textDiv.className = 'search-result-text';

                var titleSpan = document.createElement('span');
                titleSpan.className = 'search-result-title';
                titleSpan.innerHTML = highlight(s.label, query);

                var metaSpan = document.createElement('span');
                metaSpan.className = 'search-result-meta';
                metaSpan.textContent = s.type === 'page' ? s.parent : 'Module';

                textDiv.appendChild(titleSpan);
                textDiv.appendChild(metaSpan);

                var badge = document.createElement('span');
                badge.className = 'search-result-badge ' + s.type;
                badge.textContent = s.type === 'module' ? 'Module' : 'Page';

                a.appendChild(iconDiv);
                a.appendChild(textDiv);
                a.appendChild(badge);
                li.appendChild(a);
                return li;
            }

            if (modules.length > 0) {
                fragment.appendChild(makeGroupLabel('Modules'));
                modules.forEach(function (s) {
                    fragment.appendChild(makeResultItem(s));
                });
            }

            if (pages.length > 0) {
                fragment.appendChild(makeGroupLabel('Pages'));
                pages.forEach(function (s) {
                    fragment.appendChild(makeResultItem(s));
                });
            }

            list.appendChild(fragment);
            openDropdown();
        }

        /* ── keyboard navigation ───────────────────────────────── */
        function getResultLinks() {
            return Array.from(list.querySelectorAll('.search-result-link'));
        }

        function setActive(idx) {
            var links = getResultLinks();
            links.forEach(function (el, i) {
                el.classList.toggle('keyboard-active', i === idx);
            });
            if (links[idx]) {
                links[idx].scrollIntoView({ block: 'nearest' });
            }
        }

        input.addEventListener('keydown', function (e) {
            var links = getResultLinks();
            var total = links.length;

            if (!dropdown.classList.contains('open')) return;

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    activeIndex = (activeIndex + 1) % total;
                    setActive(activeIndex);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    activeIndex = (activeIndex - 1 + total) % total;
                    setActive(activeIndex);
                    break;
                case 'Enter':
                    if (activeIndex >= 0 && links[activeIndex]) {
                        e.preventDefault();
                        window.location.href = links[activeIndex].href;
                    }
                    break;
                case 'Escape':
                    closeDropdown();
                    input.blur();
                    break;
            }
        });

        /* ── input events ──────────────────────────────────────── */
        input.addEventListener('input', function () {
            var val = input.value;
            clearBtn.classList.toggle('d-none', val.length === 0);
            search(val);
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length > 0) {
                openDropdown();
            }
        });

        /* ── clear button ──────────────────────────────────────── */
        clearBtn.addEventListener('click', function () {
            input.value = '';
            lastQuery = '';
            clearBtn.classList.add('d-none');
            closeDropdown();
            input.focus();
        });

        /* ── close on outside click ────────────────────────────── */
        document.addEventListener('click', function (e) {
            var wrapper = document.querySelector('.navbar-search-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                closeDropdown();
            }
        });

        /* ── Ctrl+K / Cmd+K shortcut ───────────────────────────── */
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                input.focus();
                input.select();
            }
        });
    }

})();
