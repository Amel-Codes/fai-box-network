#!/bin/bash

#### Script de modification du bail actuel (max et default modifiés): prend en paramètre le nouveau bail en secondes #####

DHCP_CONF="/etc/dhcp/dhcpd.conf"

LEASE_TIME=$1

#vérifier si la durée est un entier positif
if [[ ! "$LEASE_TIME" =~ ^[0-9]+$ ]] || [ "$LEASE_TIME" -le 0 ]; then
    echo "Erreur : La durée du bail doit être un entier positif supérieur à zéro."
    exit 1
fi

#la ligne "default-lease-time" 
sudo sed -i "/^default-lease-time/c\default-lease-time $LEASE_TIME;" $DHCP_CONF

#la ligne "max-lease-time"
sudo sed -i "/^max-lease-time/c\max-lease-time $LEASE_TIME;" $DHCP_CONF

sudo systemctl restart isc-dhcp-server

echo "La durée du bail a été modifiée avec succès à $LEASE_TIME secondes."
