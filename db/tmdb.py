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
paese=[]
compagnia=[]
i_compagnia=[]
keyword=[]
i_keyword=[]
gender=[]
persona=[]
i_persona=[]
ruolo=[]

collezione=[]
i_collezione=[]
film=[]
i_film=[]

film_genere=[]
film_paese=[]
film_compagnia=[]
film_keyword=[]
film_partecipazione=[]



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
	for i in range(1,11):
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
		keys=[["titolo", "title"], ["titolo_originale", "original_title"], ["durata", "runtime"], ["copertina", "poster_path"], ["descrizione", "overview"], ["data_rilascio", "release_date"], ["stato", "status"], ["budget", "budget"], ["incassi", "revenue"], ["collezione", "belongs_to_collection"], ["voto", "voto"]]
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
				keys=[["id", "id"], ["nome", "name"], ["descrizione", "overview"], ["copertina", "poster_path"]]
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

		if info["production_companies"]!=None:
			for x in info["production_companies"]:
				i=len(i_compagnia)
				l=i
				try:
					i=[k["tmdb_id"] for k in i_compagnia].index(x["id"])
				except:
					i_compagnia.append({"id": i, "tmdb_id": x["id"]})
				x["id"]=i
				x["id_film"]=f["id"]
				if x["origin_country"]=="": x["origin_country"]=None
				keys=[["film", "id_film"], ["compagnia", "id"]]
				film_compagnia.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})
				if i==l:
					keys=[["id", "id"], ["nome", "name"], ["logo", "logo_path"], ["paese_fondazione", "origin_country"]]
					compagnia.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})

		kws=tmdb.Movies(i_f).keywords()
		if kws!=None:
			for x in kws["keywords"]:
				i=len(i_keyword)
				l=i
				try:
					i=[k["tmdb_id"] for k in i_keyword].index(x["id"])
				except:
					i_keyword.append({"id": i, "tmdb_id": x["id"]})
				x["id"]=i
				x["id_film"]=f["id"]
				keys=[["film", "id_film"], ["keyword", "id"]]
				film_keyword.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})
				if i==l:
					keys=[["id", "id"], ["nome", "name"]]
					keyword.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})

		credits=tmdb.Movies(i_f).credits()
		if credits["cast"]!=None:
			for x in credits["cast"]:
				i=len(i_persona)
				l=i
				try:
					i=[k["tmdb_id"] for k in i_persona].index(x["id"])
				except:
					i_persona.append({"id": i, "tmdb_id": x["id"]})
				x["id"]=i
				x["id_film"]=f["id"]
				x["ruolo"]=0
				keys=[["film", "id_film"], ["persona", "id"], ["ruolo", "ruolo"], ["interpreta", "character"]]
				film_partecipazione.append({keys[k][0]: x[keys[k][1]] for k in range(len(keys))})
				if i==l:
					p=tmdb.People(i_persona[i]["tmdb_id"]).info()
					p["id"]=i
					keys=[["id", "id"], ["nome", "name"], ["gender", "gender"], ["immagine", "profile_path"], ["data_nascita", "birthday"], ["data_morte", "deathday"], ["luogo_nascita", "place_of_birth"]]
					persona.append({keys[k][0]: p[keys[k][1]] for k in range(len(keys))})
		if credits ["crew"]!=None:
			for x in credits["crew"]:
				jobs=[[1, "Director"], [2, "Writer"], [3, "Producer"], [4, "Original Music Composer"]]
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
					keys=[["film", "id_film"], ["persona", "id"], ["ruolo", "ruolo"], ["interpreta", "interpreta"]]
					film_partecipazione.append({keys[i][0]: x[keys[i][1]] for i in range(len(keys))})
					if i==l:
						p=tmdb.People(i_persona[i]["tmdb_id"]).info()
						p["id"]=i
						keys=[["id", "id"], ["nome", "name"], ["gender", "gender"], ["immagine", "profile_path"], ["data_nascita", "birthday"], ["data_morte", "deathday"], ["luogo_nascita", "place_of_birth"]]
						persona.append({keys[i][0]: p[keys[i][1]] for i in range(len(keys))})

def set_ruolo():
	global ruolo
	ruolo=[
		{"id": 0, "nome": "Attore"},
		{"id": 1, "nome": "Regia"},
		{"id": 2, "nome": "Sceneggiatura"},
		{"id": 3, "nome": "Produttore"},
		{"id": 4, "nome": "Musiche"}
		]

def set_gender():
	global gender
	gender=[
		{"id": 0, "nome": "non specificato"},
		{"id": 1, "nome": "femmina"},
		{"id": 2, "nome": "maschio"},
		{"id": 3, "nome": "non binario"}
		]

def set_paese():
	global paese
	paese=[
		{"iso_3166_1": "AC", "nome": "Isola di Ascensione"},
		{"iso_3166_1": "AD", "nome": "Andorra"},
		{"iso_3166_1": "AE", "nome": "Emirati Arabi Uniti"},
		{"iso_3166_1": "AF", "nome": "Afghanistan"},
		{"iso_3166_1": "AG", "nome": "Antigua e Barbuda"},
		{"iso_3166_1": "AI", "nome": "Anguilla"},
		{"iso_3166_1": "AL", "nome": "Albania"},
		{"iso_3166_1": "AM", "nome": "Armenia"},
		{"iso_3166_1": "AO", "nome": "Angola"},
		{"iso_3166_1": "AQ", "nome": "Antartide"},
		{"iso_3166_1": "AR", "nome": "Argentina"},
		{"iso_3166_1": "AS", "nome": "Samoa Americane"},
		{"iso_3166_1": "AT", "nome": "Austria"},
		{"iso_3166_1": "AU", "nome": "Australia"},
		{"iso_3166_1": "AW", "nome": "Aruba"},
		{"iso_3166_1": "AX", "nome": "Isole Åland"},
		{"iso_3166_1": "AZ", "nome": "Azerbaigian"},
		{"iso_3166_1": "BA", "nome": "Bosnia ed Erzegovina"},
		{"iso_3166_1": "BB", "nome": "Barbados"},
		{"iso_3166_1": "BD", "nome": "Bangladesh"},
		{"iso_3166_1": "BE", "nome": "Belgio"},
		{"iso_3166_1": "BF", "nome": "Burkina Faso"},
		{"iso_3166_1": "BG", "nome": "Bulgaria"},
		{"iso_3166_1": "BH", "nome": "Bahrein"},
		{"iso_3166_1": "BI", "nome": "Burundi"},
		{"iso_3166_1": "BJ", "nome": "Benin"},
		{"iso_3166_1": "BL", "nome": "Saint-Barthélemy"},
		{"iso_3166_1": "BM", "nome": "Bermuda"},
		{"iso_3166_1": "BN", "nome": "Brunei"},
		{"iso_3166_1": "BO", "nome": "Bolivia"},
		{"iso_3166_1": "BQ", "nome": "Isole BES"},
		{"iso_3166_1": "BR", "nome": "Brasile"},
		{"iso_3166_1": "BS", "nome": "Bahamas"},
		{"iso_3166_1": "BT", "nome": "Bhutan"},
		{"iso_3166_1": "BV", "nome": "Isola Bouvet"},
		{"iso_3166_1": "BW", "nome": "Botswana"},
		{"iso_3166_1": "BY", "nome": "Bielorussia"},
		{"iso_3166_1": "BZ", "nome": "Belize"},
		{"iso_3166_1": "CA", "nome": "Canada"},
		{"iso_3166_1": "CC", "nome": "Isole Cocos (Keeling)"},
		{"iso_3166_1": "CD", "nome": "RD del Congo"},
		{"iso_3166_1": "CF", "nome": "Rep. Centrafricana"},
		{"iso_3166_1": "CG", "nome": "Rep. del Congo"},
		{"iso_3166_1": "CH", "nome": "Svizzera"},
		{"iso_3166_1": "CI", "nome": "Costa d'Avorio"},
		{"iso_3166_1": "CK", "nome": "Isole Cook"},
		{"iso_3166_1": "CL", "nome": "Cile"},
		{"iso_3166_1": "CM", "nome": "Camerun"},
		{"iso_3166_1": "CN", "nome": "Cina"},
		{"iso_3166_1": "CO", "nome": "Colombia"},
		{"iso_3166_1": "CP", "nome": "Clipperton"},
		{"iso_3166_1": "CR", "nome": "Costa Rica"},
		{"iso_3166_1": "CU", "nome": "Cuba"},
		{"iso_3166_1": "CV", "nome": "Capo Verde"},
		{"iso_3166_1": "CW", "nome": "Curaçao"},
		{"iso_3166_1": "CX", "nome": "Isola di Natale"},
		{"iso_3166_1": "CY", "nome": "Cipro"},
		{"iso_3166_1": "CZ", "nome": "Rep. Ceca"},
		{"iso_3166_1": "DE", "nome": "Germania"},
		{"iso_3166_1": "DG", "nome": "Diego Garcia"},
		{"iso_3166_1": "DJ", "nome": "Gibuti"},
		{"iso_3166_1": "DK", "nome": "Danimarca"},
		{"iso_3166_1": "DM", "nome": "Dominica"},
		{"iso_3166_1": "DO", "nome": "Rep. Dominicana"},
		{"iso_3166_1": "DZ", "nome": "Algeria"},
		{"iso_3166_1": "EA", "nome": "Ceuta e Melilla"},
		{"iso_3166_1": "EC", "nome": "Ecuador"},
		{"iso_3166_1": "EE", "nome": "Estonia"},
		{"iso_3166_1": "EG", "nome": "Egitto"},
		{"iso_3166_1": "EH", "nome": "Sahara Occidentale"},
		{"iso_3166_1": "ER", "nome": "Eritrea"},
		{"iso_3166_1": "ES", "nome": "Spagna"},
		{"iso_3166_1": "ET", "nome": "Etiopia"},
		{"iso_3166_1": "EU", "nome": "Unione europea"},
		{"iso_3166_1": "FI", "nome": "Finlandia"},
		{"iso_3166_1": "FJ", "nome": "Figi"},
		{"iso_3166_1": "FK", "nome": "Isole Falkland"},
		{"iso_3166_1": "FM", "nome": "Micronesia"},
		{"iso_3166_1": "FO", "nome": "Fær Øer"},
		{"iso_3166_1": "FR", "nome": "Francia"},
		{"iso_3166_1": "FX", "nome": "Francia metropolitana"},
		{"iso_3166_1": "GA", "nome": "Gabon"},
		{"iso_3166_1": "GB", "nome": "Regno Unito"},
		{"iso_3166_1": "GD", "nome": "Grenada"},
		{"iso_3166_1": "GE", "nome": "Georgia"},
		{"iso_3166_1": "GF", "nome": "Guyana francese"},
		{"iso_3166_1": "GG", "nome": "Guernsey"},
		{"iso_3166_1": "GH", "nome": "Ghana"},
		{"iso_3166_1": "GI", "nome": "Gibilterra"},
		{"iso_3166_1": "GL", "nome": "Groenlandia"},
		{"iso_3166_1": "GM", "nome": "Gambia"},
		{"iso_3166_1": "GN", "nome": "Guinea"},
		{"iso_3166_1": "GP", "nome": "Guadalupa"},
		{"iso_3166_1": "GQ", "nome": "Guinea Equatoriale"},
		{"iso_3166_1": "GR", "nome": "Grecia"},
		{"iso_3166_1": "GS", "nome": "Georgia del Sud e Isole Sandwich Australi"},
		{"iso_3166_1": "GT", "nome": "Guatemala"},
		{"iso_3166_1": "GU", "nome": "Guam"},
		{"iso_3166_1": "GW", "nome": "Guinea-Bissau"},
		{"iso_3166_1": "GY", "nome": "Guyana"},
		{"iso_3166_1": "HK", "nome": "Hong Kong"},
		{"iso_3166_1": "HM", "nome": "Isole Heard e McDonald"},
		{"iso_3166_1": "HN", "nome": "Honduras"},
		{"iso_3166_1": "HR", "nome": "Croazia"},
		{"iso_3166_1": "HT", "nome": "Haiti"},
		{"iso_3166_1": "HU", "nome": "Ungheria"},
		{"iso_3166_1": "IC", "nome": "Isole Canarie"},
		{"iso_3166_1": "ID", "nome": "Indonesia"},
		{"iso_3166_1": "IE", "nome": "Irlanda"},
		{"iso_3166_1": "IL", "nome": "Israele"},
		{"iso_3166_1": "IM", "nome": "Isola di Man"},
		{"iso_3166_1": "IN", "nome": "India"},
		{"iso_3166_1": "IO", "nome": "Territorio britannico dell'Oceano Indiano"},
		{"iso_3166_1": "IQ", "nome": "Iraq"},
		{"iso_3166_1": "IR", "nome": "Iran"},
		{"iso_3166_1": "IS", "nome": "Islanda"},
		{"iso_3166_1": "IT", "nome": "Italia"},
		{"iso_3166_1": "JE", "nome": "Jersey"},
		{"iso_3166_1": "JM", "nome": "Giamaica"},
		{"iso_3166_1": "JO", "nome": "Giordania"},
		{"iso_3166_1": "JP", "nome": "Giappone"},
		{"iso_3166_1": "KE", "nome": "Kenya"},
		{"iso_3166_1": "KG", "nome": "Kirghizistan"},
		{"iso_3166_1": "KH", "nome": "Cambogia"},
		{"iso_3166_1": "KI", "nome": "Kiribati"},
		{"iso_3166_1": "KM", "nome": "Comore"},
		{"iso_3166_1": "KN", "nome": "Saint Kitts e Nevis"},
		{"iso_3166_1": "KP", "nome": "Corea del Nord"},
		{"iso_3166_1": "KR", "nome": "Corea del Sud"},
		{"iso_3166_1": "KW", "nome": "Kuwait"},
		{"iso_3166_1": "KY", "nome": "Isole Cayman"},
		{"iso_3166_1": "KZ", "nome": "Kazakistan"},
		{"iso_3166_1": "LA", "nome": "Laos"},
		{"iso_3166_1": "LB", "nome": "Libano"},
		{"iso_3166_1": "LC", "nome": "Saint Lucia"},
		{"iso_3166_1": "LI", "nome": "Liechtenstein"},
		{"iso_3166_1": "LK", "nome": "Sri Lanka"},
		{"iso_3166_1": "LR", "nome": "Liberia"},
		{"iso_3166_1": "LS", "nome": "Lesotho"},
		{"iso_3166_1": "LT", "nome": "Lituania"},
		{"iso_3166_1": "LU", "nome": "Lussemburgo"},
		{"iso_3166_1": "LV", "nome": "Lettonia"},
		{"iso_3166_1": "LY", "nome": "Libia"},
		{"iso_3166_1": "MA", "nome": "Marocco"},
		{"iso_3166_1": "MC", "nome": "Monaco"},
		{"iso_3166_1": "MD", "nome": "Moldavia"},
		{"iso_3166_1": "ME", "nome": "Montenegro"},
		{"iso_3166_1": "MF", "nome": "Saint-Martin"},
		{"iso_3166_1": "MG", "nome": "Madagascar"},
		{"iso_3166_1": "MH", "nome": "Isole Marshall"},
		{"iso_3166_1": "MK", "nome": "Macedonia del Nord"},
		{"iso_3166_1": "ML", "nome": "Mali"},
		{"iso_3166_1": "MM", "nome": "Birmania"},
		{"iso_3166_1": "MN", "nome": "Mongolia"},
		{"iso_3166_1": "MO", "nome": "Macao"},
		{"iso_3166_1": "MP", "nome": "Isole Marianne Settentrionali"},
		{"iso_3166_1": "MQ", "nome": "Martinica"},
		{"iso_3166_1": "MR", "nome": "Mauritania"},
		{"iso_3166_1": "MS", "nome": "Montserrat"},
		{"iso_3166_1": "MT", "nome": "Malta"},
		{"iso_3166_1": "MU", "nome": "Mauritius"},
		{"iso_3166_1": "MV", "nome": "Maldive"},
		{"iso_3166_1": "MW", "nome": "Malawi"},
		{"iso_3166_1": "MX", "nome": "Messico"},
		{"iso_3166_1": "MY", "nome": "Malaysia"},
		{"iso_3166_1": "MZ", "nome": "Mozambico"},
		{"iso_3166_1": "NA", "nome": "Namibia"},
		{"iso_3166_1": "NC", "nome": "Nuova Caledonia"},
		{"iso_3166_1": "NE", "nome": "Niger"},
		{"iso_3166_1": "NF", "nome": "Isola Norfolk"},
		{"iso_3166_1": "NG", "nome": "Nigeria"},
		{"iso_3166_1": "NI", "nome": "Nicaragua"},
		{"iso_3166_1": "NL", "nome": "Paesi Bassi"},
		{"iso_3166_1": "NO", "nome": "Norvegia"},
		{"iso_3166_1": "NP", "nome": "Nepal"},
		{"iso_3166_1": "NR", "nome": "Nauru"},
		{"iso_3166_1": "NU", "nome": "Niue"},
		{"iso_3166_1": "NZ", "nome": "Nuova Zelanda"},
		{"iso_3166_1": "OM", "nome": "Oman"},
		{"iso_3166_1": "PA", "nome": "Panama"},
		{"iso_3166_1": "PE", "nome": "Perù"},
		{"iso_3166_1": "PF", "nome": "Polinesia francese"},
		{"iso_3166_1": "PG", "nome": "Papua Nuova Guinea"},
		{"iso_3166_1": "PH", "nome": "Filippine"},
		{"iso_3166_1": "PK", "nome": "Pakistan"},
		{"iso_3166_1": "PL", "nome": "Polonia"},
		{"iso_3166_1": "PM", "nome": "Saint-Pierre e Miquelon"},
		{"iso_3166_1": "PN", "nome": "Isole Pitcairn"},
		{"iso_3166_1": "PR", "nome": "Porto Rico"},
		{"iso_3166_1": "PS", "nome": "Palestina"},
		{"iso_3166_1": "PT", "nome": "Portogallo"},
		{"iso_3166_1": "PW", "nome": "Palau"},
		{"iso_3166_1": "PY", "nome": "Paraguay"},
		{"iso_3166_1": "QA", "nome": "Qatar"},
		{"iso_3166_1": "RE", "nome": "La Riunione"},
		{"iso_3166_1": "RO", "nome": "Romania"},
		{"iso_3166_1": "RS", "nome": "Serbia"},
		{"iso_3166_1": "RU", "nome": "Russia"},
		{"iso_3166_1": "RW", "nome": "Ruanda"},
		{"iso_3166_1": "SA", "nome": "Arabia Saudita"},
		{"iso_3166_1": "SB", "nome": "Isole Salomone"},
		{"iso_3166_1": "SC", "nome": "Seychelles"},
		{"iso_3166_1": "SD", "nome": "Sudan"},
		{"iso_3166_1": "SE", "nome": "Svezia"},
		{"iso_3166_1": "SG", "nome": "Singapore"},
		{"iso_3166_1": "SH", "nome": "Sant'Elena, Ascensione e Tristan da Cunha"},
		{"iso_3166_1": "SI", "nome": "Slovenia"},
		{"iso_3166_1": "SJ", "nome": "Svalbard e Jan Mayen"},
		{"iso_3166_1": "SK", "nome": "Slovacchia"},
		{"iso_3166_1": "SL", "nome": "Sierra Leone"},
		{"iso_3166_1": "SM", "nome": "San Marino"},
		{"iso_3166_1": "SN", "nome": "Senegal"},
		{"iso_3166_1": "SO", "nome": "Somalia"},
		{"iso_3166_1": "SR", "nome": "Suriname"},
		{"iso_3166_1": "SS", "nome": "Sudan del Sud"},
		{"iso_3166_1": "ST", "nome": "São Tomé e Príncipe"},
		{"iso_3166_1": "SU", "nome": "Unione Sovietica"},
		{"iso_3166_1": "SV", "nome": "El Salvador"},
		{"iso_3166_1": "SX", "nome": "Sint Maarten"},
		{"iso_3166_1": "SY", "nome": "Siria"},
		{"iso_3166_1": "SZ", "nome": "eSwatini"},
		{"iso_3166_1": "TA", "nome": "Tristan da Cunha"},
		{"iso_3166_1": "TC", "nome": "Turks e Caicos"},
		{"iso_3166_1": "TD", "nome": "Ciad"},
		{"iso_3166_1": "TF", "nome": "Terre australi e antartiche francesi"},
		{"iso_3166_1": "TG", "nome": "Togo"},
		{"iso_3166_1": "TH", "nome": "Thailandia"},
		{"iso_3166_1": "TJ", "nome": "Tagikistan"},
		{"iso_3166_1": "TK", "nome": "Tokelau"},
		{"iso_3166_1": "TL", "nome": "Timor Est"},
		{"iso_3166_1": "TM", "nome": "Turkmenistan"},
		{"iso_3166_1": "TN", "nome": "Tunisia"},
		{"iso_3166_1": "TO", "nome": "Tonga"},
		{"iso_3166_1": "TR", "nome": "Turchia"},
		{"iso_3166_1": "TT", "nome": "Trinidad e Tobago"},
		{"iso_3166_1": "TV", "nome": "Tuvalu"},
		{"iso_3166_1": "TW", "nome": "Taiwan"},
		{"iso_3166_1": "TZ", "nome": "Tanzania"},
		{"iso_3166_1": "UA", "nome": "Ucraina"},
		{"iso_3166_1": "UG", "nome": "Uganda"},
		{"iso_3166_1": "UK", "nome": "Regno Unito"},
		{"iso_3166_1": "UM", "nome": "Isole minori esterne degli Stati Uniti"},
		{"iso_3166_1": "US", "nome": "Stati Uniti"},
		{"iso_3166_1": "UY", "nome": "Uruguay"},
		{"iso_3166_1": "UZ", "nome": "Uzbekistan"},
		{"iso_3166_1": "VA", "nome": "Città del Vaticano"},
		{"iso_3166_1": "VC", "nome": "Saint Vincent e Grenadine"},
		{"iso_3166_1": "VE", "nome": "Venezuela"},
		{"iso_3166_1": "VG", "nome": "Isole Vergini britanniche"},
		{"iso_3166_1": "VI", "nome": "Isole Vergini americane"},
		{"iso_3166_1": "VN", "nome": "Vietnam"},
		{"iso_3166_1": "VU", "nome": "Vanuatu"},
		{"iso_3166_1": "WF", "nome": "Wallis e Futuna"},
		{"iso_3166_1": "WS", "nome": "Samoa"},
		{"iso_3166_1": "YE", "nome": "Yemen"},
		{"iso_3166_1": "YT", "nome": "Mayotte"},
		{"iso_3166_1": "ZA", "nome": "Sudafrica"},
		{"iso_3166_1": "ZM", "nome": "Zambia"},
		{"iso_3166_1": "ZW", "nome": "Zimbabwe"}
	]

def db_del(var, nome, engine):
	engine.execute("delete from "+nome+";")

def db_insert(var, nome, engine):
	# pd.DataFrame(var).to_csv("dump_text/"+nome+".csv", index=False)
	pd.DataFrame(var).to_json("dump_text/"+nome+".json", orient="records", indent=2)
	pd.DataFrame(var).to_sql(nome, con=engine, if_exists="append", index=False, chunksize=10000)

def db_insert_all():
	engine=create_engine("mariadb+pymysql://"+db_user+":"+db_pass+"@"+db_host+"/"+db_name)

	db_del(film_partecipazione, "film_partecipazione", engine)
	db_del(film_keyword, "film_keyword", engine)
	db_del(film_compagnia, "film_compagnia", engine)
	db_del(film_paese, "film_paese", engine)
	db_del(film_genere, "film_genere", engine)

	db_del(i_film, "i_film", engine)
	db_del(film, "film", engine)
	db_del(i_collezione, "i_collezione", engine)
	db_del(collezione, "collezione", engine)

	db_del(ruolo, "ruolo", engine)
	db_del(i_persona, "i_persona", engine)
	db_del(persona, "persona", engine)
	db_del(gender, "gender", engine)
	db_del(i_keyword, "i_keyword", engine)
	db_del(keyword, "keyword", engine)
	db_del(i_compagnia, "i_compagnia", engine)
	db_del(compagnia, "compagnia", engine)
	db_del(paese, "paese", engine)
	db_del(i_genere, "i_genere", engine)
	db_del(genere, "genere", engine)

	db_insert(genere, "genere", engine)
	db_insert(i_genere, "i_genere", engine)
	db_insert(paese, "paese", engine)
	db_insert(compagnia, "compagnia", engine)
	db_insert(i_compagnia, "i_compagnia", engine)
	db_insert(keyword, "keyword", engine)
	db_insert(i_keyword, "i_keyword", engine)
	db_insert(gender, "gender", engine)
	db_insert(persona, "persona", engine)
	db_insert(i_persona, "i_persona", engine)
	db_insert(ruolo, "ruolo", engine)

	db_insert(collezione, "collezione", engine)
	db_insert(i_collezione, "i_collezione", engine)
	db_insert(film, "film", engine)
	db_insert(i_film, "i_film", engine)

	db_insert(film_genere, "film_genere", engine)
	db_insert(film_paese, "film_paese", engine)
	db_insert(film_compagnia, "film_compagnia", engine)
	db_insert(film_keyword, "film_keyword", engine)
	db_insert(film_partecipazione, "film_partecipazione", engine)

def translate_keywords():
	global keyword
	tr=GoogleTranslator("en", "it").translate_batch([i["nome"] for i in keyword])
	for i in range(len(keyword)):
		keyword[i]["nome"]=tr[i]

def main():
	if not start(): print("set env variables"); return False

	get_film()

	set_ruolo()

	set_gender()

	set_paese()

	translate_keywords()

	db_insert_all()

if __name__ == "__main__":
	main()
