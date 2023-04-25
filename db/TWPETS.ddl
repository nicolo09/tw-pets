-- Database Section
-- ________________ 

create database TWPETS;
use TWPETS;


-- Tables Section
-- _____________ 

create table ANIMALE (
     Username varchar(25) not null,
     Descrizione varchar(100),
     Immagine varchar(200) not null,
     Tipo varchar(30) not null,
     constraint IDANIMALE_ID primary key (Username));

create table COMMENTO (
     ID_commento int not null auto_increment,
     Testo varchar(100) not null,
     Timestamp timestamp not null,
     ID_post int not null,
     ID_padre int,
     Username varchar(25) not null,
     constraint IDCOMMENTO primary key (ID_commento));

create table TENTATIVO_LOGIN (
     Timestamp timestamp not null,
     Username varchar(25) not null,
     constraint IDTENTATIVO_LOGIN primary key (Timestamp, Username));

create table IMPOSTAZIONE (
     Username varchar(25) not null,
     Notifica_like boolean not null,
     Notifica_commento boolean not null,
     Notifica_follow boolean not null,
     Notifica_follow_animale boolean not null,
     constraint FKIMPOSTA_ID primary key (Username));

create table LIKES (
     ID_post int not null,
     Username varchar(25) not null,
     constraint IDLIKES primary key (Username, ID_post));

create table NOTIFICA (
     Letta boolean not null,
     Timestamp timestamp not null,
     ID int not null,
     Destinatario varchar(25) not null,
     constraint IDNOTIFICA primary key (ID));

create table PERSONA (
     Username varchar(25) not null,
     Descrizione varchar(100),
     Immagine varchar(200) not null,
     Email varchar(30) not null,
     Password varchar(30) not null,
     Impiego varchar(20),
     constraint IDPERSONA_ID primary key (Username));

create table POSSIEDE (
     Persona varchar(25) not null,
     Animale varchar(25) not null,
     constraint IDPOSSIEDE primary key (Persona, Animale));

create table POST (
     ID_post int not null auto_increment,
     Immagine varchar(200) not null,
     Testo varchar(100) not null,
     Timestamp timestamp not null,
     Username varchar(25) not null,
     constraint IDPOST primary key (ID_post));

create table RIGUARDA (
     Animale varchar(25) not null,
     ID_post int not null,
     constraint IDRIGUARDA primary key (ID_post, Animale));

create table SALVATI (
     ID_post int not null,
     Username varchar(25) not null,
     constraint IDSALVATI primary key (Username, ID_post));

create table SEGUE_ANIMALE (
     Follower varchar(25) not null,
     Followed varchar(25) not null,
     constraint IDSEGUE_ANIMALE primary key (Followed, Follower));

create table SEGUE_PERSONA (
     Followed varchar(25) not null,
     Follower varchar(25) not null,
     constraint IDSEGUE_PER primary key (Followed, Follower));


-- Constraints Section
-- ___________________ 

-- Not implemented
-- alter table ANIMALE add constraint IDANIMALE_CHK
--     check(exists(select * from POSSIEDE
--                  where POSSIEDE.Animale = Username)); 

alter table COMMENTO add constraint FKSOTTO
     foreign key (ID_post)
     references POST (ID_post);

alter table COMMENTO add constraint FKRISPONDE
     foreign key (ID_padre)
     references COMMENTO (ID_commento);

alter table COMMENTO add constraint FKPOSTA
     foreign key (Username)
     references PERSONA (Username);

alter table TENTATIVO_LOGIN add constraint FKEFFETTUA
     foreign key (Username)
     references PERSONA (Username);

alter table IMPOSTAZIONE add constraint FKIMPOSTA_FK
     foreign key (Username)
     references PERSONA (Username);

alter table LIKES add constraint FKPERSONALIKES
     foreign key (Username)
     references PERSONA (Username);

alter table LIKES add constraint FKPOSTLIKES
     foreign key (ID_post)
     references POST (ID_post);

alter table NOTIFICA add constraint FKPER
     foreign key (Destinatario)
     references PERSONA (Username);

-- Not implemented
-- alter table PERSONA add constraint IDPERSONA_CHK
--     check(exists(select * from IMPOSTAZIONE
--                  where IMPOSTAZIONE.Username = Username)); 

alter table POSSIEDE add constraint FKANIMALEPOSSIEDE
     foreign key (Animale)
     references ANIMALE (Username);

alter table POSSIEDE add constraint FKPERSONAPOSSIEDE
     foreign key (Persona)
     references PERSONA (Username);

alter table POST add constraint FKPUBBLICA
     foreign key (Username)
     references PERSONA (Username);

alter table RIGUARDA add constraint FKPOST
     foreign key (ID_post)
     references POST (ID_post);

alter table RIGUARDA add constraint FKANIMALERIGUARDA
     foreign key (Animale)
     references ANIMALE (Username);

alter table SALVATI add constraint FKPERSONASALVATI
     foreign key (Username)
     references PERSONA (Username);

alter table SALVATI add constraint FKPOSTSALVATI
     foreign key (ID_post)
     references POST (ID_post);

alter table SEGUE_ANIMALE add constraint FKFOLLOWEDANIMALE
     foreign key (Followed)
     references ANIMALE (Username);

alter table SEGUE_ANIMALE add constraint FKFOLLOWERANIMALE
     foreign key (Follower)
     references PERSONA (Username);

alter table SEGUE_PERSONA add constraint FKFOLLOWERPERSONA
     foreign key (Follower)
     references PERSONA (Username);

alter table SEGUE_PERSONA add constraint FKFOLLOWEDPERSONA
     foreign key (Followed)
     references PERSONA (Username);


-- Index Section
-- _____________ 

