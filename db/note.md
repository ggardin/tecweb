# note

## testing in locale con docker
```
sudo docker run --name pg_tecweb -d -p 5432:5432 postgres:15-alpine
```

autenticazione: `postgres:postgres`

## lista drop table
```
grep "create table" schema.sql | cut -d' ' -f3 | tac
```
