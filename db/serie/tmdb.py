network=[]

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


def main():
	get_serie()
