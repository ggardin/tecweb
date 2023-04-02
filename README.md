# Progetto Tecnologie Web: soundstage

[![HTML5 validator](https://github.com/ggardin/tecweb/actions/workflows/validate-html.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/validate-html.yml)
[![Broken links](https://github.com/ggardin/tecweb/actions/workflows/check-broken-links.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/check-broken-links.yml)
[![A11y audit](https://github.com/ggardin/tecweb/actions/workflows/a11y-audit.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/a11y-audit.yml)
[![Site performance](https://github.com/ggardin/tecweb/actions/workflows/check-pagespeed-performance.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/check-pagespeed-performance.yml)

Sito web sviluppato per il corso di Tecnologie Web (BSc Informatica, A.A. 2022/23) dell'Università di Padova.

## Obiettivi

Scopo del progetto didattico è la realizzazione di un sito web accessibile.

### Specifiche tecniche

Riportiamo le specifiche tecniche del progetto didattico:

- il sito web deve essere realizzato con lo standard XHTML Strict, o HTML5. Le pagine in HTML5 devono degradare in modo elegante e devono rispettare la sintassi XML;
- il layout deve essere realizzato con CSS puri (CSS2 o CSS3);
- l’uso dei layout flex e grid, se sviluppati in maniera corretta ed utilizzati ragionevolmente, vengono valutati molto positivamente;
- il sito web deve rispettare la completa separazione tra contenuto, presentazione e comportamento;
- il sito web deve essere accessibile a tutte le categorie di utenti;
- il sito web deve organizzare i propri contenuti in modo da poter essere facilmente reperiti da qualsiasi utente;
- il sito web deve contenere pagine che utilizzino script PHP per collezionare e pubblicare dati inseriti dagli utenti (deve essere sviluppata anche la possibilità di modifica e cancellazione dei dati stessi);
- deve essere presente una forma di controllo dell’input inserito dall’utente, sia lato client che lato server
- i dati inseriti dagli utenti devono essere salvati in un database;
- è preferibile che il database sia in forma normale.

Il sito web deve essere basato sulle tecnologie dello stack [LAMP](https://it.wikipedia.org/wiki/LAMP).

## Valutazione e riconoscimenti

Il progetto è stato valutato con 30 e lode, al netto del bonus per la consegna durante la prima sessione.

Il sito è stato proposto al concorso [*Accattivante Accessibile 2023*](https://ilbolive.unipd.it/it/event/accattivante-accessibile-concorso-abilita), ottenendo il primo posto.

Le relazioni prodotte per la consegna del progetto e la partecipazione al concorso sono consultabili liberamente (i file si trovano in `/relazione`).

### Continous Integration

Abbiamo predisposto un container remoto per la CI, basato su Docker, ospitato su Azure e impostato per replicare il più fedelmente possibile lo stack LAMP del dipartimento. La pipeline CI è basata su GitHub Actions e prevede il deploy automatico dei branch sul server di test e una serie di controlli automatici.

Ad ogni push su un qualsiasi branch:
1. il server riceve il contenuto del branch tramite `rsync`.
1. la preview del branch diventa disponibile nella sottocartella `/nome-branch`.
1. scattano i controlli di accessibilità, performance e usabilità.

| Controllo     | Strumento                     |
|---------------|-------------------------------|
| Validazione   | html5validator CLI            |
| Link rotti    | Lychee CLI                    |
| Accessibilità | A11yWatch CLI                 |
| Prestazioni   | Google PageSpeed Insights API |

## Metodologia

Il sito web rispetta gli standard del [W3C](https://www.w3.org/) e del [WAI/WCAG](https://www.w3.org/WAI/standards-guidelines/wcag/). Lo sviluppo è avvenuto secondo i principi di [gitflow](https://nvie.com/posts/a-successful-git-branching-model/), opportunamente rivisti per adattarli alle esigenze di progetto.

## Licenza

[MIT](LICENSE)

## Movies data

<img alt="TMDB attribution" src="img/tmdb.svg" width="96">

- All movie-related data present on this site come from TMDB. It can be used freely as long as there is an attribution, as you can see in their [FAQ](https://www.themoviedb.org/about/logos-attribution).
- The data is a small subset of the most voted movies on their site, downloaded to a local copy with a Python script using their API.
- We are not affiliated with the TMDB or its subsidiaries.
- We also do not own any logos, trademarks, or other intellectual property associated with featured here.
- This project was created for educational purposes. It is NOT for profit.
