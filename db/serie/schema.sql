-- create table network (
-- 	id serial,
-- 	nome varchar(60) not null,
-- 	descrizione varchar(1000),
-- 	data_fondazione date,
-- 	paese_fondazione varchar(2),
-- 	primary key (id),
-- 	foreign key (paese_fondazione) references paese(iso_3166-1)
-- );





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
