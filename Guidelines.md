#GUIDELINE

##GIT

Les commits seront réalisés en anglais.

##Code

Le code sera écrit en suivant la convention de nommage CamelCase en anglais.
Les commentaires seront écrits en français.

###Organisation

Le code sera organisé par type : la logique dans des services (Service), l'interface exposée (Controller), les objets (Entity), les bases de données (Database) et les vues (View) seront ainsi séparés. Un autre dossier contiendra les autres types de fichiers. 

####Ex:

-src
	-Controller
		-Home
		-SecondPage
	-Entity
		-Object1
		-Object2
	-View
 		-Home
		-SecondPage
	-Database
		-Livres
	-Others


