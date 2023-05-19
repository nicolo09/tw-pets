-- Database Section
-- ________________ 

create database TWPETS;
use TWPETS;


-- Tables Section
-- _____________ 

create table ANIMALE (
     username varchar(25) not null,
     descrizione varchar(100),
     immagine varchar(200) not null,
     tipo varchar(30) not null,
     constraint IDANIMALE_ID primary key (username));

create table COMMENTO (
     id_commento int not null auto_increment,
     testo varchar(200) not null,
     timestamp timestamp not null,
     id_post int not null,
     id_padre int,
     username varchar(25) not null,
     constraint IDCOMMENTO primary key (id_commento));

create table TENTATIVO_LOGIN (
     timestamp timestamp not null,
     username varchar(25) not null,
     constraint IDTENTATIVO_LOGIN primary key (timestamp, username));

create table IMPOSTAZIONE (
     username varchar(25) not null,
     `alert-new-access` boolean not null default true,
     `alert-change-password` boolean not null default true,
     `alert-likes` boolean not null default true,
     `alert-comments` boolean not null default true,
     `alert-new-post-person` boolean not null default false,
     `alert-new-post-animal` boolean not null default false,
     constraint FKIMPOSTA_ID primary key (username));

create table LIKES (
     id_post int not null,
     username varchar(25) not null,
     constraint IDLIKES primary key (username, id_post));

create table NOTIFICA (
     letta boolean not null,
     timestamp timestamp not null,
     id int not null auto_increment,
     destinatario varchar(25) not null,
     tipo varchar(25) not null,
     origine JSON not null,
     constraint IDNOTIFICA primary key (id));

create table PERSONA (
     username varchar(25) not null,
     descrizione varchar(100),
     immagine varchar(200) not null default "default.jpg",
     email varchar(30) not null,
     password char(255) not null,
     impiego varchar(20),
     constraint IDPERSONA_ID primary key (username));

create table POSSIEDE (
     persona varchar(25) not null,
     animale varchar(25) not null,
     constraint IDPOSSIEDE primary key (persona, animale));

create table POST (
     id_post int not null auto_increment,
     immagine varchar(200) not null,
	 alt varchar(50) not null,
     testo varchar(200) not null,
     timestamp timestamp not null,
     username varchar(25) not null,
     constraint IDPOST primary key (id_post));

create table RIGUARDA (
     animale varchar(25) not null,
     id_post int not null,
     constraint IDRIGUARDA primary key (id_post, animale));

create table SALVATI (
     id_post int not null,
     username varchar(25) not null,
     constraint IDSALVATI primary key (username, id_post));

create table SEGUE_ANIMALE (
     follower varchar(25) not null,
     followed varchar(25) not null,
     constraint IDSEGUE_ANIMALE primary key (followed, follower));

create table SEGUE_PERSONA (
     followed varchar(25) not null,
     follower varchar(25) not null,
     constraint IDSEGUE_PER primary key (followed, follower));


-- Constraints Section
-- ___________________ 

-- Not implemented
-- alter table ANIMALE add constraint IDANIMALE_CHK
--     check(exists(select * from POSSIEDE
--                  where POSSIEDE.animale = username)); 

alter table COMMENTO add constraint FKSOTTO
     foreign key (id_post)
     references POST (id_post);

alter table COMMENTO add constraint FKRISPONDE
     foreign key (id_padre)
     references COMMENTO (id_commento);

alter table COMMENTO add constraint FKPOSTA
     foreign key (username)
     references PERSONA (username);

alter table TENTATIVO_LOGIN add constraint FKEFFETTUA
     foreign key (username)
     references PERSONA (username);

alter table IMPOSTAZIONE add constraint FKIMPOSTA_FK
     foreign key (username)
     references PERSONA (username)
     ON DELETE CASCADE;

alter table LIKES add constraint FKPERSONALIKES
     foreign key (username)
     references PERSONA (username);

alter table LIKES add constraint FKPOSTLIKES
     foreign key (id_post)
     references POST (id_post);

alter table NOTIFICA add constraint FKPER
     foreign key (destinatario)
     references PERSONA (username);

-- Not implemented
-- alter table PERSONA add constraint IDPERSONA_CHK
--     check(exists(select * from IMPOSTAZIONE
--                  where IMPOSTAZIONE.username = username)); 

alter table POSSIEDE add constraint FKANIMALEPOSSIEDE
     foreign key (animale)
     references ANIMALE (username);

alter table POSSIEDE add constraint FKPERSONAPOSSIEDE
     foreign key (persona)
     references PERSONA (username);

alter table POST add constraint FKPUBBLICA
     foreign key (username)
     references PERSONA (username);

alter table RIGUARDA add constraint FKPOST
     foreign key (id_post)
     references POST (id_post);

alter table RIGUARDA add constraint FKANIMALERIGUARDA
     foreign key (animale)
     references ANIMALE (username);

alter table SALVATI add constraint FKPERSONASALVATI
     foreign key (username)
     references PERSONA (username);

alter table SALVATI add constraint FKPOSTSALVATI
     foreign key (id_post)
     references POST (id_post);

alter table SEGUE_ANIMALE add constraint FKFOLLOWEDANIMALE
     foreign key (followed)
     references ANIMALE (username);

alter table SEGUE_ANIMALE add constraint FKFOLLOWERANIMALE
     foreign key (follower)
     references PERSONA (username);

alter table SEGUE_PERSONA add constraint FKFOLLOWERPERSONA
     foreign key (follower)
     references PERSONA (username);

alter table SEGUE_PERSONA add constraint FKFOLLOWEDPERSONA
     foreign key (followed)
     references PERSONA (username);


-- Index Section
-- _____________ 

