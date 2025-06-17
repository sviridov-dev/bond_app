## Prerequisites

* VPS with Ubuntu 22.04+
* SSH access
* Domain name (optional but recommended)
* Root or sudo privileges

---

## 1. Initial Server Setup

```bash
ssh root@your_server_ip
```

### Update system

```bash
apt update && apt upgrade -y
```

### Enable UFW (firewall)

```bash
ufw allow OpenSSH
ufw enable
```

---

## 2. Install Nginx

```bash
apt install nginx -y
```

### Allow HTTP/HTTPS

```bash
ufw allow 'Nginx Full'
```

Test Nginx:

```bash
systemctl status nginx
```
---

## 3. Install PHP 8.4 + Extensions

### Add PHP 8.4 repository

```bash
add-apt-repository ppa:ondrej/php -y
apt update
```

### Install PHP-FPM and common extensions

```bash
apt install php8.4-fpm php8.4-cli php8.4-pgsql php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath unzip curl -y
```

Check PHP version:

```bash
php -v
```

Ensure PHP-FPM is running:

```bash
systemctl status php8.4-fpm
```

---

## 4. Install PostgreSQL 17

```bash
sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
wget -qO - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
apt update
apt install postgresql-17 -y
```

### Secure PostgreSQL

```bash
sudo -i -u postgres
psql
```

```sql
ALTER USER postgres PASSWORD 'your_strong_password';
\q
exit
```

---

## 5. Configure Nginx with PHP-FPM

### Example server block: `/etc/nginx/sites-available/yourapp`

```nginx
server {
    listen 80;
    server_name yourdomain.com;

    root /var/www/yourapp/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }

    access_log /var/log/nginx/yourapp.access.log;
    error_log /var/log/nginx/yourapp.error.log;
}
```

```bash
ln -s /etc/nginx/sites-available/yourapp /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

---

## 6. Install Composer (Laravel packages)

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

---

## 7. Install Supervisor

```bash
apt install supervisor -y
```

### Create Laravel queue worker (example):

`/etc/supervisor/conf.d/laravel-worker.conf`

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/yourapp/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/yourapp/storage/logs/worker.log
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:*
```

---

## 8. SSL (Optional, via Let's Encrypt)

```bash
apt install certbot python3-certbot-nginx -y
certbot --nginx -d yourdomain.com
```

Auto-renewal:

```bash
systemctl status certbot.timer
```

---

## 9. Permissions

```bash
chown -R www-data:www-data /var/www/yourapp
chmod -R 755 /var/www/yourapp
```

---

## 10. Verify Everything

* Nginx responds to your domain or IP.
* PHP info file (optional):
  `/var/www/yourapp/public/info.php`

  ```php
  <?php phpinfo(); ?>
  ```
* Supervisor keeps queue worker running
* Database connection works (`psql` or Laravel)

---

