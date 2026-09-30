/**
 * Custom Navigation – Bedienung der Untermenüs im barrierefreien Navigationsmenü von
 * Contao (Template mod_navigation_accessible) in der Desktop-Ansicht.
 *
 * Contao klappt ein Untermenü auf, sobald die Maus seinen Menüpunkt erreicht (mouseenter),
 * und sofort wieder zu, sobald sie ihn verlässt (mouseleave). Dieses Skript fängt beide
 * Ereignisse am Wrapper des Moduls ab, bevor sie die Menüpunkte erreichen (Capture-Phase),
 * und hält sie zurück, solange
 *
 *  – die Maus schräg auf ein geöffnetes Untermenü zufährt (Sicherheitsdreieck) oder
 *  – die Maus das Menü nur kurz verlassen hat (Wiedereinblenden).
 *
 * Ist die Wartezeit vorbei, holt es die zurückgehaltenen Ereignisse nach, sodass Contao
 * seinen Zustand selbst weiterführt. Der Code von Contao bleibt unverändert; Tastatur,
 * Fokus und die mobile Ansicht behandelt Contao wie gewohnt.
 *
 * Einstellungen am Wrapper (aus dem Navigationsmodul):
 *   data-nav-dreieck               Sicherheitsdreieck
 *   data-nav-wiedereinblenden="ms" Wiedereinblenden nach kurzem Verlassen
 *   data-nav-richtung="auto|rechts" Seitliche Untermenüs bei Platzmangel links bzw. immer rechts
 *   data-nav-animation="ms"        Untermenüs ein- und ausblenden (Überblendung im Stylesheet)
 */

/** Klasse, solange ein Untermenü für die heranfahrende Maus offen gehalten wird. */
const HELD = 'nav-gehalten';

/** Klasse, solange ein Untermenü ausgeblendet, für Contao aber noch geöffnet ist. */
const RESTING = 'nav-ruht';

/** So lange darf die Maus im Kegel zum Untermenü innehalten, bevor es freigegeben wird. */
const AIM_TIMEOUT = 300;

/** So lange muss die Maus beim Zurückkehren über einem anderen Menüpunkt verweilen. */
const DWELL_TIMEOUT = 250;

/** Die Spitze des Kegels liegt so viele Mausbewegungen zurück (gleicht Zittern aus). */
const HISTORY_LENGTH = 3;

const instances = [];
const history = [];
let pointerType = 'mouse';

/**
 * Die Liste des Untermenüs eines Menüpunkts – direkt im li oder in einer Hülle wie
 * <div class="ul-wrapper">.
 */
function getSubmenu(li) {
    for (const ul of li.querySelectorAll('ul')) {
        if (ul.parentElement.closest('li') === li) {
            return ul;
        }
    }

    return null;
}

function isSubmenuItem(element) {
    return element instanceof HTMLLIElement && element.classList.contains('submenu');
}

function isDesktop() {
    return document.body.classList.contains('nav-is-desktop');
}

function contains(rect, point) {
    return point.x >= rect.left && point.x <= rect.right && point.y >= rect.top && point.y <= rect.bottom;
}

/** Winkel auf den Bereich −π … π bringen, damit es beim Übergang über ±π keinen Sprung gibt. */
function normalize(angle) {
    return Math.atan2(Math.sin(angle), Math.cos(angle));
}

/**
 * Liegt die Bewegung von „from“ nach „to“ im Kegel, den „from“ mit dem Rechteck des
 * Untermenüs aufspannt? Die Richtung ist beliebig: Untermenüs können unter, rechts oder
 * links neben dem Menüpunkt liegen.
 */
function isAimingAt(from, to, rect) {
    if (!from || (0 === rect.width && 0 === rect.height)) {
        return false;
    }

    if (contains(rect, to)) {
        return true;
    }

    // Kommt die Maus aus dem Untermenü heraus, entfernt sie sich davon.
    if (contains(rect, from)) {
        return false;
    }

    const center = Math.atan2(rect.top + rect.height / 2 - from.y, rect.left + rect.width / 2 - from.x);
    let min = Infinity;
    let max = -Infinity;

    for (const [x, y] of [[rect.left, rect.top], [rect.right, rect.top], [rect.left, rect.bottom], [rect.right, rect.bottom]]) {
        const angle = normalize(Math.atan2(y - from.y, x - from.x) - center);
        min = Math.min(min, angle);
        max = Math.max(max, angle);
    }

    const direction = normalize(Math.atan2(to.y - from.y, to.x - from.x) - center);

    return direction >= min && direction <= max;
}

class CustomNavigation {
    #wrapper;
    #navigation;
    #triangle;
    #returnTime;
    #direction;
    #fadeTime;
    #frozen = new Map();
    #pending = null;
    #point = null;
    #bypass = false;
    #open = new WeakSet();

    constructor(wrapper, navigation) {
        this.#wrapper = wrapper;
        this.#navigation = navigation;
        this.#triangle = wrapper.hasAttribute('data-nav-dreieck');
        this.#returnTime = parseInt(wrapper.dataset.navWiedereinblenden, 10) || 0;
        this.#direction = wrapper.dataset.navRichtung || '';
        this.#fadeTime = parseInt(wrapper.dataset.navAnimation, 10) || 0;

        // mouseenter und mouseleave steigen nicht auf, durchlaufen aber die Capture-Phase:
        // Hier kommen sie vor den Listenern von Contao an den Menüpunkten an.
        wrapper.addEventListener('mouseleave', (event) => this.#onLeave(event), true);
        wrapper.addEventListener('mouseenter', (event) => this.#onEnter(event), true);

        new MutationObserver((records) => this.#onMutation(records)).observe(navigation, {
            subtree: true,
            attributes: true,
            attributeFilter: ['class'],
            attributeOldValue: true,
        });
    }

    /**
     * Mausbewegung, bevor ihre Position in die Historie eingeht: Die Historie enthält
     * damit nur frühere Positionen.
     */
    handleMove(event) {
        const pending = this.#pending;

        if (!pending) {
            return;
        }

        if (!this.#isActive()) {
            this.#cancel();

            return;
        }

        const point = { x: event.clientX, y: event.clientY };

        if (this.#point && point.x === this.#point.x && point.y === this.#point.y) {
            return;
        }

        this.#point = point;

        if (pending.resting) {
            this.#whileResting(point, event.target);
        } else {
            this.#whileAiming(point, event.target);
        }
    }

    #isActive() {
        return 'mouse' === pointerType && isDesktop();
    }

    #onLeave(event) {
        const li = event.target;

        if (this.#bypass || !isSubmenuItem(li) || !this.#navigation.contains(li)) {
            return;
        }

        if (!this.#isActive()) {
            this.#cancel();

            return;
        }

        const pending = this.#pending;

        if (pending) {
            // Verlässt die Maus zugleich einen übergeordneten Menüpunkt, wird auch er
            // gehalten. Alle anderen Menüpunkte (die unterwegs gestreiften) dürfen Contao
            // nicht erreichen: Ihr mouseleave würde das wartende Untermenü schließen.
            if (li.contains(pending.items[0]) && li.classList.contains('nav-expanded') && !pending.items.includes(li)) {
                pending.items.push(li);
                li.classList.add(pending.resting ? RESTING : HELD);
            }

            event.stopPropagation();

            return;
        }

        if (!li.classList.contains('nav-expanded')) {
            return;
        }

        const point = { x: event.clientX, y: event.clientY };
        const submenu = getSubmenu(li);

        if (this.#triangle && submenu && isAimingAt(history[0], point, submenu.getBoundingClientRect())) {
            event.stopPropagation();
            this.#point = point;
            this.#startAiming(li);

            return;
        }

        if (this.#returnTime && this.#isOutside(event.relatedTarget) && !li.querySelector(':focus-visible')) {
            event.stopPropagation();
            this.#point = point;
            this.#pending = this.#createPending([li]);
            this.#startResting();
        }
    }

    #onEnter(event) {
        const li = event.target;

        if (this.#bypass || !this.#pending || !isSubmenuItem(li) || !this.#navigation.contains(li)) {
            return;
        }

        // Die Maus erreicht den wartenden Menüpunkt oder sein Untermenü: Contao darf es
        // mitbekommen. Jeder andere Menüpunkt wird erst nach dem Warten geöffnet.
        if (this.#pending.items[0].contains(li)) {
            return;
        }

        event.stopPropagation();
    }

    #createPending(items) {
        return { items, resting: false, aimTimer: 0, returnTimer: 0, dwellTimer: 0, dwellItem: null };
    }

    #startAiming(li) {
        this.#pending = this.#createPending([li]);
        li.classList.add(HELD);
        this.#restartAimTimer();
    }

    #restartAimTimer() {
        clearTimeout(this.#pending.aimTimer);

        this.#pending.aimTimer = setTimeout(() => {
            if (this.#pending && !this.#pending.resting) {
                this.#leaveCone(document.elementFromPoint(this.#point.x, this.#point.y));
            }
        }, AIM_TIMEOUT);
    }

    #whileAiming(point, target) {
        const inner = this.#pending.items[0];
        const submenu = getSubmenu(inner);
        const rect = submenu?.getBoundingClientRect();

        // Angekommen: Contao hält das Untermenü von hier an selbst offen.
        if (inner.contains(target) || (rect && contains(rect, point))) {
            this.#clearPending();

            return;
        }

        if (rect && isAimingAt(history[0], point, rect)) {
            this.#restartAimTimer();

            return;
        }

        this.#leaveCone(target);
    }

    #leaveCone(target) {
        if (this.#returnTime && this.#isOutside(target) && !this.#hasFocus()) {
            this.#startResting();
        } else {
            this.#release();
        }
    }

    #startResting() {
        const pending = this.#pending;

        clearTimeout(pending.aimTimer);
        pending.resting = true;

        for (const li of pending.items) {
            li.classList.remove(HELD);
            li.classList.add(RESTING);
        }

        pending.returnTimer = setTimeout(() => this.#release(), this.#returnTime);
    }

    #whileResting(point, target) {
        const pending = this.#pending;

        if (pending.items.some((li) => this.#isOver(li, point, target))) {
            this.#comeBack(point, target);

            return;
        }

        // Über einem anderen Menüpunkt erst nach kurzem Verweilen umschalten: Auf dem Weg
        // zurück wird oft ein Nachbar gestreift.
        const item = this.#getItem(target);

        if (item !== pending.dwellItem) {
            clearTimeout(pending.dwellTimer);
            pending.dwellItem = item;
            pending.dwellTimer = item ? setTimeout(() => this.#release(), DWELL_TIMEOUT) : 0;
        }
    }

    /**
     * Die Maus ist zurück: Das Untermenü erscheint wieder. Menüpunkte, über denen sie
     * nicht mehr steht, werden geschlossen – von innen nach außen, wie es Contao selbst
     * tun würde.
     */
    #comeBack(point, target) {
        const { items } = this.#pending;
        let keep = false;

        this.#clearPending();

        for (const li of items) {
            keep = keep || this.#isOver(li, point, target);

            if (!keep) {
                this.#dispatch(li, 'mouseleave');
            }
        }
    }

    /**
     * Wartezeit vorbei: zurückgehaltene Ereignisse nachholen – erst mouseleave von innen
     * nach außen, dann mouseenter für die Menüpunkte unter der Maus.
     */
    #release() {
        const pending = this.#pending;

        if (!pending) {
            return;
        }

        this.#clearPending();

        if (!isDesktop()) {
            return;
        }

        for (const li of pending.items) {
            this.#dispatch(li, 'mouseleave');
        }

        const element = this.#point && document.elementFromPoint(this.#point.x, this.#point.y);

        if (!element || !this.#navigation.contains(element)) {
            return;
        }

        const chain = [];

        for (let li = element.closest('li'); li && this.#navigation.contains(li); li = li.parentElement.closest('li')) {
            chain.unshift(li);
        }

        for (const li of chain) {
            if (isSubmenuItem(li) && !li.classList.contains('nav-expanded')) {
                this.#dispatch(li, 'mouseenter');
            }
        }
    }

    /** Beendet das Warten ohne nachzuholen. */
    #cancel() {
        this.#clearPending();
    }

    #clearPending() {
        const pending = this.#pending;

        if (!pending) {
            return;
        }

        clearTimeout(pending.aimTimer);
        clearTimeout(pending.returnTimer);
        clearTimeout(pending.dwellTimer);

        for (const li of pending.items) {
            li.classList.remove(HELD, RESTING);
        }

        this.#pending = null;
    }

    #dispatch(li, type) {
        if ('mouseleave' === type && !li.classList.contains('nav-expanded')) {
            return;
        }

        this.#bypass = true;

        try {
            li.dispatchEvent(new MouseEvent(type, {
                view: window,
                clientX: this.#point?.x ?? 0,
                clientY: this.#point?.y ?? 0,
            }));
        } finally {
            this.#bypass = false;
        }
    }

    #isOutside(element) {
        return !element || !this.#navigation.contains(element);
    }

    /**
     * Liegt die Maus über dem Menüpunkt oder seinem Untermenü? Das Untermenü ist beim
     * Wiedereinblenden unsichtbar und nimmt keine Mausereignisse an – verlässlich ist
     * dann nur sein Rechteck.
     */
    #isOver(li, point, target) {
        if (li.contains(target) || contains(li.getBoundingClientRect(), point)) {
            return true;
        }

        const submenu = getSubmenu(li);

        return !!submenu && contains(submenu.getBoundingClientRect(), point);
    }

    #getItem(element) {
        const li = element?.closest?.('li');

        return li && this.#navigation.contains(li) ? li : null;
    }

    /** Tastaturfokus im Untermenü: nicht ausblenden, was gerade per Tastatur bedient wird. */
    #hasFocus() {
        return this.#pending.items.some((li) => li.querySelector(':focus-visible'));
    }

    #onMutation(records) {
        for (const { target, oldValue } of records) {
            if (isSubmenuItem(target)) {
                this.#onSubmenuChange(target);
            } else if (target instanceof HTMLUListElement) {
                if ('rechts' === this.#direction) {
                    this.#keepRight(target);
                } else if (this.#fadeTime) {
                    this.#keepPosition(target, oldValue);
                }
            }
        }
    }

    #onSubmenuChange(li) {
        const open = li.classList.contains('nav-expanded');

        if (open === this.#open.has(li)) {
            return;
        }

        const submenu = getSubmenu(li);

        if (open) {
            this.#open.add(li);
            this.#thaw(submenu);
            this.#placeSubmenu(li);

            return;
        }

        this.#open.delete(li);

        // Contao entfernt die Markierung nur an Listen direkt im Menüpunkt; steckt die Liste
        // in einer Hülle, übernimmt das Skript – nach dem Ausblenden, wenn eingestellt.
        if (submenu && submenu.parentElement !== li) {
            const marks = ['bounds-right', 'bounds-left'].filter((mark) => submenu.classList.contains(mark));

            if (marks.length && this.#isFading(submenu)) {
                this.#freeze(submenu, marks);
            } else {
                submenu.classList.remove('bounds-right', 'bounds-left');
            }
        }

        // Contao hat ein wartendes Untermenü selbst geschlossen (Escape, Fokus, Klick):
        // Das Warten ist damit hinfällig.
        if (this.#pending?.items.includes(li)) {
            this.#cancel();
        }
    }

    /** Menüpunkt ab der zweiten Ebene: Sein Untermenü öffnet seitlich. */
    #opensSideways(li) {
        const parent = li.parentElement.closest('li');

        return !!parent && this.#navigation.contains(parent);
    }

    #placeSubmenu(li) {
        if (!this.#direction || !isDesktop() || !this.#opensSideways(li)) {
            return;
        }

        const submenu = getSubmenu(li);

        if (!submenu) {
            return;
        }

        if ('rechts' === this.#direction) {
            submenu.classList.remove('bounds-right', 'bounds-left');

            return;
        }

        // Liegt die Liste direkt im Menüpunkt, hat Contao schon gemessen. Steckt sie in einer
        // Hülle, misst das Skript – mit denselben Regeln wie Contao.
        if (submenu.parentElement === li) {
            return;
        }

        submenu.classList.remove('bounds-right', 'bounds-left');

        const rect = submenu.getBoundingClientRect();

        if (rect.right >= window.innerWidth) {
            submenu.classList.add('bounds-right');
        } else if (rect.left < 0) {
            submenu.classList.add('bounds-left');
        }
    }

    /**
     * Ein- und Ausblenden: Contao entfernt die Markierung für die Öffnungsrichtung
     * (bounds-right/-left) sofort beim Schließen. Während das Untermenü noch ausblendet,
     * spränge es dadurch zur anderen Seite – die Markierung bleibt deshalb bis zum Ende
     * der Überblendung stehen.
     */
    #keepPosition(ul, oldValue) {
        const marks = ['bounds-right', 'bounds-left'].filter((mark) => (oldValue || '').split(/\s+/).includes(mark) && !ul.classList.contains(mark));

        if (!marks.length) {
            return;
        }

        const li = ul.parentElement.closest('li');

        if (li && !li.classList.contains('nav-expanded') && getSubmenu(li) === ul && this.#isFading(ul)) {
            this.#freeze(ul, marks);
        }
    }

    /** Blendet die Liste gerade aus? Ohne Überblendung ist sie sofort nicht mehr sichtbar. */
    #isFading(ul) {
        return this.#fadeTime > 0 && isDesktop() && 'none' !== getComputedStyle(ul).display;
    }

    #freeze(ul, marks) {
        this.#thaw(ul);
        ul.classList.add(...marks);

        this.#frozen.set(ul, setTimeout(() => {
            this.#frozen.delete(ul);

            const li = ul.parentElement.closest('li');

            if (!li?.classList.contains('nav-expanded')) {
                ul.classList.remove(...marks);
            }
        }, this.#fadeTime + 50));
    }

    /** Öffnet das Untermenü wieder, bevor es ganz ausgeblendet ist: Markierung bleibt, wie sie ist. */
    #thaw(ul) {
        if (ul && this.#frozen.has(ul)) {
            clearTimeout(this.#frozen.get(ul));
            this.#frozen.delete(ul);
        }
    }

    /** „Immer rechts“: Markierungen, die Contao an seitlichen Untermenüs setzt, entfernen. */
    #keepRight(ul) {
        if (!ul.classList.contains('bounds-right') && !ul.classList.contains('bounds-left')) {
            return;
        }

        const li = ul.parentElement.closest('li');

        if (li && this.#navigation.contains(li) && this.#opensSideways(li) && getSubmenu(li) === ul) {
            ul.classList.remove('bounds-right', 'bounds-left');
        }
    }
}

// Welches Gerät zuletzt bedient wurde: Touch und Stift bleiben bei Contaos Verhalten,
// auch auf Geräten, die zusätzlich eine Maus haben.
const rememberPointer = (event) => {
    pointerType = event.pointerType;
};

document.addEventListener('pointerdown', rememberPointer, { capture: true, passive: true });
document.addEventListener('pointermove', rememberPointer, { capture: true, passive: true });

document.addEventListener('mousemove', (event) => {
    for (const instance of instances) {
        instance.handleMove(event);
    }

    history.push({ x: event.clientX, y: event.clientY });

    if (history.length > HISTORY_LENGTH) {
        history.shift();
    }
}, { passive: true });

for (const wrapper of document.querySelectorAll('.custom-navigation')) {
    const navigation = wrapper.querySelector('.navigation-main');

    if (navigation && ['data-nav-dreieck', 'data-nav-wiedereinblenden', 'data-nav-richtung', 'data-nav-animation'].some((name) => wrapper.hasAttribute(name))) {
        instances.push(new CustomNavigation(wrapper, navigation));
    }
}
