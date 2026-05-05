#!/bin/bash

#-z pour si vide
if [ -z "$1" ]; then
    echo "Erreur: Aucune adresse IP n'est passé en paramètre"
    exit 1
fi

IP=$1

#fonction pour vérifier si une adresse IP est valide
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

ping_latency() {
    if ! valid_ip "$IP"; then
        echo "<div style='color: red;'>⚠️ Erreur : Le format de l'adresse IP est invalide.</div>"
        exit 1
    fi

    #envoyer 4 pqts, extraire la dernière ligne de sortie de ping, prendre sa 4ème colonne (min/avg/max/mdev)
    ping_result=$(ping -c 20 $IP | tail -n 1 | awk '{print $4}')

    #séparer la avec / et extraire les 4 champs
    min=$(echo $ping_result | cut -d '/' -f 1)
    avg=$(echo $ping_result | cut -d '/' -f 2)
    max=$(echo $ping_result | cut -d '/' -f 3)
    mdev=$(echo $ping_result | cut -d '/' -f 4)

    if [ -z "$ping_result" ]; then
        echo "<div style='color: red;'>⚠️ Erreur : Impossible de mesurer la latence. Vérifiez que l'adresse IP est correcte et joignable.</div>"
        exit 1   
    else
        echo "<div style='color: green; text-align: center;'><br>Latence maximale calculée: <strong>$max</strong> ms.</div>"
        echo "<div style='color: green;text-align: center;'>Latence minimale calculée: <strong>$min</strong> ms.</div>"
        echo "<div style='color: green; text-align: center;'>Latence moyenne de <strong>$avg.</strong> ms.</div>"
        echo "<div style='color: green; text-align: center;'>La variance de la latence: <strong>$mdev</strong> ms .</div>"
        echo "<div style='color: grey; font-size: 0.9em; text-align: center;'><br>Nota: Les calculs ont été réalisés sur <strong>20 paquets</strong>.</div>"
    fi
}
  ping_latency
