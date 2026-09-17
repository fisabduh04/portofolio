let openSelect = null;
let globalListenersBound = false;

export function normalizeSearch(value) {
    return String(value).normalize('NFD').replace(/\p{M}/gu, '').trim().toLowerCase();
}

export function searchOptions(options, query, limit = 50) {
    const terms = normalizeSearch(query).split(/\s+/).filter(Boolean);
    const results = [];

    for (const option of options) {
        if (option.hidden || !terms.every((term) => option.searchText.includes(term))) {
            continue;
        }
        if (results.length === limit) {
            return { results, hasMore: true };
        }
        results.push(option);
    }
    return { results, hasMore: false };
}

export function nextEnabledIndex(options, currentIndex, direction) {
    let index = currentIndex + direction;
    while (index >= 0 && index < options.length) {
        if (!options[index].disabled) {
            return index;
        }
        index += direction;
    }
    return currentIndex >= options.length ? -1 : currentIndex;
}

export class SearchableSelect {
    constructor(root) {
        this.root = root;
        this.select = root.querySelector('[data-select-native]');
        this.trigger = root.querySelector('[data-select-trigger]');
        this.valueLabel = root.querySelector('[data-select-value]');
        this.search = root.querySelector('[data-select-search]');
        this.popup = root.querySelector('[data-select-popup]');
        this.portal = root.hasAttribute('data-select-portal');
        this.list = root.querySelector('[data-select-list]');
        this.status = root.querySelector('[data-select-status]');
        this.label = root.querySelector('label');
        this.limit = Math.max(1, Math.min(200, Number(root.dataset.maxResults) || 50));
        this.events = new AbortController();
        this.originalTabindex = this.select.getAttribute('tabindex');
        this.originalAriaHidden = this.select.getAttribute('aria-hidden');
        this.activeIndex = -1;
        this.isOpen = false;
        this.wasInvalid = false;
        this.select.searchableSelect = this;

        const listen = (target, event, handler) => {
            target?.addEventListener(event, handler, { signal: this.events.signal });
        };
        listen(this.trigger, 'click', () => (this.isOpen ? this.close() : this.open()));
        listen(this.trigger, 'keydown', (event) => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                this.open(event.key === 'ArrowUp');
            }
        });
        listen(this.search, 'input', () => this.render());
        listen(this.search, 'keydown', (event) => this.onKeydown(event));
        listen(this.list, 'pointerdown', (event) => {
            if (event.target.closest('[role=option]')) {
                event.preventDefault();
            }
        });
        listen(this.list, 'click', (event) => {
            const option = event.target.closest('[role=option]');
            if (option) {
                this.choose(Number(option.dataset.index));
            }
        });
        listen(this.select, 'change', () => this.refresh());
        listen(this.select, 'invalid', (event) => {
            event.preventDefault();
            this.wasInvalid = true;
            this.sync();
            if (!openSelect || openSelect.select.validity.valid) {
                this.open();
            }
            this.status.textContent = this.select.validationMessage;
        });
        listen(this.select.form, 'reset', (event) => {
            requestAnimationFrame(() => {
                if (!event.defaultPrevented && !this.events.signal.aborted) {
                    this.close();
                    this.wasInvalid = false;
                    this.refresh();
                }
            });
        });
        this.observer = new MutationObserver(() => this.refresh());
        this.observer.observe(this.select, {
            childList: true,
            subtree: true,
            characterData: true,
            attributes: true,
            attributeFilter: ['disabled', 'required', 'selected', 'label', 'value', 'hidden'],
        });
        this.refresh();
        this.select.classList.add('sr-only');
        this.select.setAttribute('tabindex', '-1');
        this.select.setAttribute('aria-hidden', 'true');
        this.trigger.hidden = false;
        if (this.label) {
            this.label.htmlFor = this.trigger.id;
        }
    }

    refresh() {
        this.options = Array.from(this.select.options, (option) => ({
            value: option.value,
            label: option.label,
            searchText: normalizeSearch(option.label),
            disabled: option.disabled || (option.parentElement.tagName === 'OPTGROUP' && option.parentElement.disabled),
            hidden: option.hidden || (this.select.required && option.value === ''),
        }));
        this.sync();
        if (this.isOpen) {
            this.render();
        }
    }

    sync() {
        this.valueLabel.textContent = this.select.selectedOptions[0]?.label ?? this.options[0]?.label ?? '';
        this.trigger.disabled = this.select.disabled;
        this.search.disabled = this.select.disabled;
        const invalid =
            this.select.getAttribute('aria-invalid') === 'true' || (this.wasInvalid && !this.select.validity.valid);
        for (const control of [this.trigger, this.search]) {
            control.setAttribute('aria-invalid', String(invalid));
            control.setAttribute('aria-describedby', this.select.getAttribute('aria-describedby') ?? '');
        }
        this.search.setAttribute('aria-required', String(this.select.required));
        if (this.select.hasAttribute('aria-label')) {
            this.trigger.setAttribute(
                'aria-label',
                this.select.getAttribute('aria-label') + ': ' + this.valueLabel.textContent,
            );
        }
        if (this.select.disabled) {
            this.close();
        }
    }

    open(fromEnd = false) {
        if (this.select.matches(':disabled')) {
            return;
        }
        if (openSelect && openSelect !== this) {
            openSelect.close();
        }
        openSelect = this;
        this.isOpen = true;
        this.search.value = '';
        if (this.portal) {
            // Popup keluar dari area gulir tabel agar tidak terpotong.
            document.body.append(this.popup);
        }
        this.popup.hidden = false;
        this.trigger.setAttribute('aria-expanded', 'true');
        this.search.setAttribute('aria-expanded', 'true');
        this.sync();
        this.render();
        if (fromEnd) {
            this.activeIndex = this.results.length;
            this.move(-1);
        }
        this.position();
        this.search.focus({ preventScroll: true });
    }

    close(restoreFocus = false) {
        this.isOpen = false;
        this.popup.hidden = true;
        if (this.portal && this.popup.parentElement !== this.root) {
            this.root.append(this.popup);
        }
        this.trigger.setAttribute('aria-expanded', 'false');
        this.search.setAttribute('aria-expanded', 'false');
        this.search.removeAttribute('aria-activedescendant');
        if (openSelect === this) {
            openSelect = null;
        }
        if (restoreFocus) {
            this.trigger.focus({ preventScroll: true });
        }
    }

    position() {
        const rect = this.trigger.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom - 12;
        const spaceAbove = rect.top - 12;
        const above = spaceBelow < 260 && spaceAbove > spaceBelow;
        if (this.portal) {
            const width = Math.min(rect.width, window.innerWidth - 16);
            this.popup.style.position = 'fixed';
            this.popup.style.width = width + 'px';
            this.popup.style.left = Math.max(8, Math.min(rect.left, window.innerWidth - width - 8)) + 'px';
            this.popup.style.right = 'auto';
            this.popup.style.top = above ? 'auto' : rect.bottom + 4 + 'px';
            this.popup.style.bottom = above ? window.innerHeight - rect.top + 4 + 'px' : 'auto';
        } else {
            this.popup.style.top = above ? 'auto' : 'calc(100% + 4px)';
            this.popup.style.bottom = above ? this.root.offsetHeight - this.trigger.offsetTop + 4 + 'px' : 'auto';
        }
        this.list.style.maxHeight = Math.max(48, Math.min(240, (above ? spaceAbove : spaceBelow) - 100)) + 'px';
    }

    render() {
        const { results, hasMore } = searchOptions(this.options, this.search.value, this.limit);
        this.results = results;
        const fragment = document.createDocumentFragment();
        results.forEach((option, index) => {
            const item = document.createElement('div');
            item.id = this.list.id + '-' + index;
            item.dataset.index = String(index);
            item.className = 'searchable-select-option';
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', String(option.value === this.select.value));
            item.setAttribute('aria-disabled', String(option.disabled));
            item.textContent = option.label;
            fragment.append(item);
        });
        this.list.replaceChildren(fragment);
        this.list.scrollTop = 0;
        this.status.textContent = hasMore
            ? this.limit + ' hasil ditampilkan. Ketik lebih spesifik untuk opsi lainnya.'
            : results.length
              ? results.length + ' opsi tersedia.'
              : 'Tidak ada hasil.';
        this.activeIndex = results.findIndex((option) => !option.disabled && option.value === this.select.value);
        if (this.activeIndex < 0) {
            this.activeIndex = results.findIndex((option) => !option.disabled);
        }
        this.highlight();
    }

    highlight() {
        Array.from(this.list.children).forEach((item, index) => {
            item.dataset.active = String(index === this.activeIndex);
        });
        const active = this.list.children[this.activeIndex];
        if (active) {
            this.search.setAttribute('aria-activedescendant', active.id);
            const top =
                active.getBoundingClientRect().top - this.list.getBoundingClientRect().top + this.list.scrollTop;
            if (top < this.list.scrollTop) {
                this.list.scrollTop = top;
            } else if (top + active.offsetHeight > this.list.scrollTop + this.list.clientHeight) {
                this.list.scrollTop = top + active.offsetHeight - this.list.clientHeight;
            }
        } else {
            this.search.removeAttribute('aria-activedescendant');
        }
    }

    move(direction) {
        this.activeIndex = nextEnabledIndex(this.results, this.activeIndex, direction);
        this.highlight();
    }

    onKeydown(event) {
        if (event.isComposing) {
            return;
        }
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            this.move(event.key === 'ArrowDown' ? 1 : -1);
        } else if (event.key === 'Enter') {
            event.preventDefault();
            this.choose(this.activeIndex);
        } else if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            this.close(true);
        } else if (event.key === 'Tab') {
            requestAnimationFrame(() => this.close());
        }
    }

    choose(index) {
        const option = this.results[index];
        if (!option || option.disabled || this.select.matches(':disabled')) {
            return;
        }
        this.close(true);
        this.setValue(option.value);
    }

    setValue(value) {
        const option = this.options.find((option) => option.value === String(value) && !option.disabled);
        if (!option) {
            return false;
        }
        const changed = this.select.value !== option.value;
        this.select.value = option.value;
        this.sync();
        if (changed) {
            this.select.dispatchEvent(new Event('input', { bubbles: true }));
            this.select.dispatchEvent(new Event('change', { bubbles: true }));
        }
        return true;
    }

    destroy() {
        this.close();
        this.events.abort();
        this.observer.disconnect();
        this.select.classList.remove('sr-only');
        for (const [attribute, value] of [
            ['tabindex', this.originalTabindex],
            ['aria-hidden', this.originalAriaHidden],
        ]) {
            if (value === null) {
                this.select.removeAttribute(attribute);
            } else {
                this.select.setAttribute(attribute, value);
            }
        }
        this.trigger.hidden = true;
        if (this.label) {
            this.label.htmlFor = this.select.id;
        }
        delete this.select.searchableSelect;
    }
}

export function initializeSearchableSelects(root = document) {
    if (!globalListenersBound) {
        const closeOutside = (event) => {
            if (openSelect && !openSelect.root.contains(event.target) && !openSelect.popup.contains(event.target)) {
                openSelect.close();
            }
        };
        document.addEventListener('pointerdown', closeOutside);
        document.addEventListener('focusin', closeOutside);
        document.addEventListener(
            'scroll',
            (event) => {
                if (openSelect?.portal && !openSelect.popup.contains(event.target)) {
                    openSelect.close();
                }
            },
            true,
        );
        window.addEventListener('resize', () => openSelect?.position());
        globalListenersBound = true;
    }
    const roots = [...root.querySelectorAll('[data-searchable-select]')];
    if (root.matches?.('[data-searchable-select]')) {
        roots.unshift(root);
    }
    roots.forEach((element) => {
        const select = element.querySelector('[data-select-native]');
        if (select && !select.multiple && !select.searchableSelect) {
            new SearchableSelect(element);
        }
    });
}
