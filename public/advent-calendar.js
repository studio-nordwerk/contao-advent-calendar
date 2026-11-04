// Adventskalender: Türchen markieren, Inhalt beim Klick vom Server holen, leichter Schneefall
(() => {
    const STORAGE = 'advent-calendar-opened';

    // Geöffnete Türchen merken: { Tag: Bildadresse oder leer }. Im Demo-Modus wird nichts gemerkt.
    const readOpened = (key) => {
        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE) || '{}')[key] || {};

            // Version 0.1.0 hat nur die Tage als Liste gespeichert
            return Array.isArray(saved) ? Object.fromEntries(saved.map((day) => [day, ''])) : saved;
        } catch (e) {
            return {};
        }
    };

    const rememberOpened = (key, day, thumb) => {
        try {
            const all = JSON.parse(localStorage.getItem(STORAGE) || '{}');
            all[key] = { ...readOpened(key), [day]: thumb || '' };
            localStorage.setItem(STORAGE, JSON.stringify(all));
        } catch (e) {
            // Ohne Speicher geht es auch, die Türchen sind dann nur nicht als geöffnet markiert.
        }
    };

    // Tür aufklappen und, falls vorhanden, das Bild dahinter zeigen
    const showOpen = (door, thumb) => {
        door.classList.add('advent-calendar__door--opened');

        if (thumb) {
            door.style.setProperty('--ac-thumb', `url("${thumb}")`);
            door.classList.add('advent-calendar__door--thumb');
        }
    };

    // Nur fürs Aussehen; ob ein Türchen wirklich aufgeht, entscheidet der Server.
    const stateFor = (day, root) => {
        if (root.dataset.demo === '1') {
            return 'open';
        }

        const now = root.dataset.today ? new Date(`${root.dataset.today}T12:00:00`) : new Date();
        const year = parseInt(root.dataset.year, 10);
        const today = now.getFullYear() === year && now.getMonth() === 11 ? now.getDate() : (now.getFullYear() > year ? 99 : 0);

        if (day > today) {
            return 'early';
        }

        if (day === today) {
            return 'today';
        }

        return root.dataset.keepOpen === '1' ? 'open' : 'past';
    };

    const snow = (root) => {
        if (!root.hasAttribute('data-snow') || matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.className = 'advent-calendar__snow';
        canvas.setAttribute('aria-hidden', 'true');
        root.prepend(canvas);

        const ctx = canvas.getContext('2d');
        const flakes = [];
        let width = 0;
        let height = 0;

        const resize = () => {
            const ratio = window.devicePixelRatio || 1;
            width = root.clientWidth;
            height = root.clientHeight;
            canvas.width = width * ratio;
            canvas.height = height * ratio;
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        };

        resize();
        new ResizeObserver(resize).observe(root);

        for (let i = 0; i < Math.min(70, Math.round(width / 12)); i++) {
            flakes.push({ x: Math.random() * width, y: Math.random() * height, r: 1 + Math.random() * 2.5, s: .3 + Math.random() * .8, w: Math.random() * Math.PI * 2 });
        }

        let visible = true;
        new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; }).observe(root);

        const tick = () => {
            if (visible) {
                ctx.clearRect(0, 0, width, height);
                ctx.fillStyle = 'rgba(255, 255, 255, .85)';

                for (const f of flakes) {
                    f.y += f.s;
                    f.w += .01;
                    f.x += Math.sin(f.w) * .4;

                    if (f.y > height + 5) {
                        f.y = -5;
                        f.x = Math.random() * width;
                    }

                    ctx.beginPath();
                    ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
    };

    const init = (root) => {
        const url = root.dataset.adventCalendar;
        const dialog = root.querySelector('.advent-calendar__dialog');
        const content = root.querySelector('.advent-calendar__content');
        const demo = root.dataset.demo === '1';
        const opened = demo ? {} : readOpened(url);

        // Fenster beim Klick auf den dunklen Hintergrund schließen
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });

        // Gutscheincode in die Zwischenablage
        content.addEventListener('click', (event) => {
            const code = event.target.closest('[data-copy]');

            if (code && navigator.clipboard) {
                navigator.clipboard.writeText(code.dataset.copy).then(() => code.setAttribute('data-copied', ''));
            }
        });

        root.querySelectorAll('.advent-calendar__door').forEach((door) => {
            const day = parseInt(door.dataset.day, 10);
            const state = stateFor(day, root);

            door.classList.add(`advent-calendar__door--${state}`);

            if (day in opened && state !== 'early') {
                showOpen(door, opened[day]);
            }

            door.addEventListener('click', async () => {
                let html;

                try {
                    const target = new URL(url, location.href);
                    target.searchParams.set('day', day);

                    const response = await fetch(target, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                    const data = await response.json();

                    if (data.state === 'open') {
                        html = data.html;
                        showOpen(door, data.thumb);

                        if (!demo) {
                            rememberOpened(url, day, data.thumb);
                        }
                    } else if (data.state === 'early') {
                        html = root.dataset.textEarly.replace('%s', day);
                    } else if (data.state === 'past') {
                        html = root.dataset.textPast;
                    } else {
                        html = root.dataset.textEmpty;
                    }
                } catch (e) {
                    html = root.dataset.textError;
                }

                content.innerHTML = html;
                dialog.showModal();
            });
        });

        snow(root);
    };

    document.querySelectorAll('[data-advent-calendar]').forEach(init);
})();
