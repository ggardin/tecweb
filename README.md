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
| Sito web       | https://gagg11y.tecweb.duckdns.org/main/        |
| Preview branch | https://gagg11y.tecweb.duckdns.org/branch-name/ |
| PhpMyAdmin     | https://gagg11y.tecweb.duckdns.org/pma/         |

Ad ogni push su un qualsiasi branch:
- il server riceve il contenuto del branch.
- la preview del banch diventa disponibile nella sottocartella `/nome-branch`.
- scattano i controlli di accessibilità, performance e usabilità.

## Licenza

[MIT](LICENSE)
