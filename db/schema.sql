create table genere (
	id serial,
	nome varchar(50),
	primary key (id)
);

create table paese (
	iso_3166-1 varchar(2),
	nome varchar(30),
	primary key (iso_3166-1)
);

create table compagnia (
	id serial,
	nome varchar(60) not null,
	descrizione varchar(1000),
	data_fondazione date,
	paese_fondazione varchar(2),
	primary key (id),
	foreign key (paese_fondazione) references paese(iso_3166-1)
);

-- create table network (
-- 	id serial,
-- 	nome varchar(60) not null,
-- 	descrizione varchar(1000),
-- 	data_fondazione date,
-- 	paese_fondazione varchar(2),
-- 	primary key (id),
-- 	foreign key (paese_fondazione) references paese(iso_3166-1)
-- );

create table keyword (
	id serial,
	nome varchar(50),
	primary key (id)
);

create table persona (
	id serial,
	nome varchar(30) not null,
	descrizione varchar(1000),
	data_nascita date,
	paese_nascita varchar(2),
	genere varchar(30),
	primary key (id),
	foreign key (paese_nascita) references paese(iso_3166-1)
);

-- create table ruolo (
-- 	id serial,
-- 	nome varchar(30) unique not null,
-- 	primary key (id)
-- );

-- create table lingua (
-- 	iso_639-1 varchar(2),
-- 	nome varchar(30),
-- 	primary key (iso_639-1)
-- );





create table collezione (
	id serial,
	titolo varchar(200) not null,
	descrizione varchar(10000),
	copertina varchar(100),
	primary key (id)
);

create table film (
	id serial,
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
	primary key (id)
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
	primary key (film, paese)
	foreign key (film) references film(id),
	foreign key (paese) references paese(iso_3166-1)
);

create table film_compagnia (
	primary key (film, compagnia)
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
	ruolo int not null,
	interpretazione varchar(30),
	primary key (film, persona, ruolo),
	foreign key (film) references film(id),
	foreign key (persona) references persona(id),
	foreign key (ruolo) references ruolo(id)
);





create table serie (
	id serial,
	titolo varchar(200) not null,
	descrizione varchar(10000),
	copertina varchar(100),
	n_stagioni int,
	n_episodi int,
	primary key (id)
);

create table stagione (
	id serial,
	serie int,
	titolo varchar(200) not null,
	descrizione varchar(10000),
	numero int,
	copertina varchar(100),
	primary key (id),
	foreign key (serie) references serie(id)
);

create table episodio (
	id serial,
	serie int,
	stagione int,
	titolo varchar(200) not null,
	descrizione varchar(10000),
	numero int,
	immagine varchar(100),
	primary key (id),
	foreign key (serie) references serie(id),
	foreign key (stagione) references stagione(id)
);





create table serie_genere ()

-- create table serie_paese ()

create table serie_compagnia ()

-- create table serie_network ()

create table serie_keyword ()

create table serie_persona ()

-- create table stagione_network ()

-- create table episodio_genere ()

create table episodio_keyword ()

create table episodio_persona ()










create table utente (
	id serial,
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

create table valutazione_film (
	utente int,
	film int,
	valore smallint not null,
	primary key (utenten, film),
	foreign key (utente) references utente(id)
	foreign key (film) references film(id),
);

create table valutazione_serie (
	utente int,
	serie int,
	valore smallint not null,
	primary key (utente, serie),
	foreign key (utente) references utente(id)
	foreign key (serie) references serie(id),
);

create table valutazione_episodio (
	utente int,
	episodio int,
	valore smallint not null,
	primary key (utente, episodio),
	foreign key (utente) references utente(id)
	foreign key (episodio) references episodio(id),
);

create table lista (
	id serial,
	utente int,
	nome varchar(50),
	primary key (serial)
	foreign key (utente) references utente(id)
)

create table lista_collezione (
	lista int,
	collezione int,
	primary key (lista, collezione),
	foreign key (lista) references lista(id),
	foreign key (collezione) references collezione(id),
);

create table lista_film (
	lista int,
	film int,
	primary key (lista, film),
	foreign key (lista) references lista(id),
	foreign key (film) references film(id)
);

create table lista_serie (
	lista int,
	serie int,
	primary key (lista, serie),
	foreign key (lista) references lista(id),
	foreign key (serie) references serie(id)
);

create table lista_stagione (
	lista int,
	stagione int,
	primary key (lista, stagione),
	foreign key (lista) references lista(id),
	foreign key (stagione) references stagione(id)
);

create table lista_episodio (
	lista int,
	episodio int,
	primary key (lista, episodio),
	foreign key (lista) references lista(id),
	foreign key (episodio) references episodio(id)
);
