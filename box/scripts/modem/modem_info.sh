#!/bin/bash

#adresse MAC du modem
MAC_ADDRESS=$(cat /sys/class/net/eth0/address)

#uptime (durée de connexion)
UPTIME=$(uptime -p)

#version du firmware (exemple de récupération,ajuster selon ta configuration)
FIRMWARE_VERSION="1.0.0"  #exemple statique,à adapter

echo "$MAC_ADDRESS|$UPTIME|$FIRMWARE_VERSION"

