import os
import json
import tmdbsimple as tmdb

tmdb_api=os.environ.get("TMDB_API")
if tmdb_api==None:
	print("set env: TMDB_API")
	exit()

tmdb.API_KEY=tmdb_api

movies=[]
tv=[]

for i in range(1,2):
	movies.extend([{"id": j.get("id")} for j in tmdb.Discover().movie(sort_by="vote_count.desc", vote_average_gte=8, page=i).get("results")])

# for i in [movies[0]]:
for i in movies:
	# ==== in tabella film

	translations=tmdb.Movies(i.get("id")).translations()
	for tr in translations.get("translations"):
		if tr["iso_3166_1"]=="IT":
			i["titolo"]=tr.get("data").get("title")
			i["descrizione"]=tr.get("data").get("overview")
			i["runtime"]=tr.get("data").get("runtime")

	info=tmdb.Movies(i.get("id")).info()
	if i["titolo"]=="": i["titolo"]=info.get("title")
	if i["descrizione"]=="": i["descrizione"]=info.get("overview")
	if i["runtime"]=="": i["runtime"]=info.get("runtime")
	i["data_rilascio"]=info.get("release_date")
	i["budget"]=info.get("budget")
	i["guadagno"]=info.get("revenue")
	i["stato"]=info.get("status")
	i["copertina"]=info.get("poster_path")
	i["is_episodio"]=False

	# ==== in altre tabelle (dizionari qui)
	i["film_genere"]=info.get("genres")
	i["film_compagnia"]=info.get("production_companies")
	i["film_paese"]=info.get("production_countries")

	keywords=tmdb.Movies(i.get("id")).keywords()
	i["film_keyword"]=keywords.get("keywords")

	credits=tmdb.Movies(i.get("id")).credits()
	i["film_casting"]=credits.get("cast")
	i["film_casting"]+=credits.get("crew")

f=open("dump.json", "w")
f.write(json.dumps(movies, indent="\t"))
f.close()
