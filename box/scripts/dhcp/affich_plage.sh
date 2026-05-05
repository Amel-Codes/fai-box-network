#!/bin/bash

# Emplacement du fichier de configuration DHCP
DHCP_CONFIG="/etc/dhcp/dhcpd.conf"

RANGE=$(grep -v '^#' "$DHCP_CONFIG" | grep -oP 'range\s+\K([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\s+[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)')
    
    if [ -n "$RANGE" ]; then
        echo "Plage DHCP actuelle : $RANGE"
    else
        echo "Aucune plage DHCP définie."
    fi
