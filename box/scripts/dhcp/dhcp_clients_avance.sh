#!/bin/bash

#### Script de gestion DHCP avancée: prend en paramètre l'adr IP de début et celle de fin de plage #####

#fonction pour vérifier si une adresse IP est valide (4 champs de 3 entiers chacun non négatifs, ne dépassant pas 255)
function valid_ip() {
    local ip=$1
    local stat=1
    if [[ $ip =~ ^([0-9]{1,3}\.){3}[0-9]{1,3}$ ]]; then
        OIFS=$IFS
        IFS='.'
        ip=($ip)
        IFS=$OIFS
        [[ ${ip[0]} -le 255 && ${ip[1]} -le 255 && ${ip[2]} -le 255 && ${ip[3]} -le 255 ]]
        stat=$?
    fi
    return $stat
}

#fonction pour convertir une adresse IP en entier pour la comparaison entre 2 IP (début < fin)
function ip_to_int() {
    local ip=$1
    local a b c d
    IFS=. read -r a b c d <<< "$ip"
    echo "$(((a << 24) + (b << 16) + (c << 8) + d))"
}

IP_DEBUT=$1
IP_FIN=$2

#Controle 1
#vérification que les adresses IP sont valides
if ! valid_ip "$IP_DEBUT"; then
    echo "Erreur : L'adresse IP de début n'est pas valide."
    exit 1
fi
if ! valid_ip "$IP_FIN"; then
    echo "Erreur : L'adresse IP de fin n'est pas valide."
    exit 1
fi

#Controle 2
# Vérification de l'adresse de début
if [[ "$IP_DEBUT" == "192.168.1.1" ]]; then
    echo "Erreur : l'adresse de début ne doit pas être l'adresse du routeur (192.168.1.1)."
    exit 1
fi
# Vérification de l'adresse de fin
if [[ "$IP_FIN" == "192.168.1.255" ]]; then
    echo "Erreur : l'adresse de fin ne doit pas être l'adresse de broadcast (192.168.1.255)."
    exit 1
fi

#Controle 3
#vérifier que IP_DEBUT est inférieur à IP_FIN
IFS='.' read -r D1 D2 D3 D4 <<< "$IP_DEBUT"
IFS='.' read -r F1 F2 F3 F4 <<< "$IP_FIN"
if (( D1 > F1 || (D1 == F1 && D2 > F2) || (D1 == F1 && D2 == F2 && D3 > F3) || (D1 == F1 && D2 == F2 && D3 == F3 && D4 >= F4) )); then
    echo "Erreur: L'adresse IP de début doit être inférieure à l'adresse de fin."
    exit 1
fi

INTERFACE="eth1"
MASK="255.255.255.0"
NETWORK="192.168.1.0"

#Controle 4
#vérifier que les adresses IP sont dans le même sous-réseau que l'hôte
DNETWORK=$(ipcalc -n "$IP_DEBUT"/"$CIDR" | grep 'Network' | awk '{print $2}' | cut -d'/' -f1) 
FNETWORK=$(ipcalc -n "$IP_FIN"/"$CIDR" | grep 'Network' | awk '{print $2}' | cut -d'/' -f1)
if [[ "$FNETWORK" != "$NETWORK" || "$DNETWORK" != "$NETWORK" ]]; then
    echo "Erreur: Les adresses IP fournies ne sont pas dans le même sous-réseau."
    exit 1
fi

#Controle 5 
#vérifier si la configuration existe déjà(on ne permet qu'une seule configuration)
if grep -q "subnet $NETWORK netmask $MASK" "/etc/dhcp/dhcpd.conf"; then
    echo "Erreur: Une configuration DHCP existe déjà. Aucune modification apportée."
    exit 0
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

echo "Configuration DHCP ajoutée avec succès à avec une plage de $NOMBRE_ADRESSES adresses : $IP_DEBUT - $IP_FIN"

