#!/bin/bash

#### Script pour détecter les appareils connectés à la box : ne prend rien en paarmètre #####
# Réseau à scanner (adapté à votre réseau local)
NETWORK="192.168.1.0/24"
INTERFACE="eth1"

RESULT=$(arp-scan --interface=$INTERFACE --localnet | awk '/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/ {print $1, $2, $3}')

# Générer une sortie propre pour HTML
if [ -z "$RESULT" ]; then
    echo '<p style="text-align: center; font-size: 1.2rem; color: #333; position: absolute; top: 70%; left: 50%; transform: translateX(-50%);">Aucun appareil connecté détecté sur le réseau.</p>';
else
    echo "<style>"
    echo "table {"
    echo "    border-collapse: collapse;"
    echo "    width: 60%;"
    echo "    margin: 40px auto;"  #centrer le tableau
    echo "    text-align: left;"
    echo "}"
    echo "th, td {"
    echo "    padding: 8px;"
    echo "    border: 1px solid #ddd;"
    echo "}"
    echo "th {"
    echo "    background-color: #f2f2f2;"
    echo "}"
    echo "tr:nth-child(even) {"
    echo "    background-color: #f9f9f9;"
    echo "}"
    echo "td:nth-child(1) {"
    echo "width: 210px;"  #limiter la largeur de la colonne de l'adresse MAC
    echo "}"
    echo "td:nth-child(2) {"
    echo "width: 230px;"  #limiter la largeur de la colonne de l'adresse MAC
    echo "}"
    echo "td:nth-child(3) {"
    echo "width: 230px;"  #limiter la largeur de la colonne de l'adresse MAC
    echo "}"
    echo "</style>"

    echo "<table>"
    echo "<tr><th>IP</th><th>MAC</th><th>Fabricant</th></tr>"
    echo "$RESULT" | while read -r line; do
        IP=$(echo "$line" | awk '{print $1}')
        MAC=$(echo "$line" | awk '{print $2}')
        MANUFACTURER=$(echo "$line" | awk '{for(i=3;i<=NF;i++) printf $i " ";}')
        echo "<tr><td>$IP</td><td>$MAC</td><td>$MANUFACTURER</td></tr>"
    done
    echo "</table>"
fi
