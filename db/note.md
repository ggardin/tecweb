# note

## postgres in locale con docker
```
sudo docker run -d --name pg_tecweb -e POSTGRES_PASSWORD=postgres -p 5432:5432 postgres:15-alpine
```

autenticazione: `postgres:postgres`

## lista drop table
```
grep "create table" schema.sql | cut -d' ' -f3 | tac
```

## tmdb.py

info
- creare api: https://www.themoviedb.org/settings/api
- documentazione api: https://developers.themoviedb.org/3
- wrapper usato: https://github.com/celiao/tmdbsimple
- per usare lo script imposta la variabile d'ambiente TMDB_API

TODO
- tenere solo dati cast e crew di interesse
- aggiungere mancanti:
	- serie tv
	- paese
	- compagnia
	- persona
- scaricare immagini da tmdb (??)
- export in sql
