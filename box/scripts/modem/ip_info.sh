#!/bin/bash

# Adresse IP privée
PRIVATE_IP=$(hostname -I)

# Adresse IP publique (utilisation de curl pour obtenir l'IP publique)
PUBLIC_IP=$(curl -s ifconfig.me)
#echo "$PRIVATE_IP|$PUBLIC_IP"
IFS=' ' read -r -a PRIVATE_IP_ARRAY <<< "$PRIVATE_IP"
PRIVATE_IP_JOINED=$(echo "${PRIVATE_IP_ARRAY[*]}" | sed 's/ / -- /g')
echo "$PRIVATE_IP_JOINED|$PUBLIC_IP"
