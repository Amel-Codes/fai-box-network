# Infrastructure Réseau Virtualisée — Simulation FAI / Box / Client

> Projet académique — Licence Informatique, Université d'Avignon  
> Amel Sekouane · 2024

---

## Présentation

Simulation complète d'une infrastructure réseau en 3 machines virtuelles :

```
[Client VM] ←──eth1──→ [Box VM] ←──eth2──→ [FAI VM] ←──eth0──→ Internet
 192.168.1.4           192.168.1.1          192.168.2.1
                       192.168.2.2
```

La **Box** joue le rôle d'un routeur/gateway grand public avec une interface d'administration web complète. Le **FAI** héberge un serveur DNS autoritaire (BIND9) et un serveur FTP pour les tests de débit. Le **Client** accède à Internet via le NAT configuré sur la Box.

---

## Architecture

```
repo/
├── box/
│   ├── config/
│   │   ├── interfaces          # Config réseau Box (eth0/eth1/eth2)
│   │   ├── named.conf.local    # Zone DNS esclave (amel.com)
│   │   └── named.conf.options  # Options BIND9 — résolution récursive LAN
│   ├── scripts/
│   │   ├── ConfigurationNAT.sh         # Active IP forwarding + iptables MASQUERADE
│   │   ├── connected_devices.sh        # Scan ARP du réseau local (arp-scan)
│   │   ├── dhcp/
│   │   │   ├── dhcp_clients.sh         # Configure une plage DHCP (param: nb adresses)
│   │   │   ├── affich_plage.sh         # Affiche la plage DHCP actuelle
│   │   │   ├── modif_bail.sh           # Modifie la durée de bail (param: secondes)
│   │   │   └── delete_config_dhcp.sh   # Supprime la config DHCP en place
│   │   ├── dns/
│   │   │   ├── add_record.sh           # Ajoute un enregistrement DNS (A/CNAME/MX) via SSH FAI
│   │   │   ├── delete_record.sh        # Supprime un enregistrement DNS via SSH FAI
│   │   │   └── list_records.sh         # Liste les enregistrements du fichier de zone
│   │   ├── debit/
│   │   │   ├── download_speed.sh       # Mesure le débit descendant via FTP
│   │   │   ├── upload_speed.sh         # Mesure le débit montant via FTP
│   │   │   └── ping_latency.sh         # Mesure la latence vers une IP (param: IP)
│   │   └── modem/
│   │       ├── ip_info.sh              # IP privées + IP publique (curl ifconfig.me)
│   │       ├── modem_info.sh           # MAC, uptime, version firmware
│   │       └── network_stats.sh        # Statistiques réseau (netstat -i)
│   └── web/                            # Interface d'administration web (Apache + PHP)
│       ├── index.html                  # Page d'accueil — tableau de bord
│       ├── dhcp.php                    # Gestion DHCP (plage, bail, start/stop)
│       ├── dns.php                     # Gestion DNS (ajout/suppression enregistrements)
│       ├── debit.php                   # Tests de débit et latence
│       ├── connected_devices.php       # Appareils connectés sur le LAN
│       ├── modem_info.php              # Infos modem + historique des actions (MySQL)
│       ├── parametres.php              # Paramètres avancés
│       └── saveBdd.php                 # Endpoint interne — log des actions en BDD
│
├── fai/
│   ├── config/
│   │   └── interfaces          # Config réseau FAI (eth0 DHCP / eth1 statique)
│   └── bind/
│       ├── named.conf.local    # Zone DNS maître (amel.com)
│       ├── named.conf.options  # Options BIND9 — transfert de zone vers Box
│       └── db.amel.com         # Fichier de zone DNS — enregistrements A/CNAME/MX
│
├── client/
│   └── config/
│       └── interfaces          # Config réseau Client (eth0 DHCP / eth1 statique)
│
├── docs/
│   └── Rapport_AMS_Reseaux.pdf # Rapport complet du projet
│
└── README.md
```

---

## Fonctionnalités

### Interface web (Box)
| Page | Fonctionnalité |
|---|---|
| `index.html` | Tableau de bord général |
| `dhcp.php` | Configurer plage, modifier bail, start/stop service |
| `dns.php` | Ajouter / supprimer des enregistrements DNS (A, CNAME, MX) |
| `debit.php` | Tester débit download/upload et latence ping |
| `connected_devices.php` | Lister les appareils connectés via ARP scan |
| `modem_info.php` | Infos modem, stats réseau, historique des actions |

### Scripts Bash
- **NAT** : activation de l'IP forwarding et règle `MASQUERADE` iptables
- **DHCP** : gestion complète de la plage (ajout, suppression, modification bail)
- **DNS** : interactions avec le serveur FAI via SSH sans mot de passe
- **Débit** : mesures FTP upload/download + ping latency sur 20 paquets
- **Modem** : récupération infos système (MAC, uptime, IPs, stats réseau)

### DNS maître/esclave
- Le **FAI** est le serveur DNS maître pour `amel.com`
- La **Box** est configurée en esclave — elle récupère la zone par transfert
- Les scripts DNS de la Box se connectent en SSH au FAI pour modifier `db.amel.com`

---

## Prérequis (par VM)

### Box
```bash
apt install isc-dhcp-server bind9 arp-scan apache2 php libapache2-mod-php php-mysqli iptables-persistent mysql-server
```

### FAI
```bash
apt install bind9 vsftpd openssh-server
```

### Client
```bash
# Aucun service spécifique requis
```

---

## Installation

### 1. Cloner le repo
```bash
git clone https://github.com/Amel-Codes/fai-box-network.git
cd fai-box-network
```

### 2. Configurer la Box
```bash
# Réseau
sudo cp box/config/interfaces /etc/network/interfaces
sudo systemctl restart networking

# BIND9
sudo cp box/config/named.conf.local /etc/bind/named.conf.local
sudo cp box/config/named.conf.options /etc/bind/named.conf.options
sudo systemctl restart bind9

# Scripts
sudo cp -r box/scripts/* /home/stud/
sudo chmod +x /home/stud/**/*.sh

# Interface web
sudo cp -r box/web/* /var/www/html/

# NAT
sudo bash box/scripts/ConfigurationNAT.sh
```

### 3. Configurer le FAI
```bash
sudo cp fai/config/interfaces /etc/network/interfaces
sudo cp fai/bind/named.conf.local /etc/bind/named.conf.local
sudo cp fai/bind/named.conf.options /etc/bind/named.conf.options
sudo cp fai/bind/db.amel.com /etc/bind/db.amel.com
sudo systemctl restart bind9
```

### 4. Configurer le Client
```bash
sudo cp client/config/interfaces /etc/network/interfaces
sudo systemctl restart networking
```

### 5. Base de données (Box)
```sql
CREATE DATABASE projet_reseaux;
CREATE USER 'Amel'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON projet_reseaux.* TO 'Amel'@'localhost';

USE projet_reseaux;
CREATE TABLE historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_type VARCHAR(100),
    action_details TEXT,
    user VARCHAR(100),
    action_time DATETIME
);
```

### 6. SSH sans mot de passe Box → FAI
```bash
# Sur la Box, en tant que www-data
sudo -u www-data ssh-keygen -t rsa -f /var/www/.ssh/id_rsa -N ""
sudo -u www-data ssh-copy-id -i /var/www/.ssh/id_rsa.pub stud@192.168.2.1
```

---

## ⚠️ Sécurité — Points importants

> Ce projet a été réalisé en environnement virtualisé isolé. Avant tout déploiement en production :

- **Credentials BDD** dans `saveBdd.php` et `modem_info.php` : remplacer `"stud"` par un mot de passe fort et l'externaliser dans un fichier de config non versionné
- **`www-data ALL=(ALL) NOPASSWD: ALL`** dans sudoers : à restreindre aux seules commandes nécessaires
- **Clés SSH** : ne jamais committer les clés privées (`/var/www/.ssh/id_rsa`)
- **FTP credentials** en clair dans les scripts de débit : à remplacer par une authentification par clé ou un `.netrc` chiffré
- **`shell_exec()` dans PHP** sans sanitisation complète des inputs : à sécuriser en production

---

## Technologies

`Bash` · `PHP` · `HTML/CSS` · `MySQL` · `BIND9` · `ISC DHCP` · `iptables` · `arp-scan` · `Apache2` · `Linux (Debian)` · `VirtualBox`

---

## Auteur

**Amel Sekouane** — [github.com/Amel-Codes](https://github.com/Amel-Codes) · [LinkedIn](https://linkedin.com/in/amel-sekouane-b2897a254)
