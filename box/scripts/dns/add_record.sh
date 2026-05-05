#!/bin/bash

#### Script d'ajout d'un sous domaine DNS: prend en paramètres le type de l'enregistrement (A,CNAME, MX) et le nom du ss domaine#####

TYPE=$1                            # type d'enregistrement(A, CNAME,ou MX)
SUBDOMAIN=$2                       # nom du ss domain
ZONE_FILE="/etc/bind/db.amel.com"  #sur le serveur FAI
FAI_IP="192.168.2.1" 
BOX_IP="192.168.1.1"  

#interdire les ss domaine: de système ou services réservés /susceptibles d'être utilisés à des fins malveillantes/ internes / réservés 
forbiddens=("www" "mail" "ftp" "smtp" "dns" "ns" "amel" "mx" "admin" "cpanel" "login" "secure" "root" "webmail" "dev" "staging" "test" "local" "prod" "example" "localhost" "invalid")
for forbidden in "${forbiddens[@]}"; do
    if [ "$SUBDOMAIN" == "$forbidden" ]; then
        echo "Erreur : Le sous-domaine ou alias '$SUBDOMAIN' est interdit."
        exit 1
    fi
done

#connexion SSH et ajout de l'enregistrement sur le serveur FAI
add_record() {
 #lancer une cnx SSH  au FAI avec une clé privée(pour une authentification sans mdp) en tant qu'user www-data
 sudo -u www-data ssh -i /var/www/.ssh/id_rsa stud@$FAI_IP "
    #recherch en quiet des lignes commençant par  $SUBDOMAIN  IN $TYPE  pour ne pas faire des doublons
    if grep -q '^$SUBDOMAIN IN' $ZONE_FILE; then
        echo 'Erreur: Le sous-domaine $SUBDOMAIN de type $TYPE existe déjà dans $ZONE_FILE.' 
    else
        case "$TYPE" in
             A)
                 echo '$SUBDOMAIN IN $TYPE 192.168.1.1' | sudo tee -a $ZONE_FILE > /dev/null &&
                 sudo systemctl restart bind9 &&
                 echo 'Enregistrement de type $TYPE pour $SUBDOMAIN ajouté avec succès'
             ;;
             CNAME)
                 echo '$SUBDOMAIN IN CNAME "amel.com."' | sudo tee -a $ZONE_FILE > /dev/null &&
                 sudo systemctl restart bind9 &&
                 echo 'Enregistrement pour $SUBDOMAIN vers amel.com ajouté avec succès'
             ;;
             MX)
                 echo '$SUBDOMAIN IN MX 10 mail.amel.com.' | sudo tee -a $ZONE_FILE > /dev/null &&
                 sudo systemctl restart bind9 &&
                 echo "Enregistrement réussi. Les e-mails envoyés à $SUBDOMAIN@amel.com seront traités par notre serveur mail.amel.com."
             ;;
             *)
                 echo 'Erreur: Type non supporté. Utilise A, CNAME ou MX.'
                 exit 1
             ;;
        esac
    fi
 "
    if [ $? -ne 0 ]; then
        echo "Erreur: Une erreur est survenue."
    fi
}

add_record
