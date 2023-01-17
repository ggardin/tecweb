drop table if exists lista_film;
drop table if exists lista;
drop table if exists valutazione;
drop table if exists utente;
drop table if exists crew;
drop table if exists film_paese;
drop table if exists film_genere;
drop table if exists film;
drop table if exists collezione;
drop table if exists ruolo;
drop table if exists persona;
drop table if exists gender;
drop table if exists paese;
drop table if exists genere;
drop table if exists stato;





create table stato (
	id tinyint unsigned,
	nome varchar(50) unique not null,
	primary key (id)
);

create table genere (
	id smallint unsigned,
	nome varchar(50) unique not null,
	primary key (id)
);

create table paese (
	iso_3166_1 char(2),
	nome varchar(100) unique not null,
	primary key (iso_3166_1)
);

create table gender (
	id tinyint unsigned,
	nome varchar(50) unique not null,
	primary key (id)
);

create table persona (
	id int unsigned,
	nome varchar(50) not null,
	gender tinyint unsigned not null,
	immagine varchar(100) unique,
	data_nascita date,
	data_morte date,
	primary key (id),
	foreign key (gender) references gender(id)
);

create table ruolo (
	id smallint unsigned,
	nome varchar(50) unique not null,
	primary key (id)
);





create table collezione (
	id int unsigned,
	nome varchar(200) not null,
	descrizione varchar(10000),
	locandina varchar(100) unique,
	primary key (id)
);

create table film (
	id bigint unsigned,
	nome varchar(200) not null,
	nome_originale varchar(200),
	durata smallint unsigned,
	locandina varchar(100) unique,
	descrizione varchar(10000),
	stato tinyint unsigned not null,
	data_rilascio date,
	budget int unsigned,
	incassi int unsigned,
	collezione int unsigned,
	voto float unsigned,
	primary key (id),
	foreign key (collezione) references collezione(id) on delete set null,
	foreign key (stato) references stato(id)
);





create table film_genere (
	film bigint unsigned,
	genere smallint unsigned,
	primary key (film, genere),
	foreign key (film) references film(id) on delete cascade,
	foreign key (genere) references genere(id)
);

create table film_paese (
	film bigint unsigned,
	paese char(2),
	primary key (film, paese),
	foreign key (film) references film(id) on delete cascade,
	foreign key (paese) references paese(iso_3166_1)
);

create table crew (
	film bigint unsigned,
	persona int unsigned,
	ruolo smallint unsigned,
	primary key (film, persona, ruolo),
	foreign key (film) references film(id) on delete cascade,
	foreign key (persona) references persona(id) on delete cascade,
	foreign key (ruolo) references ruolo(id)
);





create table utente (
	id bigint unsigned,
	username varchar(30) unique not null,
	mail varchar(100) unique,
	nome varchar(50),
	gender tinyint unsigned not null,
	data_nascita date,
	password varchar(255) not null,
	is_admin tinyint unsigned not null default 0,
	primary key (id),
	foreign key (gender) references gender(id)
);

create table valutazione (
	utente bigint unsigned,
	film bigint unsigned,
	voto smallint unsigned not null,
	testo varchar(1000),
	primary key (utente, film),
	foreign key (utente) references utente(id) on delete cascade,
	foreign key (film) references film(id) on delete cascade
);

create table lista (
	id bigint unsigned,
	utente bigint unsigned,
	nome varchar(50),
	primary key (id),
	foreign key (utente) references utente(id) on delete cascade,
	constraint no_stesso_nome_lista unique (utente, nome)
);

create table lista_film (
	id bigint unsigned,
	lista bigint unsigned,
	film bigint unsigned,
	primary key (id),
	foreign key (lista) references lista(id) on delete cascade,
	foreign key (film) references film(id) on delete cascade,
	constraint no_stesso_film_lista unique (lista, film)
);