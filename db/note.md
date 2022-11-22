# note

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
- dipendenze python (pip):
```
tmdbsimple
deep-translator
pandas
sqlalchemy
PyMySQL
```
- env variables
export TMDB_API=.....
export DB_HOST=localhost
export DB_NAME=rbonavig
export DB_USER=rbonavig
export DB_PASS=paJa5The1eiM4hei
