drop table if exists i_film;
drop table if exists i_collezione;
drop table if exists i_persona;
drop table if exists i_genere;

drop table if exists lista_film;
drop table if exists lista_collezione;
drop table if exists lista;
drop table if exists valutazione;
drop table if exists utente;
drop table if exists film_partecipazione;
drop table if exists film_paese;
drop table if exists film_genere;
drop table if exists film;
drop table if exists collezione;
drop table if exists ruolo;
drop table if exists persona;
drop table if exists gender;
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

create table gender (
	id int unsigned,
	nome varchar(50) not null,
	primary key (id)
);

create table persona (
	id bigint unsigned,
	nome varchar(50) not null,
	gender int unsigned,
	immagine varchar(100),
	data_nascita date,
	data_morte date,
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
	locandina varchar(100),
	primary key (id)
);

create table film (
	id bigint unsigned,
	nome varchar(200) not null,
	nome_originale varchar(200) not null,
	durata smallint unsigned,
	locandina varchar(100),
	descrizione varchar(10000),
	data_rilascio date,
	stato varchar(30) not null,
	budget int unsigned,
	incassi int unsigned,
	collezione int unsigned,
	voto float unsigned,
	primary key (id),
	foreign key (collezione) references collezione(id) on delete set null
);





create table film_genere (
	film bigint unsigned,
	genere int unsigned,
	primary key (film, genere),
	foreign key (film) references film(id) on delete cascade,
	foreign key (genere) references genere(id) on delete cascade
);

create table film_paese (
	film bigint unsigned,
	paese char(2),
	primary key (film, paese),
	foreign key (film) references film(id) on delete cascade,
	foreign key (paese) references paese(iso_3166_1)
);

create table film_partecipazione (
	id bigint unsigned,
	film bigint unsigned,
	persona bigint unsigned,
	ruolo int unsigned,
	interpreta varchar(150),
	primary key (id),
	unique (film, persona, ruolo),
	foreign key (film) references film(id) on delete cascade,
	foreign key (persona) references persona(id) on delete cascade,
	foreign key (ruolo) references ruolo(id) on delete cascade
);





create table utente (
	id bigint unsigned,
	username varchar(30) unique not null,
	mail varchar(100) unique,
	nome varchar(50),
	gender int unsigned,
	data_nascita date,
	password varchar(255) not null,
	is_admin boolean not null default 0,
	primary key (id),
	foreign key (gender) references gender(id)
);

create table valutazione (
	utente bigint unsigned,
	film bigint unsigned,
	valore smallint unsigned not null,
	testo varchar(10000),
	primary key (utente, film),
	foreign key (utente) references utente(id) on delete cascade,
	foreign key (film) references film(id) on delete cascade
);

create table lista (
	id bigint unsigned,
	utente bigint unsigned,
	nome varchar(50),
	primary key (id),
	foreign key (utente) references utente(id) on delete cascade
);

create table lista_collezione (
	lista bigint unsigned,
	collezione int unsigned,
	primary key (lista, collezione),
	foreign key (lista) references lista(id) on delete cascade,
	foreign key (collezione) references collezione(id) on delete cascade
);

create table lista_film (
	lista bigint unsigned,
	film bigint unsigned,
	primary key (lista, film),
	foreign key (lista) references lista(id) on delete cascade,
	foreign key (film) references film(id) on delete cascade
);





create table i_genere (
	id int unsigned,
	tmdb_id int unsigned,
	primary key (id),
	foreign key (id) references genere(id) on delete cascade
);

create table i_persona (
	id bigint unsigned,
	tmdb_id bigint unsigned,
	primary key (id),
	foreign key (id) references persona(id) on delete cascade
);

create table i_collezione (
	id int unsigned,
	tmdb_id int unsigned,
	primary key (id),
	foreign key (id) references collezione(id) on delete cascade
);

create table i_film (
	id bigint unsigned,
	tmdb_id bigint unsigned,
	primary key (id),
	foreign key (id) references film(id) on delete cascade
);
