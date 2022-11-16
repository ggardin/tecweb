drop table if exists lista_film;
drop table if exists lista_collezione;
drop table if exists lista;
drop table if exists valutazione;
drop table if exists utente;
drop table if exists film_persona;
drop table if exists film_keyword;
drop table if exists film_compagnia;
drop table if exists film_paese;
drop table if exists film_genere;
drop table if exists film;
drop table if exists collezione;
drop table if exists lingua;
drop table if exists ruolo;
drop table if exists persona;
drop table if exists keyword;
drop table if exists compagnia;
drop table if exists paese;
drop table if exists genere;





create table genere (
	id int,
	nome varchar(50),
	primary key (id)
);

create table paese (
	iso_3166_1 char(2),
	nome varchar(30),
	primary key (iso_3166_1)
);

create table compagnia (
	id int,
	nome varchar(60) not null,
	descrizione varchar(1000),
	data_fondazione date,
	paese_fondazione char(2),
	primary key (id),
	foreign key (paese_fondazione) references paese(iso_3166_1)
);

create table keyword (
	id int,
	nome varchar(50),
	primary key (id)
);

create table persona (
	id int,
	nome varchar(30) not null,
	descrizione varchar(1000),
	data_nascita date,
	paese_nascita char(2),
	genere varchar(30),
	primary key (id),
	foreign key (paese_nascita) references paese(iso_3166_1)
);

create table ruolo (
	id int,
	nome varchar(30) unique not null,
	primary key (id)
);

create table lingua (
	iso_639_1 char(2),
	nome varchar(30),
	primary key (iso_639_1)
);





create table collezione (
	id int,
	titolo varchar(200) not null,
	descrizione varchar(10000),
	copertina varchar(100),
	primary key (id)
);

create table film (
	id int,
	titolo varchar(200) not null,
	titolo_originale varchar(200) not null,
	lingua_originale varchar(2) not null,
	descrizione varchar(10000),
	data_rilascio date,
	budget int,
	incassi int,
	durata smallint,
	voto smallint,
	collezione int,
	stato varchar(30),
	copertina varchar(100),
	primary key (id),
	foreign key (collezione) references collezione(id)
);





create table film_genere (
	film int,
	genere int,
	primary key (film, genere),
	foreign key (film) references film(id),
	foreign key (genere) references genere(id)
);

create table film_paese (
	film int,
	paese char(2),
	primary key (film, paese),
	foreign key (film) references film(id),
	foreign key (paese) references paese(iso_3166_1)
);

create table film_compagnia (
	film int,
	compagnia int,
	primary key (film, compagnia),
	foreign key (film) references film(id),
	foreign key (compagnia) references compagnia(id)
);

create table film_keyword (
	film int,
	keyword int,
	primary key (film, keyword),
	foreign key (film) references film(id),
	foreign key (keyword) references keyword(id)
);

create table film_persona (
	film int,
	persona int,
	ruolo int,
	interpreta varchar(30),
	primary key (film, persona, ruolo),
	foreign key (film) references film(id),
	foreign key (persona) references persona(id),
	foreign key (ruolo) references ruolo(id)
);





create table utente (
	id int,
	username varchar(30) unique not null,
	mail varchar(100) unique,
	nome varchar(50),
	genere varchar(30),
	data_nascita date,
	salt varchar(16) not null,
	password varchar(32) not null,
	is_admin boolean not null,
	primary key (id)
);

create table valutazione (
	utente int,
	film int,
	valore smallint not null,
	primary key (utente, film),
	foreign key (utente) references utente(id),
	foreign key (film) references film(id)
);

create table lista (
	id int,
	utente int,
	nome varchar(50),
	primary key (id),
	foreign key (utente) references utente(id)
);

create table lista_collezione (
	lista int,
	collezione int,
	primary key (lista, collezione),
	foreign key (lista) references lista(id),
	foreign key (collezione) references collezione(id)
);

create table lista_film (
	lista int,
	film int,
	primary key (lista, film),
	foreign key (lista) references lista(id),
	foreign key (film) references film(id)
);
