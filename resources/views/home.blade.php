@extends('layouts.storefront')

@section('title', 'WEEB STATION | Wear Your Energy')

@section('content')

<style>
/* WEEB STATION — refreshed storefront landing page */
.ws-landing {
    --ws-ink: #171414;
    --ws-paper: #f8f5f1;
    --ws-white: #fffdfb;
    --ws-red: #a52a3b;
    --ws-red-dark: #671725;
    --ws-pink: #e8a4a8;
    --ws-muted: #706766;
    color: var(--ws-ink);
    background: var(--ws-paper);
    width: 100%;
    overflow: hidden;
    padding: 0 0 34px;
}
.ws-landing, .ws-landing * { box-sizing: border-box; }
.ws-landing a { color: inherit; text-decoration: none; }
.ws-container { width: min(1240px, calc(100% - 56px)); margin-inline: auto; }
.ws-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    color: var(--ws-red); font-size: 10px; font-weight: 800;
    letter-spacing: .19em; text-transform: uppercase;
}
.ws-eyebrow::before { content: ""; width: 24px; height: 2px; background: currentColor; }
.ws-section { padding-block: 86px; }
.ws-section-heading {
    display: flex; justify-content: space-between; align-items: end;
    gap: 28px; margin-bottom: 32px;
}
.ws-section-heading h2 {
    margin: 13px 0 0; font-size: clamp(2.35rem, 5vw, 4.4rem);
    line-height: .94; letter-spacing: -.07em; font-weight: 900;
}
.ws-section-heading p {
    max-width: 360px; margin: 0; color: var(--ws-muted);
    line-height: 1.8; font-size: .94rem;
}
.ws-btn {
    min-height: 50px; padding: 0 22px; display: inline-flex;
    align-items: center; justify-content: center; gap: 14px;
    border: 1px solid var(--ws-ink); border-radius: 4px;
    background: var(--ws-ink); color: #fff !important;
    font-size: 10px; font-weight: 850; letter-spacing: .12em;
    text-transform: uppercase; transition: .25s ease;
}
.ws-btn:hover { transform: translateY(-3px); background: var(--ws-red); border-color: var(--ws-red); }
.ws-btn--outline { background: transparent; color: var(--ws-ink) !important; }
.ws-btn--outline:hover { color: #fff !important; }

/* Hero */
.ws-hero {
    position: relative; isolation: isolate; display: grid;
    grid-template-columns: 1.05fr .95fr; align-items: center;
    gap: 26px; min-height: 590px; margin-top: 24px;
    padding: clamp(32px, 6vw, 76px); overflow: hidden;
    border-radius: 8px; color: #fff;
    background:
        radial-gradient(circle at 77% 26%, rgba(232,164,168,.2), transparent 28%),
        linear-gradient(118deg, #171414 0%, #32131a 62%, #681b2b 100%);
}
.ws-hero::before {
    content: "WS"; position: absolute; z-index: -1;
    right: -28px; bottom: -130px; color: rgba(255,255,255,.035);
    font-size: clamp(250px, 37vw, 520px); font-weight: 950;
    letter-spacing: -.13em; line-height: 1;
}
.ws-hero__content { position: relative; z-index: 2; animation: ws-enter-left .75s ease both; }
.ws-hero h1 {
    margin: 24px 0 22px; font-size: clamp(3.8rem, 8.5vw, 7.4rem);
    font-weight: 950; line-height: .78; letter-spacing: -.095em;
}
.ws-hero h1 span { display: block; color: #fff5ef; }
.ws-hero h1 .ws-outline-text { color: transparent; -webkit-text-stroke: 1.5px var(--ws-pink); }
.ws-hero__description { max-width: 430px; margin: 0 0 28px; color: #e8dfe0; font-size: 1rem; line-height: 1.85; }
.ws-hero__actions { display: flex; flex-wrap: wrap; gap: 11px; }
.ws-hero .ws-btn { background: #fff5ef; color: var(--ws-ink) !important; border-color: #fff5ef; }
.ws-hero .ws-btn:hover { background: var(--ws-pink); border-color: var(--ws-pink); }
.ws-hero .ws-btn--outline { background: transparent; color: #fff !important; border-color: rgba(255,255,255,.42); }
.ws-hero .ws-btn--outline:hover { background: #fff5ef; color: var(--ws-ink) !important; }
.ws-hero__art {
    position: relative; min-height: 390px; display: grid; place-items: center;
    animation: ws-enter-right .85s .1s ease both;
}
.ws-orbit {
    position: absolute; width: min(100%, 365px); aspect-ratio: 1;
    border: 1px solid rgba(255,245,239,.28); border-radius: 50%;
}
.ws-orbit::before, .ws-orbit::after {
    content: ""; position: absolute; inset: 24px; border: 1px solid rgba(232,164,168,.28); border-radius: 50%;
}
.ws-orbit::after { inset: 54px; }
.ws-orbit__dot { position: absolute; top: 13%; right: 17%; width: 11px; height: 11px; border-radius: 50%; background: var(--ws-pink); box-shadow: 0 0 22px var(--ws-pink); }
.ws-art-card {
    position: relative; width: min(72%, 285px); aspect-ratio: .77;
    display: flex; flex-direction: column; justify-content: space-between;
    padding: 24px; border: 1px solid rgba(255,255,255,.45); border-radius: 5px;
    background: linear-gradient(145deg, #d98b91, #8d2435 54%, #291117);
    box-shadow: 0 28px 65px rgba(0,0,0,.35); transform: rotate(4deg);
    transition: transform .4s ease;
}
.ws-art-card:hover { transform: rotate(0) translateY(-5px); }
.ws-art-card__top, .ws-art-card__bottom {
    display: flex; justify-content: space-between; gap: 12px;
    color: #fff2ec; font-size: 9px; font-weight: 800; letter-spacing: .13em;
}
.ws-art-card__symbol { align-self: center; color: #fff2ec; font-size: clamp(5rem, 10vw, 9rem); font-weight: 950; line-height: .8; letter-spacing: -.13em; }
.ws-hero__note {
    position: absolute; right: -3px; bottom: 11%; padding: 12px 14px;
    border: 1px solid rgba(255,255,255,.22); border-radius: 3px;
    background: rgba(23,20,20,.88); color: #fff5ef;
    font-size: 9px; font-weight: 800; letter-spacing: .14em; transform: rotate(-4deg);
}

/* Moving brand strip */
.ws-marquee { margin-top: 18px; overflow: hidden; border-block: 1px solid #e4d9d4; background: #f0e7e2; }
.ws-marquee__track { display: flex; width: max-content; animation: ws-marquee 28s linear infinite; }
.ws-marquee__track span { padding: 18px 24px; color: var(--ws-ink); font-size: 10px; font-weight: 900; letter-spacing: .18em; white-space: nowrap; }
.ws-marquee__track span:nth-child(even) { color: var(--ws-red); }

/* Category cards */
.ws-category-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 18px; }
.ws-category {
    position: relative; min-height: 385px; display: flex; flex-direction: column;
    justify-content: space-between; padding: 25px; overflow: hidden;
    border-radius: 6px; color: #fff; background: #24191a;
    transition: transform .3s ease, box-shadow .3s ease;
}
.ws-category:nth-child(2) { background: #8f2637; }
.ws-category:nth-child(3) { background: #423033; }
.ws-category::before {
    content: attr(data-mark); position: absolute; top: 48px; right: -10px;
    color: rgba(255,255,255,.09); font-size: clamp(120px, 15vw, 190px);
    font-weight: 950; line-height: .8; letter-spacing: -.12em; transition: transform .45s ease;
}
.ws-category:hover { transform: translateY(-6px); box-shadow: 0 18px 36px rgba(47,22,26,.16); }
.ws-category:hover::before { transform: scale(1.08) rotate(-5deg); }
.ws-category__number { position: relative; z-index: 1; color: #f3c5c3; font-size: 10px; letter-spacing: .16em; }
.ws-category__bottom { position: relative; z-index: 1; }
.ws-category h3 { margin: 0 0 12px; color: #fff8f3; font-size: clamp(2.2rem, 3.6vw, 3.2rem); font-weight: 900; line-height: .94; letter-spacing: -.07em; }
.ws-category p { max-width: 250px; margin: 0 0 23px; color: #f0dddd; font-size: .88rem; line-height: 1.7; }
.ws-category__link {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 15px; border-top: 1px solid rgba(255,255,255,.27);
    color: #fff8f3; font-size: 9px; font-weight: 850; letter-spacing: .13em; text-transform: uppercase;
}
.ws-category__arrow { font-size: 22px; transition: transform .25s ease; }
.ws-category:hover .ws-category__arrow { transform: translate(4px,-3px); }

/* Collection banner */
.ws-collection {
    position: relative; display: grid; grid-template-columns: 1fr auto;
    align-items: center; gap: 25px; padding: clamp(32px, 6vw, 66px);
    overflow: hidden; border-radius: 7px; color: #fff;
    background: radial-gradient(circle at 90% 10%, rgba(232,164,168,.2), transparent 32%), #25171a;
}
.ws-collection h2 { max-width: 700px; margin: 16px 0; color: #fff5ef; font-size: clamp(2.5rem, 5.2vw, 4.8rem); font-weight: 950; line-height: .93; letter-spacing: -.075em; }
.ws-collection p { max-width: 520px; margin: 0; color: #dfd0d0; line-height: 1.8; }
.ws-collection .ws-eyebrow { color: var(--ws-pink); }
.ws-collection .ws-btn { background: #fff5ef; color: var(--ws-ink) !important; border-color: #fff5ef; white-space: nowrap; }
.ws-collection .ws-btn:hover { background: var(--ws-pink); border-color: var(--ws-pink); }

/* Brand values */
.ws-values { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 28px; }
.ws-value { padding: 25px 0 0; border-top: 2px solid #d7b7b3; }
.ws-value__number { color: var(--ws-red); font-size: 10px; font-weight: 900; letter-spacing: .14em; }
.ws-value h3 { margin: 16px 0 9px; color: var(--ws-ink); font-size: 1.25rem; letter-spacing: -.035em; }
.ws-value p { margin: 0; color: var(--ws-muted); font-size: .91rem; line-height: 1.85; }

/* Closing call to action */
.ws-closing {
    position: relative; padding: 76px 24px; overflow: hidden; text-align: center;
    border-radius: 7px; background: #ead1cc;
}
.ws-closing::before {
    content: "YOUR STYLE"; position: absolute; top: 50%; left: 50%;
    color: rgba(103,23,37,.055); font-size: clamp(55px,12vw,150px);
    font-weight: 950; letter-spacing: -.09em; white-space: nowrap; transform: translate(-50%,-50%);
}
.ws-closing > * { position: relative; }
.ws-closing .ws-eyebrow { justify-content: center; }
.ws-closing h2 { margin: 17px 0; color: var(--ws-ink); font-size: clamp(2.6rem,6vw,5.2rem); font-weight: 950; line-height: .93; letter-spacing: -.075em; }
.ws-closing p { max-width: 480px; margin: 0 auto 25px; color: #5c4547; line-height: 1.8; }

/* Scroll reveal and motion */
.ws-reveal { opacity: 0; transform: translateY(22px); transition: opacity .65s ease, transform .65s cubic-bezier(.2,.7,.2,1); }
.ws-reveal.is-visible { opacity: 1; transform: translateY(0); }
.ws-delay-1 { transition-delay: .1s; }
.ws-delay-2 { transition-delay: .2s; }
@keyframes ws-enter-left { from { opacity: 0; transform: translateX(-24px); } to { opacity: 1; transform: translateX(0); } }
@keyframes ws-enter-right { from { opacity: 0; transform: translateX(24px); } to { opacity: 1; transform: translateX(0); } }
@keyframes ws-marquee { to { transform: translateX(-50%); } }

@media (max-width: 850px) {
    .ws-hero { grid-template-columns: 1fr; padding: 42px 32px 28px; }
    .ws-hero__art { min-height: 330px; }
    .ws-category-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
    .ws-category:last-child { grid-column: 1 / -1; min-height: 310px; }
    .ws-collection { grid-template-columns: 1fr; }
    .ws-collection .ws-btn { justify-self: start; }
}
@media (max-width: 560px) {
    .ws-container { width: calc(100% - 28px); }
    .ws-section { padding-block: 58px; }
    .ws-section-heading { align-items: start; flex-direction: column; gap: 17px; }
    .ws-hero { min-height: auto; margin-top: 12px; padding: 37px 23px 20px; border-radius: 5px; }
    .ws-hero h1 { font-size: clamp(3.4rem,16vw,5rem); }
    .ws-hero__description { font-size: .94rem; }
    .ws-hero__art { min-height: 275px; }
    .ws-art-card { width: min(68%,245px); padding: 19px; }
    .ws-hero__note { right: -4px; bottom: 8%; }
    .ws-category-grid, .ws-values { grid-template-columns: 1fr; }
    .ws-category, .ws-category:last-child { grid-column: auto; min-height: 300px; }
    .ws-collection { padding: 32px 24px; }
    .ws-collection .ws-btn { white-space: normal; }
    .ws-closing { padding: 58px 18px; }
}
@media (prefers-reduced-motion: reduce) {
    .ws-landing *, .ws-landing *::before, .ws-landing *::after {
        animation-duration: .01ms !important; animation-iteration-count: 1 !important;
        scroll-behavior: auto !important; transition-duration: .01ms !important;
    }
    .ws-reveal { opacity: 1; transform: none; }
}
</style>

<div class="ws-landing">
    {{-- HERO --}}
    <section class="ws-hero ws-container">
        <div class="ws-hero__content">
            <span class="ws-eyebrow">Not made to blend in</span>
            <h1>WEEB <span class="ws-outline-text">STATION.</span></h1>
            <p class="ws-hero__description">
                For all the dwags and divas out there.</br>
                Let your fit do the drama.
            </p>
            <div class="ws-hero__actions">
                <a href="/shop/categories" class="ws-btn">Explore the fits <span aria-hidden="true">↗</span></a>
                <a href="#ws-categories" class="ws-btn ws-btn--outline">Find your style</a>
            </div>
        </div>
        <div class="ws-hero__art" aria-label="WEEB STATION brand artwork">
            <div class="ws-orbit"><span class="ws-orbit__dot"></span></div>
            <div class="ws-art-card">
                <div class="ws-art-card__top"><span>WEEB STATION</span><span>001</span></div>
                <div class="ws-art-card__symbol">WS.</div>
                <div class="ws-art-card__bottom"><span>OWN YOUR LOOK</span><span>EST. GEN-Z</span></div>
            </div>
            <div class="ws-hero__note">NO BASIC ENERGY.</div>
        </div>
    </section>

    {{-- BRAND STRIP --}}
    <div class="ws-marquee" aria-label="WEEB STATION brand message">
        <div class="ws-marquee__track">
            @for ($i = 0; $i < 2; $i++)
                <span>WEAR YOUR ENERGY ✳</span>
                <span>DWAGS &amp; DIVAS ✳</span>
                <span>MAKE YOUR OWN RULES ✳</span>
                <span>STAY UNCOMMON ✳</span>
            @endfor
        </div>
    </div>

    {{-- CATEGORIES --}}
    <section class="ws-section ws-container" id="ws-categories">
        <div class="ws-section-heading ws-reveal">
            <div>
                <span class="ws-eyebrow">Let your fit do the drama.</span>
                <h2>Find your<br>kind of different.</h2>
            </div>
            <p>Different moods. Different fits. One place to find the pieces that feel like you.</p>
        </div>
        <div class="ws-category-grid">
            <a href="/shop/categories/men" class="ws-category ws-reveal" data-mark="D" aria-label="Shop styles for men">
                <span class="ws-category__number">01 / THE DWAGS</span>
                <div class="ws-category__bottom">
                    <h3>For all<br>Dwags.</h3>
                    <p>Tees, shirts, denim and everyday statement pieces.</p>
                    <div class="ws-category__link"><span>Explore men's styles</span><span class="ws-category__arrow">↗</span></div>
                </div>
            </a>
            <a href="/shop/categories/women" class="ws-category ws-reveal ws-delay-1" data-mark="V" aria-label="Shop styles for women">
                <span class="ws-category__number">02 / THE DIVAS</span>
                <div class="ws-category__bottom">
                    <h3>For all<br>Divas.</h3>
                    <p>Standout pieces, accessories and looks worth repeating.</p>
                    <div class="ws-category__link"><span>Explore women's styles</span><span class="ws-category__arrow">↗</span></div>
                </div>
            </a>
            <a href="/shop/categories/everyone" class="ws-category ws-reveal ws-delay-2" data-mark="U" aria-label="Explore all styles">
                <span class="ws-category__number">03 / EVERYONE</span>
                <div class="ws-category__bottom">
                    <h3>Made for<br>y'all.</h3>
                    <p>Find your next favourite, whatever your style.</p>
                    <div class="ws-category__link"><span>Explore everything</span><span class="ws-category__arrow">↗</span></div>
                </div>
            </a>
        </div>
    </section>

    {{-- COLLECTION BANNER --}}
    <section class="ws-container">
        <div class="ws-collection ws-reveal">
            <div>
                <span class="ws-eyebrow">Your wardrobe called</span>
                <h2>Playing it safe<br>is so last season.</h2>
                <p>Build your rotation around pieces you actually love. Experiment, mix it up, and make the fit your own.</p>
            </div>
            <a href="/shop/categories" class="ws-btn">Find your next fit <span aria-hidden="true">↗</span></a>
        </div>
    </section>

    {{-- BRAND VALUES --}}
    <section class="ws-section ws-container">
        <div class="ws-section-heading ws-reveal">
            <div>
                <span class="ws-eyebrow">The WEEB mindset</span>
                <h2>More than<br>just a fit.</h2>
            </div>
            <p>Clothes are the canvas. You're the statement. Here's what we're about.</p>
        </div>
        <div class="ws-values">
            <article class="ws-value ws-reveal">
                <span class="ws-value__number">01 — YOUR RULES</span>
                <h3>Express yourself.</h3>
                <p>Your style doesn't need permission. Wear what feels right and make it unmistakably yours.</p>
            </article>
            <article class="ws-value ws-reveal ws-delay-1">
                <span class="ws-value__number">02 — YOUR ENERGY</span>
                <h3>Bring the vibe.</h3>
                <p>From laid-back everyday fits to looks that turn heads, dress for the energy you want to bring.</p>
            </article>
            <article class="ws-value ws-reveal ws-delay-2">
                <span class="ws-value__number">03 — YOUR IDENTITY</span>
                <h3>Stay uncommon.</h3>
                <p>Trends come and go. The best look is the one that feels like you, not everybody else.</p>
            </article>
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="ws-container">
        <div class="ws-closing ws-reveal">
            <span class="ws-eyebrow">This is your sign</span>
            <h2>Your fit.<br>Your statement.</h2>
            <p>Stop saving the looks. Start making them your own. Your next favourite piece is waiting.</p>
            <a href="/shop/categories" class="ws-btn">Explore WEEB STATION <span aria-hidden="true">↗</span></a>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const revealElements = document.querySelectorAll('.ws-landing .ws-reveal');

    if (!('IntersectionObserver' in window)) {
        revealElements.forEach(element => element.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -35px 0px' });

    revealElements.forEach(element => observer.observe(element));
});
</script>

@endsection
