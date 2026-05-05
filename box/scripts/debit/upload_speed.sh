#!/bin/bash

# Adresse IP du serveur FTP FAI
FAI_IP="192.168.2.1"
FTP_USER="stud" 
FTP_PASS="stud"  

# Fichier de test
TEST_FILE="testfile" #fichier de données aléatoire de 100Mo généré à partir du périphérique /dev/urandom.

upload_speed() {
    start_time=$(date +%s) #obtenir l heure actuelle en secondes depuis l'époque Unix (1/1/1970).
    ftp -n $FAI_IP <<EOF #lancer un cl FTP/mode nonInteractif(empêche la cnx automatique)pour mieux contrôler la sqnc d'authentification <<EOF.

user $FTP_USER $FTP_PASS
put $TEST_FILE #envoyer
bye
EOF

end_time=$(date +%s)
elapsed=$((end_time - start_time))

if [ "$elapsed" -gt 0 ]; then
        speed=$(echo "scale=2; 100 / $elapsed" | bc)
        echo "<div style='color: green;text-align: center;'><br>Débit d'envoi estimé : $speed Mo/s.<br> Temps : $elapsed secondes.</div>"
    else
        echo "<div style='color: red;text-align: center;'><br>⚠️ Erreur : L'envoi a échoué. <br> Vérifiez la connexion ou les informations FTP.</div>"
    fi
}

upload_speed
