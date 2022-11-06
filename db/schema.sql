drop table if exists libreria_collezione;
drop table if exists libreria_film;
drop table if exists collezione_film;
drop table if exists collezione_media;
drop table if exists collezione;
drop table if exists film_media;
drop table if exists votazione;
drop table if exists utente;
drop table if exists casting;
drop table if exists ruolo;
drop table if exists persona;
drop table if exists film_genere;
drop table if exists genere;
drop table if exists film_keyword;
drop table if exists keyword;
drop table if exists film_compagnia;
drop table if exists film_paese;
drop table if exists film;
drop table if exists compagnia;
drop table if exists paese;


create table paese (
	iso_3166-1 varchar(5),
	nome varchar(30),
	primary key (iso_3166-1)
);

create table compagnia (
	id serial,
	nome varchar(50) not null,
	descrizione varchar(1000),
	data_fondazione date,
	paese_fondazione varchar(5),
	primary key (id),
	foreign key (paese_fondazione) references paese(iso_3166-1)
);

create table film (
	id serial,
	titolo varchar(150) not null,
	descrizione varchar(10000),
	data_rilascio date,
	budget int,
	guadagno int,
	durata smallint,
	voto smallint,
	stato varchar(50),
	copertina varchar(500),
	is_episodio boolean not null,
	primary key (id)
);

create table film_paese (
	primary key (film, paese)
	foreign key (film) references film(id),
	foreign key (paese) references paese(iso_3166-1)
);

create table film_compagnia (
	primary key (film, compagnia)
	foreign key (film) references film(id),
	foreign key (compagnia) references compagnia(id)
);

create table keyword (
	id serial,
	nome varchar(50),
	primary key (id)
);

create table film_keyword (
	film int,
	keyword int,
	primary key (film, keyword),
	foreign key (film) references film(id),
	foreign key (keyword) references keyword(id)
);

create table genere (
	id serial,
	nome varchar(50),
	primary key (id)
);

create table film_genere (
	film int,
	genere int,
	primary key (film, genere),
	foreign key (film) references film(id),
	foreign key (genere) references genere(id)
);

create table persona (
	id serial,
	nome varchar(30) not null,
	descrizione varchar(1000),
	data_nascita date,
	paese_nascita varchar(30),
	genere varchar(30),
	primary key (id),
	foreign key (paese_nascita) references paese(iso_3166-1)
);

create table ruolo (
	id serial,
	nome varchar(30) unique not null,
	primary key (id)
);

create table casting (
	film int,
	persona int,
	ruolo int,
	interpretazione varchar(30),
	primary key (film, persona, ruolo),
	foreign key (film) references film(id),
	foreign key (persona) references persona(id),
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
	percorso varchar(500),
	descrizione varchar(300),
	ordine smallint not null,
	primary key (film, percorso),
	foreign key (film) references film(id)
);

create table collezione (
	id serial,
	nome varchar(150),
	descrizione varchar(1000),
	copertina varchar(500),
	exs varchar(50), -- lista con il numero di episodi per stagione
	primary key (id)
);

create table collezione_media (
	collezione int,
	percorso varchar(500),
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

create table libreria_collezione (
	utente int,
	collezione int,
	primary key (utente, collezione),
	foreign key (utente) references utente(id),
	foreign key (collezione) references collezione(id)
);
