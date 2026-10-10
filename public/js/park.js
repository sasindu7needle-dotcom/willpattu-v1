const toggle=document.querySelector('.menu-toggle');const menu=document.querySelector('#site-menu');toggle.addEventListener('click',()=>{const open=toggle.getAttribute('aria-expanded')!=='true';toggle.setAttribute('aria-expanded',String(open));menu.hidden=!open;toggle.textContent=open?'×':'☰';});document.addEventListener('keydown',e=>{if(e.key==='Escape'){menu.hidden=true;toggle.setAttribute('aria-expanded','false');toggle.textContent='☰';}});
const filters=document.querySelectorAll('[data-filter]');filters.forEach(b=>b.addEventListener('click',()=>{filters.forEach(f=>f.setAttribute('aria-pressed',String(f===b)));document.querySelectorAll('[data-category]').forEach(i=>i.hidden=b.dataset.filter!=='All'&&i.dataset.category!==b.dataset.filter);}));const dialog=document.querySelector('#lightbox');document.querySelectorAll('.gallery-item').forEach(b=>b.addEventListener('click',()=>{const img=b.querySelector('img');document.querySelector('#lightbox-image').src=img.src;document.querySelector('#lightbox-image').alt=img.alt;document.querySelector('#lightbox-caption').textContent=b.dataset.caption;dialog.showModal();}));document.querySelector('.lightbox-close').addEventListener('click',()=>dialog.close());dialog.addEventListener('click',e=>{if(e.target===dialog)dialog.close();});const arrival=document.querySelector('[name="arrival"]');const departure=document.querySelector('[name="departure"]');if(arrival){arrival.addEventListener('change',()=>{departure.min=arrival.value;departure.required=Boolean(arrival.value);});}

const wildlifeJourney = document.querySelector('[data-wildlife-journey]');
if (wildlifeJourney) {
    const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const hero = wildlifeJourney.querySelector('.hero');
    const discovery = wildlifeJourney.querySelector('.discover');
    const discoveryCards = [...discovery.querySelectorAll('.discovery-card')];
    const exploreLink = wildlifeJourney.querySelector('.scroll-note');
    const leopard = wildlifeJourney.querySelector('.leopard-sprite');
    const discoveryTitle = discovery.querySelector('h2');
    discoveryTitle.setAttribute('aria-label', discoveryTitle.textContent);
    for (const textContainer of [discoveryTitle, discoveryTitle.querySelector('em')]) {
        for (const textNode of [...textContainer.childNodes].filter((node) => node.nodeType === Node.TEXT_NODE)) {
            const letters = document.createDocumentFragment();
            for (const character of textNode.textContent) {
                const letter = document.createElement('span');
                letter.className = 'discovery-letter';
                letter.textContent = character;
                letter.setAttribute('aria-hidden', 'true');
                letters.append(letter);
            }
            textNode.replaceWith(letters);
        }
    }
    const titleLetters = [...discoveryTitle.querySelectorAll('.discovery-letter')];
    let furSpots = [];
    const walkingAtlas = new Image();
    walkingAtlas.src = new URL('../images/leopard-front-walk-atlas.png', document.querySelector('script[src*="park.js"]').src).href;
    const clamp = (value) => Math.min(1, Math.max(0, value));
    const ease = (value) => value * value * (3 - 2 * value);
    let framePending = false;
    let renderedProgress = null;
    let previousFrameTime = 0;
    let titleLayout = null;
    const measureTitle = () => {
        wildlifeJourney.style.setProperty('--heading-offset', '0px');
        titleLetters.forEach((letter) => { letter.style.transform = 'none'; });
        const stageBounds = wildlifeJourney.querySelector('.wildlife-stage').getBoundingClientRect();
        const titleBounds = discoveryTitle.getBoundingClientRect();
        titleLayout = {
            center: titleBounds.top - stageBounds.top + titleBounds.height / 2,
            letters: titleLetters.map((letter) => {
                const bounds = letter.getBoundingClientRect();
                return { x: bounds.left - stageBounds.left + bounds.width / 2, y: bounds.top - stageBounds.top + bounds.height / 2 };
            }),
        };
        titleLetters.forEach((letter) => { letter.style.removeProperty('transform'); });
    };

    const renderJourney = (timestamp) => {
        framePending = false;
        if (motionPreference.matches) {
            return;
        }
        const bounds = wildlifeJourney.getBoundingClientRect();
        const targetProgress = clamp(-bounds.top / Math.max(1, bounds.height - window.innerHeight));
        const elapsed = Math.min(50, timestamp - previousFrameTime || 16);
        previousFrameTime = timestamp;
        renderedProgress = renderedProgress === null ? targetProgress : renderedProgress + (targetProgress - renderedProgress) * (1 - Math.exp(-elapsed / 65));
        if (Math.abs(targetProgress - renderedProgress) < 0.0001) {
            renderedProgress = targetProgress;
        }
        const progress = renderedProgress;
        const walking = clamp(progress / 0.22);
        const stride = Math.min(11, Math.floor(walking * 12));
        leopard.style.backgroundPosition = `${(stride % 4) * 100 / 3}% ${Math.floor(stride / 4) * 50}%`;
        wildlifeJourney.style.setProperty('--leopard-travel', `${-ease(walking) * 16}vw`);
        wildlifeJourney.style.setProperty('--leopard-scale', (1 + ease(walking) * 1.15).toFixed(4));
        wildlifeJourney.style.setProperty('--leopard-bob', `${Math.sin(walking * Math.PI * 8) * 4}px`);
        const backgroundReveal = ease(clamp((progress - 0.64) / 0.26));
        const copyFade = 1 - ease(clamp(progress / 0.25));
        wildlifeJourney.style.setProperty('--camera-scale', '1');
        wildlifeJourney.style.setProperty('--copy-opacity', copyFade.toFixed(4));
        wildlifeJourney.style.setProperty('--copy-offset', `${-progress * 80}px`);
        wildlifeJourney.style.setProperty('--discover-opacity', progress >= 0.23 ? '1' : '0');
        wildlifeJourney.style.setProperty('--discovery-background', backgroundReveal.toFixed(4));
        wildlifeJourney.style.setProperty('--scene-opacity', (1 - backgroundReveal).toFixed(4));
        const stageBounds = wildlifeJourney.querySelector('.wildlife-stage').getBoundingClientRect();
        const headingOffset = (window.innerHeight * 0.44 - titleLayout.center) * (1 - backgroundReveal);
        wildlifeJourney.style.setProperty('--heading-offset', `${headingOffset}px`);
        const actorBounds = leopard.getBoundingClientRect();
        const visibleSpots = furSpots.map((spot) => ({
            x: actorBounds.left + spot.x * actorBounds.width,
            y: actorBounds.top + spot.y * actorBounds.height,
        })).filter((spot) => spot.x > 30 && spot.x < window.innerWidth - 30 && spot.y > 30 && spot.y < window.innerHeight - 30);
        titleLetters.forEach((letter, index) => {
            const emergence = clamp((progress - 0.24 - index * 0.008) / 0.24);
            const travel = ease(emergence);
            const spot = visibleSpots[Math.floor(index * visibleSpots.length / titleLetters.length)] || { x: window.innerWidth * 0.5, y: window.innerHeight * 0.45 };
            const target = titleLayout.letters[index];
            letter.style.setProperty('--letter-x', `${(spot.x - stageBounds.left - target.x) * (1 - travel)}px`);
            letter.style.setProperty('--letter-y', `${(spot.y - stageBounds.top - target.y - headingOffset) * (1 - travel)}px`);
            letter.style.setProperty('--letter-scale', (0.12 + travel * 0.88 + Math.sin(travel * Math.PI) * 0.2).toFixed(4));
            letter.style.setProperty('--letter-opacity', clamp(emergence * 4).toFixed(4));
            letter.style.setProperty('--letter-glow', `${Math.sin(travel * Math.PI) * 22}px`);
            letter.style.setProperty('--letter-color', emergence < 1 ? 'var(--gold)' : 'var(--cream)');
        });
        const isRevealed = progress >= 0.99;
        discovery.classList.toggle('is-revealed', isRevealed);
        hero.classList.toggle('is-departed', progress > 0.3);
        hero.inert = progress > 0.3;
        discovery.inert = false;
        discoveryCards.forEach((card, index) => {
            const arrival = ease(clamp((progress - 0.76 - index * 0.02) / 0.13));
            card.style.setProperty('--card-opacity', arrival.toFixed(4));
            card.style.setProperty('--card-offset', `${(1 - arrival) * 48}px`);
        });
        if (renderedProgress !== targetProgress) {
            requestJourneyFrame();
        }
    };
    const requestJourneyFrame = () => {
        if (!framePending) {
            framePending = true;
            window.requestAnimationFrame(renderJourney);
        }
    };
    const configureJourney = () => {
        wildlifeJourney.classList.toggle('is-animated', !motionPreference.matches);
        hero.inert = false;
        discovery.inert = false;
        if (motionPreference.matches) {
            leopard.style.backgroundPosition = '0% 0%';
        }
        renderedProgress = null;
        measureTitle();
        requestJourneyFrame();
    };
    window.addEventListener('scroll', requestJourneyFrame, { passive: true });
    window.addEventListener('resize', () => { measureTitle(); requestJourneyFrame(); });
    document.fonts.ready.then(() => { measureTitle(); requestJourneyFrame(); });
    motionPreference.addEventListener('change', configureJourney);
    exploreLink.addEventListener('click', (event) => {
        if (motionPreference.matches) {
            return;
        }
        event.preventDefault();
        const top = window.scrollY + wildlifeJourney.getBoundingClientRect().top;
        window.scrollTo({ top: top + wildlifeJourney.offsetHeight - window.innerHeight, behavior: 'smooth' });
    });
    discovery.addEventListener('focusin', () => {
        if (!motionPreference.matches && !discovery.classList.contains('is-revealed')) {
            const top = window.scrollY + wildlifeJourney.getBoundingClientRect().top;
            window.scrollTo({ top: top + wildlifeJourney.offsetHeight - window.innerHeight, behavior: 'instant' });
        }
    });
    walkingAtlas.addEventListener('load', () => {
        const frameWidth = walkingAtlas.naturalWidth / 4;
        const frameHeight = walkingAtlas.naturalHeight / 3;
        const canvas = document.createElement('canvas');
        canvas.width = Math.ceil(frameWidth);
        canvas.height = Math.ceil(frameHeight);
        const context = canvas.getContext('2d', { willReadFrequently: true });
        context.drawImage(walkingAtlas, frameWidth * 3, frameHeight * 2, frameWidth, frameHeight, 0, 0, canvas.width, canvas.height);
        const pixels = context.getImageData(0, 0, canvas.width, canvas.height).data;
        for (let y = 12; y < canvas.height * 0.65; y += 8) {
            for (let x = Math.floor(canvas.width * 0.2); x < canvas.width * 0.8; x += 8) {
                const offset = (y * canvas.width + x) * 4;
                const nearby = (y * canvas.width + Math.min(canvas.width - 1, x + 7)) * 4;
                if (pixels[offset + 3] > 220 && pixels[offset] < 85 && pixels[offset + 1] < 70 && pixels[offset + 2] < 60 && pixels[nearby] > 110 && pixels[nearby] > pixels[nearby + 2] * 1.2) {
                    furSpots.push({ x: x / canvas.width, y: y / canvas.height });
                }
            }
        }
        requestJourneyFrame();
    });
    configureJourney();
}
