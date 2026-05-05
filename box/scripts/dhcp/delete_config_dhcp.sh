#!/bin/bash

#### Script de suppression complète de la configuration DHCP actuelle: Ne prend aucun paramètre #####

DHCP_CONF="/etc/dhcp/dhcpd.conf"

#La configuration commence toujours par la phrase 'Nouvelle Conf....'
sed -i '/# Nouvelle configuration DHCP/,/}/d' "$DHCP_CONF"
systemctl restart isc-dhcp-server

echo "La configuration DHCP a été supprimée avec succès."
