#!/bin/bash

#### Script de gestion DHCP simple: prend en paramètre une taille de plage d'adresses #####

NOMBRE_ADRESSES=$1

# Controle 1
if [ $NOMBRE_ADRESSES -lt 0 ]; then
  echo "Erreur: la plage doit etre positive"
  exit 1
fi

#adresse de départ fixe (celle du routeur est 192.168.1.1)
IP_DEBUT="192.168.1.2"
#extraire son dernier octet et calculer l'octet de fin +(NOMBRE_ADRESSES-1)
OCTET_DEBUT=$(echo $IP_DEBUT | cut -d '.' -f 4)
OCTET_FIN=$((OCTET_DEBUT + NOMBRE_ADRESSES - 1))

# Controle 2
if [ "$OCTET_FIN" -gt 254 ]; then
  echo "Erreur : La taille de la plage dépasse la limite du sous-réseau (192.168.1.0/24)."
  echo "Veuillez saisir une plage ne dépassant pas 253 adresses pour rester dans le sous-réseau."
  exit 1
fi

IP_FIN="192.168.1.$OCTET_FIN"

INTERFACE="eth1"
NETWORK="192.168.1.0"
MASK="255.255.255.0"

# Controle 3
# Vérifier que les adresses IP sont dans le même sous-réseau que l'hôte
DNETWORK=$(ipcalc -n "$IP_DEBUT"/"$CIDR" | grep 'Network' | awk '{print $2}' | cut -d'/' -f1)
FNETWORK=$(ipcalc -n "$IP_FIN"/"$CIDR" | grep 'Network' | awk '{print $2}' | cut -d'/' -f1)
if [[ "$FNETWORK" != "$NETWORK" || "$DNETWORK" != "$NETWORK" ]]; then
    echo "Erreur: Les adresses IP ne sont pas dans le même sous-réseau."
    exit 1
fi

# Controle 4
# Vérifier si la configuration existe déjà (on ne permet qu'une seule configuration)
if grep -q "subnet $NETWORK netmask $MASK" "/etc/dhcp/dhcpd.conf"; then
    echo "Erreur: Une configuration DHCP est déjà en place. Aucune modification n’a été effectuée."
    echo "Veuillez d’abord supprimer l’ancienne configuration avant d’en ajouter une nouvelle."
    exit 1
fi

# Ajouter la nouvelle configuration DHCP à la fin du fichier

cat <<EOF >> /etc/dhcp/dhcpd.conf
# Nouvelle configuration DHCP ajoutée avec $NOMBRE_ADRESSES adresses
option subnet-mask 255.255.255.0;
option broadcast-address 192.168.1.255; #Les paqts envoyés à cette @ sont reçus par ttes les hotes du réseau
option routers 192.168.1.1;  
option domain-name-servers 192.168.1.1;  # Pour que les clients désignés avec une @DHCP puissent résoudre les noms de domaines

subnet 192.168.1.0 netmask 255.255.255.0 { 
    range $IP_DEBUT $IP_FIN;
}
EOF

systemctl restart isc-dhcp-server

echo "Une plage DHCP de $NOMBRE_ADRESSES adresses a été configurée avec succès."

