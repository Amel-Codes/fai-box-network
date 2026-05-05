#!/bin/bash

#### Script pour afficher les informations sur les sous domaine et leurs significations simplifiées: Ne prend rien en paramètres#####

ZONE_FILE="/etc/bind/db.amel.com" #fichier de zone
FAI_IP="192.168.2.1"              #adresse IP du FAI
SSH_USER="stud"                   #utilisateur SSH sur FAI
SSH_KEY="/var/www/.ssh/id_rsa"    #clé SSH pour la cnx sns mdp

ssh -i "$SSH_KEY" "$SSH_USER@$FAI_IP" "cat $ZONE_FILE" | \
    awk '/; Enregistrements pour le sous-domaine/ {found=1} found && /IN\s+(A|MX|CNAME)/ {print}' | \
    while read -r line; do
        # Ignorer les lignes vides ou les commentaires (lignes commençant par ;)
        if [[ "$line" == \;* ]] || [[ -z "$line" ]]; then
            continue
        fi
        # Extraire les informations du ss domaine, du type et de la valeur
        subdomain=$(echo "$line" | awk '{print $1}')
        type=$(echo "$line" | awk '{print $3}')
        value=$(echo "$line" | awk '{print $4}')

        # Afficher les résultats avec des explications pour chaque type
        if [[ "$type" == "A" ]]; then
          echo "  $subdomain → Adresse IP : $value"
        elif [[ "$type" == "MX" ]]; then
         priority=$(echo "$line" | awk '{print $4}')
         mail_server=$(echo "$line" | awk '{print $5}')
         echo "  $subdomain → Serveur mail (Priorité : $priority) : $mail_server"
        elif [[ "$type" == "CNAME" ]]; then
         echo "  $subdomain → Alias : $value"
        fi 
        echo "<br>"
done
