#!/bin/bash

# Statistiques réseau (paquets envoyés et reçus)
STATS=$(netstat -i)

echo "$STATS"| column -t

