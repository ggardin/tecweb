drop table if exists libreria_collezioni;
drop table if exists libreria_film;
drop table if exists collezione_film;
drop table if exists collezione_media;
drop table if exists collezione;
drop table if exists film_media;
drop table if exists votazione;
drop table if exists utente;
drop table if exists casting;
drop table if exists ruolo;
drop table if exists persone;
drop table if exists film_tipologia;
drop table if exists tipologia;
drop table if exists film_keyword;
drop table if exists keyword;
drop table if exists film;
drop table if exists compagnia;
drop table if exists paese;



create table paese (
	iso varchar(5),
	nome varchar(30),
	primary key (iso)
);

create table compagnia (
	id serial,
	nome varchar(50) not null,
	descrizione varchar(1000),
	data_fondazione date,
	paese_fondazione varchar(5),
	primary key (id),
	foreign key (paese_fondazione) references paese(iso)
);

create table film (
	id serial,
	titolo varchar(150) not null,
	descrizione varchar(10000),
	data_rilascio date,
	paese_produzione varchar(5),
	produttore int,
	stato varchar(50) not null,
	budget int,
	guadagno int,
	durata int,
	is_episodio boolean not null,
	copertina varchar(150),
	primary key (id),
	foreign key (paese_produzione) references paese(iso),
	foreign key (produttore) references compagnia(id)
);

create table keyword (
	id serial,
	nome varchar(30),
	primary key (id)
);

create table film_keyword (
	film int,
	keyword int,
	primary key (film, keyword),
	foreign key (film) references film(id),
	foreign key (keyword) references keyword(id)
);

create table tipologia (
	id serial,
	nome varchar(30),
	primary key (id)
);

create table film_tipologia (
	film int,
	tipologia int,
	primary key (film, tipologia),
	foreign key (film) references film(id),
	foreign key (tipologia) references tipologia(id)
);

create table persone (
	id serial,
	nome varchar(30) not null,
	descrizione varchar(1000),
	data_nascita date,
	paese_nascita varchar(30),
	genere varchar(30),
	primary key (id),
	foreign key (paese_nascita) references paese(iso)
);

create table ruolo (
	id serial,
	nome varchar(30) unique not null,
	primary key (id)
);

create table casting (
	film int,
	persone int,
	ruolo int,
	interpretazione varchar(30),
	primary key (film, persone, ruolo),
	foreign key (film) references film(id),
	foreign key (persone) references persone(id),
	foreign key (ruolo) references ruolo(id)
);

create table utente (
	id serial,
	mail varchar(50) unique not null,
	nome varchar(50),
	genere varchar(30),
	salt varchar(16) not null,
	password varchar(64) not null,
	is_admin boolean not null,
	primary key (id)
);

create table votazione (
	film int,
	utente int,
	voto smallint not null,
	primary key (film, utente),
	foreign key (film) references film(id),
	foreign key (utente) references utente(id)
);

create table film_media (
	film int,
	percorso varchar(150),
	descrizione varchar(300),
	ordine smallint not null,
	primary key (film, percorso),
	foreign key (film) references film(id)
);

create table collezione (
	id serial,
	nome varchar(150),
	descrizione varchar(1000),
	copertina varchar(150),
	exs varchar(50), -- lista con il numero di episodi per stagione
	primary key (id)
);

create table collezione_media (
	collezione int,
	percorso varchar(150),
	descrizione varchar(300),
	ordine smallint not null,
	primary key (collezione, percorso),
	foreign key (collezione) references collezione(id)
);

create table collezione_film (
	collezione int,
	film int,
	ordine smallint not null,
	primary key (collezione, film),
	foreign key (collezione) references collezione(id),
	foreign key (film) references film(id)
);

create table libreria_film (
	utente int,
	film int,
	primary key (utente, film),
	foreign key (utente) references utente(id),
	foreign key (film) references film(id)
);

create table libreria_collezioni (
	utente int,
	collezione int,
	primary key (utente, collezione),
	foreign key (utente) references utente(id),
	foreign key (collezione) references collezione(id)
);
