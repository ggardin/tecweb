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
drop table if exists ruolo;
drop table if exists persona;
drop table if exists gender;
drop table if exists keyword;
drop table if exists compagnia;
drop table if exists paese;
drop table if exists genere;





create table genere (
	id int unsigned,
	nome varchar(50),
	primary key (id)
);

create table paese (
	iso_3166_1 char(2),
	nome varchar(100),
	primary key (iso_3166_1)
);

create table compagnia (
	id int unsigned,
	nome varchar(100) not null,
	logo varchar(100),
	paese_fondazione char(2),
	primary key (id),
	foreign key (paese_fondazione) references paese(iso_3166_1)
);

create table keyword (
	id int unsigned,
	nome varchar(50),
	primary key (id)
);

create table gender (
	id int unsigned,
	nome varchar(50) not null,
	primary key (id)
);

create table persona (
	id int unsigned,
	nome varchar(50) not null,
	gender int unsigned,
	immagine varchar(100),
	data_nascita date,
	data_morte date,
	luogo_nascita varchar(100),
	primary key (id),
	foreign key (gender) references gender(id)
);

create table ruolo (
	id int unsigned,
	nome varchar(50) unique not null,
	primary key (id)
);





create table collezione (
	id int unsigned,
	nome varchar(200) not null,
	descrizione varchar(10000),
	copertina varchar(100),
	primary key (id)
);

create table film (
	id int unsigned,
	titolo varchar(200) not null,
	titolo_originale varchar(200) not null,
	durata smallint unsigned,
	copertina varchar(100),
	descrizione varchar(10000),
	data_rilascio date,
	stato varchar(30),
	budget int unsigned,
	incassi int unsigned,
	collezione int unsigned,
	voto smallint unsigned,
	primary key (id),
	foreign key (collezione) references collezione(id)
);





create table film_genere (
	film int unsigned,
	genere int unsigned,
	primary key (film, genere),
	foreign key (film) references film(id),
	foreign key (genere) references genere(id)
);

create table film_paese (
	film int unsigned,
	paese char(2),
	primary key (film, paese),
	foreign key (film) references film(id),
	foreign key (paese) references paese(iso_3166_1)
);

create table film_compagnia (
	film int unsigned,
	compagnia int unsigned,
	primary key (film, compagnia),
	foreign key (film) references film(id),
	foreign key (compagnia) references compagnia(id)
);

create table film_keyword (
	film int unsigned,
	keyword int unsigned,
	primary key (film, keyword),
	foreign key (film) references film(id),
	foreign key (keyword) references keyword(id)
);

create table film_persona (
	id serial,
	film int unsigned,
	persona int unsigned,
	ruolo int unsigned,
	interpreta varchar(150),
	primary key (partecipazione),
	foreign key (film) references film(id),
	foreign key (persona) references persona(id),
	foreign key (ruolo) references ruolo(id)
);





create table utente (
	id int unsigned,
	username varchar(30) unique not null,
	mail varchar(100) unique,
	nome varchar(50),
	gender int unsigned,
	data_nascita date,
	salt varchar(16) not null,
	password varchar(32) not null,
	is_admin boolean not null,
	primary key (id),
	foreign key (gender) references gender(id)
);

create table valutazione (
	utente int unsigned,
	film int unsigned,
	valore smallint unsigned not null,
	primary key (utente, film),
	foreign key (utente) references utente(id),
	foreign key (film) references film(id)
);

create table lista (
	id int unsigned,
	utente int unsigned,
	nome varchar(50),
	primary key (id),
	foreign key (utente) references utente(id)
);

create table lista_collezione (
	lista int unsigned,
	collezione int unsigned,
	primary key (lista, collezione),
	foreign key (lista) references lista(id),
	foreign key (collezione) references collezione(id)
);

create table lista_film (
	lista int unsigned,
	film int unsigned,
	primary key (lista, film),
	foreign key (lista) references lista(id),
	foreign key (film) references film(id)
);
