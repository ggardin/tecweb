# Progetto Tec Web

[![HTML5 validator](https://github.com/ggardin/tecweb/actions/workflows/validate-html.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/validate-html.yml)
[![Broken links](https://github.com/ggardin/tecweb/actions/workflows/check-broken-links.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/check-broken-links.yml)
[![A11y audit](https://github.com/ggardin/tecweb/actions/workflows/a11y-audit.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/a11y-audit.yml)
[![Site performance](https://github.com/ggardin/tecweb/actions/workflows/check-pagespeed-performance.yml/badge.svg)](https://github.com/ggardin/tecweb/actions/workflows/check-pagespeed-performance.yml)

## Server remoto di test

Disponiamo di un ambiente remoto per l'esecuzione del codice PHP. La macchina è configurata in modo da ricreare l'ambiente server del Paolotti.
Il server è ospitato su Azure. I link per l'accesso sono riportati qui sotto.

Per accedere al server tramite SSH, si può definire lo shorthand in `~/.ssh/config`:

```
Host tecweb
    HostName tecweb.duckdns.org
    User azureuser
    Port 22
```

## Link utili

| Descrizione    | Link                                            |
|----------------|-------------------------------------------------|
| Sito web       | https://gagg11y.tecweb.duckdns.org/dev/        |
| Preview branch | https://gagg11y.tecweb.duckdns.org/branch-name/ |
| PhpMyAdmin     | https://gagg11y.tecweb.duckdns.org/pma/         |

Ad ogni push su un qualsiasi branch:
- il server riceve il contenuto del branch.
- la preview del banch diventa disponibile nella sottocartella `/nome-branch`.
- scattano i controlli di accessibilità, performance e usabilità.

## Licenza

[MIT](LICENSE)

## Dati dei film
![TMDB attribution](img/tmdb.svg)
Tutti i dati relativi ai film presenti sono forniti da TMDB.

TMDB mette a disposizione tutti i dati presenti sul loro sito (Titoli, Autori, Immagini, descrizioni etc.) mediante API.

Abbiamo scaricato nel nostro database locale gli N film più votati — con relativi metadati — tramite uno script Python.

Tutti i marchi e/o nomi presenti su questo sito sono protetti dalle leggi internazionali di copyright/trademark e sono di esclusiva pertinenza dei proprietari.

Questo progetto è stato creato a scopo educativo/universitario: non ha scopo di lucro. Tutto il codice è disponibile gratuitamente su GitHub.

Non siamo sostenuti né certificati da TMDB.
