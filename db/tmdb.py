import os
import json
import datetime
import time
import tmdbsimple as tmdb
from deep_translator import GoogleTranslator
from sqlalchemy import create_engine
import pandas as pd

genere=[]
i_genere=[]
stato=[]
paese=[]
gender=[]
ruolo=[]
persona=[]
i_persona=[]
compagnia=[]
i_compagnia=[]
keyword=[]
i_keyword=[]

collezione=[]
i_collezione=[]
film=[]
i_film=[]

film_genere=[]
film_paese=[]
film_compagnia=[]
film_keyword=[]
cast=[]
crew=[]



def start():
	global tmdb_api
	global db_host
	global db_name
	global db_user
	global db_pass
	tmdb_api=os.environ.get("TMDB_API")
	db_host=os.environ.get("DB_HOST")
	db_name=os.environ.get("DB_NAME")
	db_user=os.environ.get("DB_USER")
	db_pass=os.environ.get("DB_PASS")
	if not (tmdb_api and db_host and db_name and db_user and db_pass):
		return False
	tmdb.API_KEY=tmdb_api
	return True

def get_film():
	f=[]
	for i in range(1,8):
		f.extend([k["id"] for k in tmdb.Discover().movie(sort_by="vote_count.desc", vote_average_gte=8, page=i)["results"]])
	for k in range(len(f)):
		i_film.append({"id": k, "tmdb_id": f[k]})
		film.append({"id": k})

	for f in film:
		i_f=i_film[[k["id"] for k in i_film].index(f["id"])]["tmdb_id"]
		print(str(datetime.datetime.now())+" : "+str(i_f)+"\n")
		# === in film
		info=tmdb.Movies(i_f).info(language="it")
		info["voto"]=None
		if info["release_date"]=="": info["release_date"]=None
		if info["runtime"]==0: info["runtime"]=None
		if info["overview"]=="": info["overview"]=None
		if info["budget"]==0: info["budget"]=None
		if info["revenue"]==0: info["revenue"]=None
		if info["poster_path"]: info["poster_path"]=info["poster_path"][1:]
		else: info["poster_path"]=None
		orig=info["original_title"]
		info["original_title"]="["+info["original_language"]+"]"+info["original_title"]+"[/"+info["original_language"]+"]"
		if info["title"]==orig: info["title"]=info["original_title"]
		status=[["Released", 5], ["In Production", 1], ["Post Production", 3]]
		info["status"]=status[[status[i][0] for i in range(len(status))].index(info["status"])][1]
		keys=[["nome", "title"], ["nome_originale", "original_title"], ["durata", "runtime"], ["locandina", "poster_path"], ["descrizione", "overview"], ["stato", "status"], ["data_rilascio", "release_date"], ["budget", "budget"], ["incassi", "revenue"], ["collezione", "belongs_to_collection"], ["voto", "voto"]]
		for i in range(len(keys)):
			f[keys[i][0]]=info[keys[i][1]]

		# ==== in altre tabelle
		if f["collezione"]!=None:
			f["collezione"]=f["collezione"]["id"]
			i=len(i_collezione)
			l=i
			try:
				i=[k["tmdb_id"] for k in i_collezione].index(f["collezione"])
			except:
				i_collezione.append({"id": i, "tmdb_id": f["collezione"]})
			if i==l:
				c=tmdb.Collections(f["collezione"]).info(language="it")
				c["id"]=i
				c["name"] = c["name"].replace(" - Collezione", "")
				if c["overview"]=="": c["overview"]=None
				if c["poster_path"]: c["poster_path"]=c["poster_path"][1:]
				else: c["poster_path"]=None
				keys=[["id", "id"], ["nome", "name"], ["descrizione", "overview"], ["locandina", "poster_path"]]
				collezione.append({keys[k][0]: c[keys[k][1]] for k in range(len(keys))})
				for k in range(len(c["parts"])):
					if c["parts"][k]["id"] not in [h["tmdb_id"] for h in i_film]:
						a=len(i_film)
						i_film.append({"id": a, "tmdb_id": c["parts"][k]["id"]})
						film.append({"id": a})
			f["collezione"]=i

		if info["genres"]!=None:
			for x in info["genres"]:
				i=len(i_genere)
				l=i
				try:
					i=[k["tmdb_id"] for k in i_genere].index(x["id"])
				except:
					i_genere.append({"id": i, "tmdb_id": x["id"]})
				x["id"]=i
				x["id_film"]=f["id"]
				keys=[["film", "id_film"], ["genere", "id"]]
				film_genere.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})
				if i==l:
					keys=[["id", "id"], ["nome", "name"]]
					genere.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})

		if info["production_countries"]!=None:
			for x in info["production_countries"]:
				x["id_film"]=f["id"]
				keys=[["film", "id_film"], ["paese", "iso_3166_1"]]
				film_paese.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})

		# if info["production_companies"]!=None:
		# 	for x in info["production_companies"]:
		# 		i=len(i_compagnia)
		# 		l=i
		# 		try:
		# 			i=[k["tmdb_id"] for k in i_compagnia].index(x["id"])
		# 		except:
		# 			i_compagnia.append({"id": i, "tmdb_id": x["id"]})
		# 		x["id"]=i
		# 		x["id_film"]=f["id"]
		# 		if x["origin_country"]=="": x["origin_country"]=None
		# 		keys=[["film", "id_film"], ["compagnia", "id"]]
		# 		film_compagnia.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})
		# 		if i==l:
		# 			x["name"]="[en]"+x["name"]+"[/en]"
		# 			if x["logo_path"]: x["logo_path"]=x["logo_path"][1:]
		# 			keys=[["id", "id"], ["nome", "name"], ["logo", "logo_path"], ["paese_fondazione", "origin_country"]]
		# 			compagnia.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})

		# kws=tmdb.Movies(i_f).keywords()
		# if kws!=None:
		# 	for x in kws["keywords"]:
		# 		i=len(i_keyword)
		# 		l=i
		# 		try:
		# 			i=[k["tmdb_id"] for k in i_keyword].index(x["id"])
		# 		except:
		# 			i_keyword.append({"id": i, "tmdb_id": x["id"]})
		# 		x["id"]=i
		# 		x["id_film"]=f["id"]
		# 		keys=[["film", "id_film"], ["keyword", "id"]]
		# 		film_keyword.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})
		# 		if i==l:
		# 			keys=[["id", "id"], ["nome", "name"]]
		# 			keyword.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})

		credits=tmdb.Movies(i_f).credits()
		# if credits["cast"]!=None:
		# 	for i in range(len(credits["cast"])):
		# 		if i<10:
		# 			x=credits["cast"][i]
		# 			i=len(i_persona)
		# 			l=i
		# 			try:
		# 				i=[k["tmdb_id"] for k in i_persona].index(x["id"])
		# 			except:
		# 				i_persona.append({"id": i, "tmdb_id": x["id"]})
		# 			x["id"]=i
		# 			x["id_film"]=f["id"]
		# 			x["index_id"]=len(cast)
		# 			x["character"]="[en]"+x["character"]+"[/en]"
		# 			keys=[["id", "index_id"], ["film", "id_film"], ["persona", "id"], ["interpreta", "character"]]
		# 			cast.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})
		# 			if i==l:
		# 				p=tmdb.People(i_persona[i]["tmdb_id"]).info()
		# 				p["id"]=i
		# 				p["name"]="[en]"+p["name"]+"[/en]"
		# 				if p["profile_path"]: p["profile_path"]=p["profile_path"][1:]
		# 				if p["gender"]=="": p["gender"]=None
		# 				else: p["profile_path"]=None
		# 				if p["birthday"]=="": p["birthday"]=None
		# 				if p["deathday"]=="": p["deathday"]=None
		# 				keys=[["id", "id"], ["nome", "name"], ["gender", "gender"], ["immagine", "profile_path"], ["data_nascita", "birthday"], ["data_morte", "deathday"]]
		# 				persona.append({keys[k][0]: p[keys[k][1]] for k in range(len(keys))})
		if credits ["crew"]!=None:
			for x in credits["crew"]:
				jobs=[[0, "Director"], [1, "Writer"], [2, "Producer"], [3, "Original Music Composer"]]
				if (x["job"] in [jobs[i][1] for i in range(len(jobs))]):
					i=len(i_persona)
					l=i
					try:
						i=[k["tmdb_id"] for k in i_persona].index(x["id"])
					except:
						i_persona.append({"id": i, "tmdb_id": x["id"]})
					x["id"]=i
					x["id_film"]=f["id"]
					x["ruolo"]=jobs[[jobs[i][1] for i in range(len(jobs))].index(x["job"])][0]
					x["interpreta"]=""
					keys=[["film", "id_film"], ["persona", "id"], ["ruolo", "ruolo"]]
					crew.append({keys[i][0]: x[keys[i][1]] for i in range(len(keys))})
					if i==l:
						p=tmdb.People(i_persona[i]["tmdb_id"]).info()
						p["id"]=i
						p["name"]="[en]"+p["name"]+"[/en]"
						if p["profile_path"]: p["profile_path"]=p["profile_path"][1:]
						else: p["profile_path"]=None
						if p["gender"]=="": p["gender"]=None
						if p["birthday"]=="": p["birthday"]=None
						if p["deathday"]=="": p["deathday"]=None
						keys=[["id", "id"], ["nome", "name"], ["gender", "gender"], ["immagine", "profile_path"], ["data_nascita", "birthday"], ["data_morte", "deathday"]]
						persona.append({keys[i][0]: p[keys[i][1]] for i in range(len(keys))})

def set_stato():
	global stato
	stato=[
		{"id": 0, "nome": "Annunciato"},
		{"id": 1, "nome": "Pre Produzione"},
		{"id": 2, "nome": "Riprese in corso"},
		{"id": 3, "nome": "Post Produzione"},
		{"id": 4, "nome": "Completato"},
		{"id": 5, "nome": "Rilasciato"}
		]

def set_paese():
	global paese
	paese = (pd.read_json("paese.json")).to_dict(orient='records')

def set_gender():
	global gender
	gender=[
		{"id": 0, "nome": "non specificato"},
		{"id": 1, "nome": "femmina"},
		{"id": 2, "nome": "maschio"},
		{"id": 3, "nome": "non binario"}
		]

def set_ruolo():
	global ruolo
	ruolo=[
		{"id": 0, "nome": "Regista"},
		{"id": 1, "nome": "Sceneggiatore"},
		{"id": 2, "nome": "Produttore"},
		{"id": 3, "nome": "Compositore"}
		]

def db_del(var, nome, engine):
	engine.execute("delete from "+nome+";")

def db_insert(var, nome, engine):
	# pd.DataFrame(var).to_csv("dump_text/"+nome+".csv", index=False)
	if not os.path.exists("dump_text"):
		os.makedirs("dump_text")
	pd.DataFrame(var).to_json("dump_text/"+nome+".json", orient="records", indent=2)
	pd.DataFrame(var).to_sql(nome, con=engine, if_exists="append", index=False, chunksize=10000)

def db_insert_all():
	engine=create_engine("mariadb+pymysql://"+db_user+":"+db_pass+"@"+db_host+"/"+db_name)

	db_del(crew, "crew", engine)
	# db_del(cast, "cast", engine)
	# db_del(film_keyword, "film_keyword", engine)
	# db_del(film_compagnia, "film_compagnia", engine)
	db_del(film_paese, "film_paese", engine)
	db_del(film_genere, "film_genere", engine)

	db_del(i_film, "i_film", engine)
	db_del(film, "film", engine)
	db_del(i_collezione, "i_collezione", engine)
	db_del(collezione, "collezione", engine)

	# db_del(i_keyword, "i_keyword", engine)
	# db_del(keyword, "keyword", engine)
	# db_del(i_compagnia, "i_compagnia", engine)
	# db_del(compagnia, "compagnia", engine)
	db_del(i_persona, "i_persona", engine)
	db_del(persona, "persona", engine)
	db_del(ruolo, "ruolo", engine)
	db_del(gender, "gender", engine)
	db_del(paese, "paese", engine)
	db_del(stato, "stato", engine)
	db_del(i_genere, "i_genere", engine)
	db_del(genere, "genere", engine)

	db_insert(genere, "genere", engine)
	db_insert(i_genere, "i_genere", engine)
	db_insert(stato, "stato", engine)
	db_insert(paese, "paese", engine)
	db_insert(gender, "gender", engine)
	db_insert(ruolo, "ruolo", engine)
	db_insert(persona, "persona", engine)
	db_insert(i_persona, "i_persona", engine)
	# db_insert(compagnia, "compagnia", engine)
	# db_insert(i_compagnia, "i_compagnia", engine)
	# db_insert(keyword, "keyword", engine)
	# db_insert(i_keyword, "i_keyword", engine)

	db_insert(collezione, "collezione", engine)
	db_insert(i_collezione, "i_collezione", engine)
	db_insert(film, "film", engine)
	db_insert(i_film, "i_film", engine)

	db_insert(film_genere, "film_genere", engine)
	db_insert(film_paese, "film_paese", engine)
	# db_insert(film_compagnia, "film_compagnia", engine)
	# db_insert(film_keyword, "film_keyword", engine)
	# db_insert(cast, "cast", engine)
	db_insert(crew, "crew", engine)

def translate_keywords():
	global keyword
	tr=GoogleTranslator("en", "it").translate_batch([i["nome"] for i in keyword])
	for i in range(len(keyword)):
		keyword[i]["nome"]=tr[i]

def main():
	if not start(): print("set env variables"); return False

	get_film()

	set_stato()
	set_paese()
	set_gender()
	set_ruolo()

	# translate_keywords()

	db_insert_all()

if __name__ == "__main__":
	main()