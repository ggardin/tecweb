import os
import json
import tmdbsimple as tmdb
from deep_translator import GoogleTranslator
import pandas as pd
from sqlalchemy import create_engine

genere=[]
paese=[]
compagnia=[]
network=[]
keyword=[]
persona=[]
# ruolo=[]
# lingua=[]

collezione=[]
film=[]

film_genere=[]
film_paese=[]
film_compagnia=[]
film_keyword=[]
film_persona=[]

serie=[]
stagione=[]
episodio=[]

serie_genere=[]
# serie_paese=[]
serie_compagnia=[]
serie_network=[]
serie_keyword=[]
serie_persona=[]
# stagione_network=[]
# episodio_genere=[]
episodio_keyword=[]
episodio_persona=[]



def start():
	tmdb_api=os.environ.get("TMDB_API")
	if tmdb_api==None:
		return False
	tmdb.API_KEY=tmdb_api
	return True

def get_film():
	for i in range(1,2):
		film.extend([{"id": j["id"]} for j in tmdb.Discover().movie(sort_by="vote_count.desc", vote_average_gte=8, page=i)["results"]])

	# for f in [film[0]]:
	for f in film:
		# === in film
		info=tmdb.Movies(f["id"]).info(language="it")

		keys=[["titolo", "title"], ["titolo_originale", "original_title"], ["lingua_originale", "original_language"], ["descrizione", "overview"], ["runtime", "runtime"], ["data_rilascio", "release_date"], ["budget", "budget"], ["incassi", "revenue"], ["stato", "status"], ["copertina", "poster_path"], ["collezione", "belongs_to_collection"]]
		for i in range(len(keys)):
			f[keys[i][0]]=info[keys[i][1]]

		# ==== in altre tabelle
		if f["collezione"]!=None:
			f["collezione"]=f["collezione"]["id"]
			if (f["collezione"] not in [c["id"] for c in collezione]):
				keys=[["id", "id"], ["nome", "name"], ["descrizione", "overview"], ["percorso", "poster_path"]]
				c=tmdb.Collections(f["collezione"]).info(language="it")
				collezione.extend([{keys[i][0]: c[keys[i][1]] for i in range(len(keys))}])
				film.extend([{"id": i["id"]} for i in c["parts"]])

		if info["genres"]!=None:
			for x in info["genres"]:
				x["id_film"]=f["id"]
				keys=[["id", "id"], ["id_film", "id_film"]]
				film_genere.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
				keys=[["id", "id"], ["nome", "name"]]
				genere.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		if info["production_countries"]!=None:
			for x in info["production_countries"]:
				x["id_film"]=f["id"]
				keys=[["iso_3166-1", "iso_3166_1"], ["id_film", "id_film"]]
				film_paese.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
				keys=[["iso_3166-1", "iso_3166_1"], ["nome", "name"]]
				paese.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		if info["production_companies"]!=None:
			for x in info["production_companies"]:
				x["id_film"]=f["id"]
				keys=[["id", "id"], ["id_film", "id_film"]]
				film_compagnia.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
				keys=[["id", "id"], ["copertina", "logo_path"], ["nome", "name"], ["paese_origine", "origin_country"]]
				compagnia.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		kws=tmdb.Movies(f["id"]).keywords()
		if kws!=None:
			for x in kws["keywords"]:
				x["id_film"]=f["id"]
				keys=[["id", "id"], ["id_film", "id_film"]]
				film_keyword.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
				keys=[["id", "id"], ["nome", "name"]]
				keyword.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		credits=tmdb.Movies(f["id"]).credits()
		if credits["cast"]!=None:
			for x in credits["cast"]:
				x["id_film"]=f["id"]
				keys=[["id", "id"], ["interpreta", "character"], ["id_film", "id_film"]]
				cast=[{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}]
				for i in cast:
					i["ruolo"]="Attore"
				film_persona.extend(cast)
				keys=[["id", "id"], ["nome", "name"], ["immagine", "profile_path"], ["genere", "gender"]]
				persona.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
		if credits["crew"]!=None:
			for x in credits["crew"]:
				# jobs=[["Direttore", "Director"], ["Produttore", "Producer"], ["Scrittore", "Writer"], ["Compositore", "Original Music Composer"]]
				# if (x["job"] in [jobs[i][1] for i in range(len(jobs))]):
				jobs={"Director", "Producer", "Writer", "Original Music Composer"}
				if (x["job"] in jobs):
					x["id_film"]=f["id"]
					keys=[["id", "id"], ["ruolo", "job"], ["id_film", "id_film"]]
					crew=[{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}]
					for i in crew:
						i["interpreta"]=""
					film_persona.extend(crew)
					keys=[["id", "id"], ["nome", "name"], ["immagine", "profile_path"], ["genere", "gender"]]
					persona.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

def get_serie():
	for i in range(1,2):
		serie.extend([{"id": j["id"]} for j in tmdb.Discover().tv(sort_by="vote_count.desc", vote_average_gte=8, page=i)["results"]])

	for s in serie:
		info=tmdb.TV(s["id"]).info(language="it")

		keys=[["nome", "name"], ["in_produzione", "in_production"], ["ultima_trasmissione", "last_air_date"], ["numero_episodi", "number_of_episodes"], ["numero_stagioni", "number_of_seasons"], ["lingua_originale", "original_language"], ["nome_originale", "original_name"], ["descrizione", "overview"], ["copertina", "poster_path"], ["stato", "status"]]
		for i in range(len(keys)):
			s[keys[i][0]]=info[keys[i][1]]

		# ==== in altre tabelle
		if info["seasons"]!=None:
			for x in info["seasons"]:
				stagione.extend(x)

				# x["id_serie"]=s["id"]
				# keys=[["data_trasmissione", "air_date"], ["numero_episodi", "episode_count"], ["id", "id"], ["nome", "name"], ["descrizione", "overview"], ["copertina", "poster_path"], ["numero_stagione", "season_number"], ["id_serie", "id_serie"]]
				# stagione.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

				# dict={}
				# dict["id_stagione"]=x["id"]
				# dict["id_serie"]=x["id_serie"]
				# dict
				# episodio.extend([{}])

				# ep=tmdb.TV(s["id"]).info(language="it")
				# episodio.extend()

		if info["genres"]!=None:
			for x in info["genres"]:
				x["id_serie"]=s["id"]
				keys=[["id", "id"], ["id_serie", "id_serie"]]
				serie_genere.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
				keys=[["id", "id"], ["nome", "name"]]
				genere.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		# if info["origin_country"]!=None: ????????????????

		# if info["production_countries"]!=None:
		# 	for x in info["production_countries"]:
		# 		x["id_serie"]=s["id"]
		# 		keys=[["iso_3166-1", "iso_3166_1"], ["id_serie", "id_serie"]]
		# 		serie_paese.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
		# 		keys=[["iso_3166-1", "iso_3166_1"], ["nome", "name"]]
		# 		paese.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		# if info["production_companies"]!=None:
		# 	for x in info["production_companies"]:
		# 		x["id_serie"]=s["id"]
		# 		keys=[["id", "id"], ["id_serie", "id_serie"]]
		# 		serie_network.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
		# 		keys=[["id", "id"], ["nome", "name"], ["immagine", "logo_path"], ["paese_origine", "origin_country"]]
		# 		network.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		if info["networks"]!=None:
			for x in info["networks"]:
				x["id_serie"]=s["id"]
				keys=[["id", "id"], ["id_serie", "id_serie"]]
				serie_network.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
				keys=[["id", "id"], ["nome", "name"], ["immagine", "logo_path"], ["paese_origine", "origin_country"]]
				network.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		kws=tmdb.TV(s["id"]).keywords()
		if kws!=None:
			for x in kws["results"]:
				x["id_serie"]=s["id"]
				keys=[["id", "id"], ["id_serie", "id_serie"]]
				serie_keyword.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
				keys=[["id", "id"], ["nome", "name"]]
				keyword.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

		credits=tmdb.TV(s["id"]).credits()
		if credits["cast"]!=None:
			for x in credits["cast"]:
				x["id_serie"]=s["id"]
				keys=[["id", "id"], ["interpreta", "character"], ["id_serie", "id_serie"]]
				cast=[{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}]
				for i in cast:
					i["ruolo"]="Attore"
				serie_persona.extend(cast)
				keys=[["id", "id"], ["nome", "name"], ["immagine", "profile_path"], ["genere", "gender"]]
				persona.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])
		# if credits["crew"]!=None:
		# 	for x in credits["crew"]:
		# 		jobs={"Director", "Producer", "Writer", "Original Music Composer"}
		# 		if (x["job"] in jobs):
		# 			x["id_serie"]=s["id"]
		# 			keys=[["id", "id"], ["ruolo", "job"], ["id_serie", "id_serie"]]
		# 			crew=[{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}]
		# 			for i in crew:
		# 				i["interpreta"]=""
		# 			serie_persona.extend(crew)
		# 			keys=[["id", "id"], ["nome", "name"], ["immagine", "profile_path"], ["genere", "gender"]]
		# 			persona.extend([{keys[i][0]: x[keys[i][1]] for i in range(len(keys))}])

def set_unique_all():
	global genere
	genere=pd.DataFrame(genere).drop_duplicates().to_dict("records")
	global paese
	paese=pd.DataFrame(paese).drop_duplicates().to_dict("records")
	global compagnia
	compagnia=pd.DataFrame(compagnia).drop_duplicates().to_dict("records")
	global network
	network=pd.DataFrame(network).drop_duplicates().to_dict("records")
	global keyword
	keyword=pd.DataFrame(keyword).drop_duplicates().to_dict("records")
	global persona
	persona=pd.DataFrame(persona).drop_duplicates().to_dict("records")
	# ruolo=[]
	# lingua=[]

def translate_keywords():
	global keyword
	parole=[i["nome"] for i in keyword]
	print(parole)
	tr=GoogleTranslator("en", "it").translate_batch(parole)
	for i in range(len(keyword)):
		keyword[i]["nome"]=tr[i]

def write(nome, var):
	if not os.path.exists("dump"):
		os.mkdir("dump")
	file=open("dump/"+nome+".json", "w")
	file.write(json.dumps(var, indent="\t"))
	file.close()

def write_all():
	write("genere", genere)
	write("paese", paese)
	write("compagnia", compagnia)
	write("network", network)
	write("keyword", keyword)
	write("persona", persona)
	# write("ruolo", ruolo)
	# write("lingua", lingua)

	write("collezione", collezione)
	write("film", film)

	write("film_genere", film_genere)
	write("film_paese", film_paese)
	write("film_compagnia", film_compagnia)
	write("film_keyword", film_keyword)
	write("film_persona", film_persona)

	write("serie", serie)
	write("stagione", stagione)
	write("episodio", episodio)

	write("serie_genere", serie_genere)
	# write("serie_paese", serie_paese)
	write("serie_compagnia", serie_compagnia)
	write("serie_network", serie_network)
	write("serie_keyword", serie_keyword)
	write("serie_persona", serie_persona)
	# write("stagione_network", stagione_network)
	# write("episodio_genere", episodio_genere)
	write("episodio_keyword", episodio_keyword)
	write("episodio_persona", episodio_persona)

def db():
	engine = create_engine("postgresql://postgres:postgres@localhost/db")

def main():
	if not start(): print("set env: TMDB_API"); return False

	get_film()

	get_serie()

	set_unique_all()

	# translate_keywords()

	write_all()

	# db()

if __name__ == "__main__":
	main()
