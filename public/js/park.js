const toggle = document.querySelector('.menu-toggle');
const menu = document.querySelector('#site-menu');
const setMenuOpen = (isOpen) => {
    toggle.setAttribute('aria-expanded', String(isOpen));
    toggle.setAttribute('aria-label', isOpen ? 'Close all pages' : 'Open all pages');
    menu.hidden = !isOpen;
};
toggle.addEventListener('click', () => setMenuOpen(toggle.getAttribute('aria-expanded') !== 'true'));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setMenuOpen(false);
    }
});
const filters=document.querySelectorAll('[data-filter]');filters.forEach(b=>b.addEventListener('click',()=>{filters.forEach(f=>f.setAttribute('aria-pressed',String(f===b)));document.querySelectorAll('[data-category]').forEach(i=>i.hidden=b.dataset.filter!=='All'&&i.dataset.category!==b.dataset.filter);}));const dialog=document.querySelector('#lightbox');document.querySelectorAll('.gallery-item').forEach(b=>b.addEventListener('click',()=>{const img=b.querySelector('img');document.querySelector('#lightbox-image').src=img.src;document.querySelector('#lightbox-image').alt=img.alt;document.querySelector('#lightbox-caption').textContent=b.dataset.caption;dialog.showModal();}));document.querySelector('.lightbox-close').addEventListener('click',()=>dialog.close());dialog.addEventListener('click',e=>{if(e.target===dialog)dialog.close();});const arrival=document.querySelector('[name="arrival"]');const departure=document.querySelector('[name="departure"]');if(arrival){arrival.addEventListener('change',()=>{departure.min=arrival.value;departure.required=Boolean(arrival.value);});}

const heroBooking = document.querySelector('[data-hero-booking]');
if (heroBooking) {
    heroBooking.addEventListener('submit', () => {
        const nationality = heroBooking.querySelector('[data-booking-nationality]').value;
        const promoCode = heroBooking.querySelector('[data-booking-promo]').value.trim();
        heroBooking.querySelector('[data-booking-message]').value = `I would like to enquire about a stay. Nationality: ${nationality}.${promoCode ? ` Promo code: ${promoCode}.` : ''}`;
    });
}

const wildlifeJourney = document.querySelector('[data-wildlife-journey]');
if (wildlifeJourney) {
    const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const hero = wildlifeJourney.querySelector('.hero');
    const discovery = wildlifeJourney.querySelector('.discover');
    const discoveryCards = [...discovery.querySelectorAll('.discovery-card')];
    const exploreLink = wildlifeJourney.querySelector('.scroll-note');
    const livingMode = wildlifeJourney.dataset.livingAnimation === 'true';
    const leopard = wildlifeJourney.querySelector(livingMode ? '.hero-photo' : '.leopard-sprite');
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
    if (leopard && !livingMode) {
        walkingAtlas.src = leopard.dataset.animationSrc;
    }
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
        if (leopard && !livingMode) {
            leopard.style.backgroundPosition = `${(stride % 4) * 100 / 3}% ${Math.floor(stride / 4) * 50}%`;
            wildlifeJourney.style.setProperty('--leopard-travel', `${-ease(walking) * 16}vw`);
            wildlifeJourney.style.setProperty('--leopard-scale', (1 + ease(walking) * 1.15).toFixed(4));
            wildlifeJourney.style.setProperty('--leopard-bob', `${Math.sin(walking * Math.PI * 8) * 4}px`);
        }
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
        const actorBounds = leopard?.getBoundingClientRect();
        const visibleSpots = actorBounds ? furSpots.map((spot) => ({
            x: actorBounds.left + spot.x * actorBounds.width,
            y: actorBounds.top + spot.y * actorBounds.height,
        })).filter((spot) => spot.x > 30 && spot.x < window.innerWidth - 30 && spot.y > 30 && spot.y < window.innerHeight - 30) : [];
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
        if (motionPreference.matches && leopard && !livingMode) {
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
    if (livingMode) {
        const livingScene = document.createElement('canvas');
        livingScene.className = 'living-wildlife';
        livingScene.setAttribute('aria-hidden', 'true');
        leopard.after(livingScene);
        const sceneContext = livingScene.getContext('2d');
        const sceneBase = document.createElement('canvas');
        const baseContext = sceneBase.getContext('2d');
        const chestPatch = document.createElement('canvas');
        const patchContext = chestPatch.getContext('2d');
        let chestRegion = null;
        let headRegion = null;
        let sceneTransform = null;
        const glanceFrames = [leopard, ...['leopard-glance-early.png', 'leopard-glance-half.png', 'leopard-glance-late.png', 'leopard-glance-look.png'].map((name) => {
            const frame = new Image();
            frame.src = new URL(`../images/${name}`, document.querySelector('script[src*="park.js"]').src).href;
            return frame;
        })];
        const headPatches = glanceFrames.map(() => document.createElement('canvas'));
        const morphCanvas = document.createElement('canvas');
        morphCanvas.width = 192;
        morphCanvas.height = 148;
        const morphContext = morphCanvas.getContext('2d');
        const morphPixels = morphContext.createImageData(192, 148);
        let morphFrames = [];
        let motionFields = [];
        const buildMotionField = (first, second) => {
            const width = 192;
            const height = 148;
            const columns = 12;
            const rows = 9;
            const vectors = [];
            const luminance = (pixels, x, y) => {
                const offset = (Math.max(0, Math.min(height - 1, y)) * width + Math.max(0, Math.min(width - 1, x))) * 4;
                return pixels[offset] * 0.3 + pixels[offset + 1] * 0.6 + pixels[offset + 2] * 0.1;
            };
            for (let row = 0; row <= rows; row++) {
                for (let column = 0; column <= columns; column++) {
                    const x = Math.round(column * (width - 1) / columns);
                    const y = Math.round(row * (height - 1) / rows);
                    let best = Infinity;
                    let vector = { x: 0, y: 0 };
                    for (let dy = -10; dy <= 10; dy += 2) {
                        for (let dx = -10; dx <= 10; dx += 2) {
                            let error = (dx * dx + dy * dy) * 0.12;
                            for (let sy = -4; sy <= 4; sy += 2) {
                                for (let sx = -4; sx <= 4; sx += 2) {
                                    error += Math.abs(luminance(first, x + sx, y + sy) - luminance(second, x + sx + dx, y + sy + dy));
                                }
                            }
                            if (error < best) {
                                best = error;
                                vector = { x: dx, y: dy };
                            }
                        }
                    }
                    const edge = Math.min(1, x / 24, (width - 1 - x) / 24, y / 18, (height - 1 - y) / 18);
                    vectors.push({ x: vector.x * edge, y: vector.y * edge });
                }
            }
            const dense = new Float32Array(width * height * 2);
            for (let y = 0; y < height; y++) {
                for (let x = 0; x < width; x++) {
                    const gx = x / (width - 1) * columns;
                    const gy = y / (height - 1) * rows;
                    const column = Math.min(columns - 1, Math.floor(gx));
                    const row = Math.min(rows - 1, Math.floor(gy));
                    const tx = gx - column;
                    const ty = gy - row;
                    const nodes = [vectors[row * (columns + 1) + column], vectors[row * (columns + 1) + column + 1], vectors[(row + 1) * (columns + 1) + column], vectors[(row + 1) * (columns + 1) + column + 1]];
                    const weights = [(1 - tx) * (1 - ty), tx * (1 - ty), (1 - tx) * ty, tx * ty];
                    for (let n = 0; n < 4; n++) {
                        dense[(y * width + x) * 2] += nodes[n].x * weights[n];
                        dense[(y * width + x) * 2 + 1] += nodes[n].y * weights[n];
                    }
                }
            }
            return dense;
        };
        const prepareMorph = () => {
            if (!glanceFrames.every((frame) => frame.complete && frame.naturalWidth)) {
                return;
            }
            morphFrames = headPatches.map((patch) => {
                morphContext.clearRect(0, 0, 192, 148);
                morphContext.drawImage(patch, 0, 0, 192, 148);
                return morphContext.getImageData(0, 0, 192, 148).data;
            });
            motionFields = morphFrames.slice(0, -1).map((frame, index) => ({ forward: buildMotionField(frame, morphFrames[index + 1]), backward: buildMotionField(morphFrames[index + 1], frame) }));
        };
        const drawMorph = (position) => {
            const lower = Math.min(3, Math.floor(position));
            const blend = position - lower;
            const first = morphFrames[lower];
            const second = morphFrames[lower + 1];
            const field = motionFields[lower];
            const sample = (pixels, x, y, channel) => {
                x = Math.max(0, Math.min(190.999, x));
                y = Math.max(0, Math.min(146.999, y));
                const ix = Math.floor(x);
                const iy = Math.floor(y);
                const fx = x - ix;
                const fy = y - iy;
                const offset = (iy * 192 + ix) * 4 + channel;
                return pixels[offset] * (1 - fx) * (1 - fy) + pixels[offset + 4] * fx * (1 - fy) + pixels[offset + 768] * (1 - fx) * fy + pixels[offset + 772] * fx * fy;
            };
            for (let y = 0; y < 148; y++) {
                for (let x = 0; x < 192; x++) {
                    const pixel = y * 192 + x;
                    const ax = x - field.forward[pixel * 2] * blend;
                    const ay = y - field.forward[pixel * 2 + 1] * blend;
                    const bx = x - field.backward[pixel * 2] * (1 - blend);
                    const by = y - field.backward[pixel * 2 + 1] * (1 - blend);
                    for (let channel = 0; channel < 4; channel++) {
                        morphPixels.data[pixel * 4 + channel] = sample(first, ax, ay, channel) * (1 - blend) + sample(second, bx, by, channel) * blend;
                    }
                }
            }
            morphContext.putImageData(morphPixels, 0, 0);
            sceneContext.drawImage(morphCanvas, headRegion.x, headRegion.y, headRegion.width, headRegion.height);
        };
        const prepareHeadPatches = () => {
            if (!sceneTransform || !sceneReady) {
                return;
            }
            const { scale, left, top } = sceneTransform;
            headRegion = { x: left + (leopard.naturalWidth - 735) * scale, y: top + 185 * scale, width: 195 * scale, height: 150 * scale };
            glanceFrames.forEach((frame, index) => {
                if (!frame.complete || !frame.naturalWidth) {
                    return;
                }
                const patch = headPatches[index];
                patch.width = Math.ceil(headRegion.width);
                patch.height = Math.ceil(headRegion.height);
                const context = patch.getContext('2d');
                const xRatio = frame.naturalWidth / leopard.naturalWidth;
                const yRatio = frame.naturalHeight / leopard.naturalHeight;
                context.setTransform(-1, 0, 0, 1, patch.width, 0);
                context.drawImage(frame, 540 * xRatio, 185 * yRatio, 195 * xRatio, 150 * yRatio, 0, 0, patch.width, patch.height);
                context.resetTransform();
                context.globalCompositeOperation = 'destination-in';
                for (const horizontal of [true, false]) {
                    const feather = context.createLinearGradient(0, 0, horizontal ? patch.width : 0, horizontal ? 0 : patch.height);
                    feather.addColorStop(0, 'transparent');
                    feather.addColorStop(0.12, '#000');
                    feather.addColorStop(0.88, '#000');
                    feather.addColorStop(1, 'transparent');
                    context.fillStyle = feather;
                    context.fillRect(0, 0, patch.width, patch.height);
                }
            });
            prepareMorph();
        };
        let sceneReady = false;
        let sceneVisible = true;
        let lastBreathFrame = 0;
        let smoothedGlance = 0;
        const prepareLivingScene = () => {
            if (!leopard.complete || !leopard.naturalWidth) {
                return;
            }
            const width = hero.clientWidth;
            const height = hero.clientHeight;
            livingScene.width = sceneBase.width = width;
            livingScene.height = sceneBase.height = height;
            const scale = Math.max(width / leopard.naturalWidth, height / leopard.naturalHeight);
            const imageWidth = leopard.naturalWidth * scale;
            const imageHeight = leopard.naturalHeight * scale;
            const left = Math.max(width - imageWidth, Math.min(0, width * 0.7 - (leopard.naturalWidth - 500) * scale));
            const top = (height - imageHeight) * 0.64;
            baseContext.setTransform(-1, 0, 0, 1, left + imageWidth, top);
            baseContext.drawImage(leopard, 0, 0, imageWidth, imageHeight);
            baseContext.resetTransform();
            chestRegion = { x: left + (leopard.naturalWidth - 620) * scale, y: top + 285 * scale, width: 240 * scale, height: 155 * scale };
            chestPatch.width = Math.ceil(chestRegion.width);
            chestPatch.height = Math.ceil(chestRegion.height);
            furSpots = [];
            const source = document.createElement('canvas');
            source.width = leopard.naturalWidth;
            source.height = leopard.naturalHeight;
            const sourceContext = source.getContext('2d', { willReadFrequently: true });
            sourceContext.drawImage(leopard, 0, 0);
            const pixels = sourceContext.getImageData(0, 0, source.width, source.height).data;
            for (let y = 290; y < 425; y += 9) {
                for (let x = 390; x < 610; x += 9) {
                    const offset = (y * source.width + x) * 4;
                    if (pixels[offset] < 85 && pixels[offset + 1] < 70 && pixels[offset + 2] < 60) {
                        furSpots.push({ x: (left + (source.width - x) * scale) / width, y: (top + y * scale) / height });
                    }
                }
            }
            sceneContext.drawImage(sceneBase, 0, 0);
            sceneReady = true;
            sceneTransform = { scale, left, top };
            prepareHeadPatches();
            hero.classList.add('has-living-scene');
            requestJourneyFrame();
        };
        const breathe = (timestamp) => {
            if (sceneReady && sceneVisible && !document.hidden && !motionPreference.matches && (renderedProgress || 0) < 0.9 && timestamp - lastBreathFrame >= 33) {
                const glanceElapsed = Math.min(80, timestamp - lastBreathFrame || 33);
                lastBreathFrame = timestamp;
                const expansion = 1 + Math.sin(timestamp / 1200) * 0.004;
                const { x, y, width, height } = chestRegion;
                patchContext.resetTransform();
                patchContext.clearRect(0, 0, chestPatch.width, chestPatch.height);
                patchContext.globalCompositeOperation = 'source-over';
                patchContext.drawImage(sceneBase, x, y, width, height, width * (1 - expansion) / 2, height * (1 - expansion) / 2, width * expansion, height * expansion);
                patchContext.globalCompositeOperation = 'destination-in';
                patchContext.setTransform(width / 2, 0, 0, height / 2, width / 2, height / 2);
                const feather = patchContext.createRadialGradient(0, 0, 0, 0, 0, 1);
                feather.addColorStop(0.45, '#000');
                feather.addColorStop(1, 'transparent');
                patchContext.fillStyle = feather;
                patchContext.fillRect(-1, -1, 2, 2);
                patchContext.resetTransform();
                sceneContext.drawImage(sceneBase, 0, 0);
                sceneContext.drawImage(chestPatch, x, y);
                const targetGlance = ease(clamp(((renderedProgress || 0) - 0.025) / 0.38));
                const change = (targetGlance - smoothedGlance) * (1 - Math.exp(-glanceElapsed / 150));
                smoothedGlance += Math.max(-glanceElapsed / 700, Math.min(glanceElapsed / 700, change));
                if (Math.abs(smoothedGlance - targetGlance) < 0.0001) {
                    smoothedGlance = targetGlance;
                }
                const glance = smoothedGlance * (glanceFrames.length - 1);
                if (headRegion && glance > 0 && glanceFrames.every((frame) => frame.complete && frame.naturalWidth)) {
                    if (motionFields.length === 4) {
                        drawMorph(glance);
                    }
                    sceneContext.globalAlpha = 1;
                }
                livingScene.dataset.glanceProgress = glance.toFixed(4);
            }
            window.requestAnimationFrame(breathe);
        };
        new IntersectionObserver((entries) => { sceneVisible = entries[0].isIntersecting; }).observe(hero);
        glanceFrames.forEach((frame) => frame.addEventListener('load', prepareHeadPatches));
        leopard.addEventListener('load', prepareLivingScene);
        window.addEventListener('resize', prepareLivingScene);
        motionPreference.addEventListener('change', prepareLivingScene);
        prepareLivingScene();
        window.requestAnimationFrame(breathe);
    } else {
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
    }
    configureJourney();
}
