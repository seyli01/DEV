Requete 3

select lower(titre)from film where id not between 42 and 84; 

requete 4

select upper(titre) as titre, date_fin_affiche from film order by date_fin_affiche desc ; 

requete 5

select sha1(titre) as 'titre_sha1', md5(titre) as titre_md5 from film as sha; 

requete 6

select count(id) from film where titre like '%b'

requete 7 

select titre from film where genre_id = 2 and lower(titre) like '%the%'

requete 8 

