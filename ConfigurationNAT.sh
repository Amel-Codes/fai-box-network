#!/bin/bash

#### Script pour permttre la connexion à Internet pour le Client grace à la box: Ne prend rien en paramètres#####

#activer l'IP forwarding
echo 1 > /proc/sys/net/ipv4/ip_forward

#configurer iptables pour le NAT
iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE

#sauvegarder les règles iptables
iptables-save > /etc/iptables/rules.v4

echo "NAT configuré avec succès."

