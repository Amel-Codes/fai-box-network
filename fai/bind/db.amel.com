$TTL 86400
@   IN   SOA  ns1.amel.com. admin.amel.com. (
        2023111201  ; Serial
        3600        ; Refresh
        1800        ; Retry
        1209600     ; Expire
        86400 )     ; Minimum TTL
@   IN   NS    ns1.amel.com.
ns1 IN   A     192.168.2.1  ; L'IP du serveur FAI

; Enregistrement pour le domaine principal
@   IN   A     52.20.84.62  ; Adresse IP publique d'amel.com

; Enregistrements pour le sous-domaine
mail IN A 192.168.1.1

aaaa IN CNAME amel.com.
blog IN A 192.168.1.1
aye IN CNAME amel.com.
malek IN MX 10 mail.amel.com.
aa IN A 192.168.1.1
melissa IN MX 10 mail.amel.com.
lisa IN MX 10 mail.amel.com.
balle IN CNAME amel.com.
loane IN MX 10 mail.amel.com.