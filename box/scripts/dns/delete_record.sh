#!/bin/bash

#### Script de suppression d'un enregistrement DNS: prend en paramètres le type de l'enregistrement (A,CNAME, MX) et le nom du ss domaine ####

TYPE=$1        
SUBDOMAIN=$2   

ZONE_FILE="/etc/bind/db.amel.com"  #fichier de zone sur le FAI
FAI_IP="192.168.2.1"              #adresse IP du FAI
SSH_USER="stud"                   #utilisateur SSH sur FAI
SSH_KEY="/var/www/.ssh/id_rsa"    #clé SSH pour la cns sns mdp

delete() {
 sudo -u www-data ssh -i $SSH_KEY $SSH_USER@$FAI_IP "
    # Vérifier si l'enregistrement existe déjà dans le fichier de zone
    if grep -q '^$SUBDOMAIN IN $TYPE' $ZONE_FILE; then
        # Supprimer l'enregistrement de type $TYPE pour le sous-domaine $SUBDOMAIN
        sudo sed -i '/^$SUBDOMAIN IN $TYPE/d' $ZONE_FILE &&
        sudo systemctl restart bind9 &&
        echo "Enregistrement supprimé avec succès."
    else
        echo "Erreur : Aucun enregistrement $TYPE pour le sous-domaine $SUBDOMAIN."
        exit 1
    fi
 "
}

delete
