<?php
if (!defined('ABSPATH')) { exit; }
$projects = (int) zp_suite_opt('stats.projects_count', 114);
$team_img = zp_suite_opt('about.team_image', 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_box.webp');
$team_w = (float) zp_suite_opt('about.team_width', 820);
$team_top = (float) zp_suite_opt('about.team_top', -260);
$team_right = (float) zp_suite_opt('about.team_right', 22);
$team_scale = (float) zp_suite_opt('about.team_scale', 1.18);
$team_gray = (float) zp_suite_opt('about.team_grayscale', 0);
$team_opacity = (float) zp_suite_opt('about.team_opacity', 1);
$cards_top = (float) zp_suite_opt('about.cards_top', 210);
$cards_wrap_min = max(438, min(980, $cards_top + 438));
?>
<section class="zpAboutExperience" id="zpAboutExperience" aria-labelledby="zpAboutExperienceTitle">
  <div class="zpAboutExperience__bleed" aria-hidden="true"></div>
  <div class="zpAboutExperience__mark" aria-hidden="true">strategy</div>

  <div class="zpAboutExperience__inner">
    <header class="zpAboutExperience__head">
      <span class="zpAboutExperience__eyebrow">O Zaprojektowani</span>
      <h2 id="zpAboutExperienceTitle">Projektujemy nie tylko ładny ekran, ale cały system widoczności, zaufania i zapytań.</h2>
    </header>

    <div class="zpAboutExperience__layout">
      <div class="zpAboutExperience__copy">
        <p class="zpAboutExperience__lead">
          Za każdą stroną internetową, sklepem i brandingiem stoi decyzja klienta: czy warto zaufać tej firmie. Dlatego łączymy projekt graficzny, treść, UX, SEO i kampanie w jeden spójny proces — tak, żeby marka wyglądała premium, była czytelna w Google i prowadziła użytkownika do zapytania.
        </p>

        <div class="zpAboutExperience__quote">
          <p>Najlepszy projekt to taki, który po wdrożeniu dalej pracuje: porządkuje ofertę, buduje wiarygodność, wzmacnia SEO i skraca drogę od pierwszego wejścia do kontaktu.</p>
          <strong>Mateusz <span>| CEO, Zaprojektowani</span></strong>
        </div>
      </div>

      <div class="zpAboutExperience__cardsWrap">
        <figure class="zpAboutExperience__team" aria-hidden="true">
          <img src="<?php echo esc_url($team_img); ?>" alt="" loading="lazy" decoding="async">
        </figure>

      <div class="zpAboutExperience__cards" role="group" aria-label="Co jest ważne w naszej pracy">
        <article>
          <i data-lucide="fingerprint"></i>
          <strong>Indywidualny kierunek</strong>
          <span>Nie kopiujemy układów. Każdy projekt dopasowujemy do branży, poziomu zaufania i celu sprzedażowego.</span>
        </article>
        <article>
          <i data-lucide="route"></i>
          <strong>Ścieżka zapytania</strong>
          <span>Układ strony, CTA, formularze i treści projektujemy tak, żeby użytkownik wiedział, co zrobić dalej.</span>
        </article>
        <article>
          <i data-lucide="search-check"></i>
          <strong>SEO od początku</strong>
          <span>Struktura nagłówków, sekcje, FAQ, linkowanie wewnętrzne i treści są planowane pod widoczność, nie dopisywane na końcu.</span>
        </article>
        <article>
          <i data-lucide="sparkles"></i>
          <strong><?php echo esc_html($projects); ?> wdrożeń</strong>
          <span>Pracujemy z firmami usługowymi, kancelariami, deweloperami, beauty, e-commerce i markami premium.</span>
        </article>
      </div>
      </div>
    </div>
  </div>

  <style>
    .zpAboutExperience,.zpAboutExperience *,.zpAboutExperience *::before,.zpAboutExperience *::after{box-sizing:border-box}
    .zpAboutExperience{--font:"Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;--navy:#05070b;--navy2:#071426;--blue:#102a4f;--blue2:#1c477a;--team-w:<?php echo esc_attr(max(260, min(980, $team_w))); ?>px;--team-top:<?php echo esc_attr(max(-360, min(120, $team_top))); ?>px;--team-right:<?php echo esc_attr(max(-220, min(260, $team_right))); ?>px;--team-scale:<?php echo esc_attr(max(.55, min(2.2, $team_scale))); ?>;--team-gray:<?php echo esc_attr(max(0, min(1, $team_gray))); ?>;--team-opacity:<?php echo esc_attr(max(0, min(1, $team_opacity))); ?>;--cards-top:<?php echo esc_attr(max(0, min(520, $cards_top))); ?>px;--cards-wrap-min:<?php echo esc_attr($cards_wrap_min); ?>px;position:relative;isolation:isolate;overflow:visible;color:#fff;font-family:var(--font);padding:clamp(76px,7vw,118px) 0;background:transparent}
    .zpAboutExperience__bleed{position:absolute;z-index:-3;inset:0;left:50%;width:100vw;transform:translateX(-50%);background:radial-gradient(circle at 8% 0%,rgba(28,71,122,.30),transparent 34%),radial-gradient(circle at 88% 20%,rgba(59,110,168,.16),transparent 32%),linear-gradient(135deg,#030407 0%,#05070b 48%,#071426 100%)}
    .zpAboutExperience__bleed::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,0,0,.88),rgba(7,20,38,.58),rgba(0,0,0,.82)),linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,0));pointer-events:none}
    .zpAboutExperience__mark{position:absolute;left:50%;bottom:-.08em;z-index:-1;transform:translateX(-50%);font-size:clamp(110px,18vw,300px);line-height:.78;font-weight:780;letter-spacing:-.11em;color:#fff;opacity:.05;white-space:nowrap;pointer-events:none;-webkit-mask-image:linear-gradient(90deg,transparent 0,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,#000 0,#000 54%,transparent 100%);mask-image:linear-gradient(90deg,transparent 0,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,#000 0,#000 54%,transparent 100%);-webkit-mask-composite:source-in;mask-composite:intersect}
    .zpAboutExperience__inner{width:min(1500px,calc(100% - 56px));margin:0 auto;position:relative;z-index:2}
    .zpAboutExperience__head{display:grid;grid-template-columns:minmax(0,1fr);gap:14px;margin-bottom:clamp(28px,4vw,54px)}
    .zpAboutExperience__eyebrow{display:inline-flex;align-items:center;gap:10px;color:rgba(255,255,255,.72);font-size:10px;line-height:1;text-transform:uppercase;letter-spacing:.13em;font-weight:850}
    .zpAboutExperience__eyebrow::before{content:"";width:24px;height:1px;background:linear-gradient(90deg,#102a4f,#3b6ea8)}
    .zpAboutExperience h2{max-width:1050px;margin:0;color:#fff;font-size:clamp(34px,4.8vw,72px);line-height:.94;letter-spacing:-.058em;font-weight:580;text-wrap:balance}
    .zpAboutExperience__layout{display:grid;grid-template-columns:minmax(0,.82fr) minmax(420px,1fr);gap:clamp(28px,5vw,78px);align-items:start}.zpAboutExperience__cardsWrap{position:relative;min-height:var(--cards-wrap-min);padding-top:var(--cards-top);z-index:4;overflow:visible}.zpAboutExperience__team{position:absolute;right:var(--team-right);top:var(--team-top);z-index:7;width:var(--team-w);max-width:none;margin:0;pointer-events:none;filter:grayscale(var(--team-gray)) contrast(1.02) brightness(1);opacity:var(--team-opacity);transform:scale(var(--team-scale)) translateZ(0);transform-origin:center bottom}.zpAboutExperience__team::after{content:"";position:absolute;left:8%;right:8%;bottom:-4%;height:22%;border-radius:999px;background:radial-gradient(ellipse at center,rgba(0,0,0,.34),rgba(0,0,0,0) 70%);filter:none;opacity:.62}.zpAboutExperience__team img{display:block;width:100%;height:auto;max-width:none;object-fit:contain;-webkit-mask-image:linear-gradient(180deg,#000 0%,#000 76%,rgba(0,0,0,.78) 86%,transparent 100%);mask-image:linear-gradient(180deg,#000 0%,#000 76%,rgba(0,0,0,.78) 86%,transparent 100%)}
    .zpAboutExperience__lead{margin:0;color:rgba(255,255,255,.74);font-size:clamp(15px,1.15vw,18px);line-height:1.68;letter-spacing:-.012em;max-width:720px}
    .zpAboutExperience__quote{margin-top:30px;padding:26px;border:1px solid rgba(255,255,255,.13);border-radius:28px;background:rgba(255,255,255,.055);box-shadow:inset 0 1px 0 rgba(255,255,255,.08);backdrop-filter:none}
    .zpAboutExperience__quote p{margin:0;color:#fff;font-size:clamp(20px,1.7vw,28px);line-height:1.15;letter-spacing:-.04em;font-weight:560}
    .zpAboutExperience__quote strong{display:block;margin-top:18px;color:#fff;font-size:15px;letter-spacing:-.02em}.zpAboutExperience__quote strong span{color:rgba(255,255,255,.58);font-weight:650}
    .zpAboutExperience__cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));border-top:1px solid rgba(255,255,255,.12);border-left:1px solid rgba(255,255,255,.12)}
    .zpAboutExperience__cards article{min-height:218px;padding:26px;border-right:1px solid rgba(255,255,255,.12);border-bottom:1px solid rgba(255,255,255,.12);background:linear-gradient(180deg,rgba(255,255,255,.052),rgba(255,255,255,.016));position:relative;overflow:hidden}
    .zpAboutExperience__cards article::after{content:"";position:absolute;left:22px;right:22px;bottom:0;height:3px;border-radius:999px 999px 0 0;background:linear-gradient(90deg,#102a4f,#1c477a,#3b6ea8);transform:scaleX(.28);transform-origin:left;opacity:.85;transition:.25s}
    .zpAboutExperience__cards article:hover::after{transform:scaleX(1)}
    .zpAboutExperience__cards i,.zpAboutExperience__cards svg{width:28px;height:28px;color:#fff;margin-bottom:24px}
    .zpAboutExperience__cards strong{display:block;color:#fff;font-size:clamp(23px,1.65vw,30px);line-height:1.02;letter-spacing:-.045em;font-weight:570}
    .zpAboutExperience__cards span{display:block;margin-top:13px;color:rgba(255,255,255,.68);font-size:13.5px;line-height:1.56}
    @media(max-width:980px){.zpAboutExperience{padding:54px 0;overflow:hidden}.zpAboutExperience__inner{width:calc(100% - 24px)}.zpAboutExperience__layout{grid-template-columns:1fr}.zpAboutExperience__cardsWrap{padding-top:0;min-height:0}.zpAboutExperience__team{display:none}.zpAboutExperience__cards{grid-template-columns:1fr}.zpAboutExperience h2{font-size:clamp(34px,9vw,48px);line-height:.98}.zpAboutExperience__mark{font-size:clamp(84px,26vw,150px)}}
  </style>
</section>
